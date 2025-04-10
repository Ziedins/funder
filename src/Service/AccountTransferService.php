<?php

namespace App\Service;

use App\Entity\Account;
use App\Repository\ExchangeRateRepository;
use Doctrine\ORM\EntityManagerInterface;

class AccountTransferService
{
    private ExchangeRateRepository $exchangeRateRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ExchangeRateRepository $exchangeRateRepository, EntityManagerInterface $entityManager)
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws \Exception
     */
    public function transferBetweenAccounts(Account $sourceAccount, Account $targetAccount, float $transferAmount): void
    {
        if ($sourceAccount->getBalance() < $transferAmount) {
            throw new \Exception('Source account does not have enough balance');
        }

        $sourceAccount->setBalance($sourceAccount->getBalance() - $transferAmount);
        $this->entityManager->persist($sourceAccount);

        $exchangeRate = $this->exchangeRateRepository->findExchangeRate($sourceAccount->getCurrency()->getId(), $targetAccount->getCurrency()->getId());

        if (!$exchangeRate) {
            throw new \Exception('Exchange rate does not exist: '. $sourceAccount->getCurrency()->getName() . ' to ' . $targetAccount->getCurrency()->getName());
        }

        //floor the amount if there's numbers after the 2nd decimal point (this is how I understand transactions)
        $targetTransferAmount = floor($transferAmount * $exchangeRate->getRate() * 100) / 100;
        $targetAccount->setBalance((string)($targetAccount->getBalance() + $targetTransferAmount));
        $this->entityManager->persist($targetAccount);

        $this->entityManager->flush();
    }
}
