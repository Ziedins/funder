<?php

namespace App\Controller;

use App\Client\ExchangeRateClient;
use App\Entity\Customer;
use App\Repository\AccountRepository;
use App\Service\AccountTransferService;
use App\Service\DummyService;
use App\Service\ExchangeRateService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    private ExchangeRateService $exchangeRateService;
    private AccountTransferService $accountTransferService;
    private EntityManagerInterface $entityManager;
    private DummyService $dummyService;
    private AccountRepository $accountRepository;
    private ExchangeRateClient $exchangeRateClient;

    public function __construct(
        ExchangeRateService    $exchangeRateService,
        AccountTransferService $accountTransferService,
        EntityManagerInterface $entityManager,
        DummyService $dummyService,
        AccountRepository $accountRepository,
        ExchangeRateClient $exchangeRateClient
    ) {
        $this->exchangeRateService = $exchangeRateService;
        $this->accountTransferService = $accountTransferService;
        $this->entityManager = $entityManager;
        $this->dummyService = $dummyService;
        $this->accountRepository = $accountRepository;
        $this->exchangeRateClient = $exchangeRateClient;
    }

    /**
     * @throws \Exception
     */
    #[Route('/test', name: 'app_test')]
    public function index(): JsonResponse
    {
        $this->dummyService->AddJohnDoe();


    }
}
