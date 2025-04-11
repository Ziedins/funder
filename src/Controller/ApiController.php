<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Customer;
use App\Repository\AccountRepository;
use App\Repository\CustomerRepository;
use App\Repository\TransactionRepository;
use App\Service\AccountTransferService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ApiController extends AbstractController
{

    #[Route('/accounts/{customerId}', name: 'accounts')]
    public function accounts(int $customerId, CustomerRepository $customerRepository): JsonResponse
    {
        $customer = $customerRepository->findOneBy(['id' => $customerId]);

        if (!$customer) {
            throw $this->createNotFoundException('Customer not found');
        }

        $accounts = $customer->getAccounts()->toArray();
        $accounts = array_map('self::getAccountArray', $accounts);

        return new JsonResponse([
            'customer' => $this->getCustomerArray($customer),
            'accounts' => $accounts,
        ]);
    }

    #[Route('/transactions/{accountId}', name: 'transactions')]
    public function transactions(Request $request, int $accountId, TransactionRepository $transactionRepository): JsonResponse
    {
        $limit = (int)$request->query->get('limit', 10);
        $offset = (int)$request->query->get('offset', 0);

        $transactions = $transactionRepository->getTransactionsBySource($accountId, $limit, $offset);

        $data = array_map(function ($transaction) {
            return [
                'id' => $transaction->getId(),
                'amount' => $transaction->getAmount(),
                'target' => $transaction->getTargetAccount()->getId(),
                'createdAt' => $transaction->getCreatedAt()->format('Y-m-d H:i:s'),
                // Add any other fields you want
            ];
        }, $transactions);
        return new JsonResponse($data);
    }

    #[Route('/transfer', name: 'transfer', methods: ['POST'])]
    public function transfer(
        Request                $request,
        AccountTransferService $accountTransferService,
        AccountRepository $accountRepository,
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['sourceAccountId'], $data['targetAccountId'], $data['amount'])) {
            return new JsonResponse(['error' => 'Missing required fields: sourceAccountId, targetAccountId, amount'], 400);
        }

        $sourceId = (int)$data['sourceAccountId'];
        $targetId = (int)$data['targetAccountId'];
        $amount = (float)$data['amount'];

        if ($amount <= 0) {
            return new JsonResponse(['error' => 'Amount must be greater than zero'], 400);
        }

        $sourceAccount = $accountRepository->findOneBy(["id" => $sourceId]);
        $targetAccount = $accountRepository->findOneBy(["id" => $targetId]);

        if (!$sourceAccount || !$targetAccount) {
            return new JsonResponse(['error' => "One or both accounts don't exist"], 404);
        }

        try {
            $accountTransferService->transferBetweenAccounts($sourceAccount, $targetAccount, $amount);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Transfer error occurred: '. $e->getMessage()], 400);
        }

        return new JsonResponse([
            'success' => true,
            'sourceAccount' => $sourceAccount->getId(),
            'targetAccount' => $targetAccount->getId(),
            'amount' => $amount
        ]);
    }

    private function getCustomerArray(Customer $customer): array
    {
        return [
            'id' => $customer->getId(),
            'name' => $customer->getName(),
        ];
    }

    private function getAccountArray(Account $account): array
    {
        return [
            'id' => $account->getId(),
            'customerId' => $account->getCustomer()->getId(),
            'currency' => $account->getCurrency()->getName(),
        ];
    }
}
