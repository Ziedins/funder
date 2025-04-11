<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\Currency;
use App\Entity\ExchangeRate;
use App\Entity\Transaction;
use App\Repository\ExchangeRateRepository;
use Doctrine\ORM\EntityManagerInterface;

class AccountTransferService
{
    private ExchangeRateRepository $exchangeRateRepository;
    private EntityManagerInterface $entityManager;
    private ExchangeRateService $exchangeRateService;

    public function __construct(
        ExchangeRateRepository $exchangeRateRepository,
        EntityManagerInterface $entityManager,
        ExchangeRateService $exchangeRateService
    )
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->entityManager = $entityManager;
        $this->exchangeRateService = $exchangeRateService;
    }

    /**
     * @throws \Exception
     */
    public function transferBetweenAccounts(Account $sourceAccount, Account $targetAccount, float $transferAmount): void
    {
        if ($sourceAccount->getBalance() < $transferAmount) {
            throw new \Exception('Source account does not have enough balance');
        }

        $exchangeRate = $this->getExchangeRate($sourceAccount->getCurrency(), $targetAccount->getCurrency());

        if (!$exchangeRate) {
            throw new \Exception('A valid exchange rate not found: ' . $sourceAccount->getCurrency()->getName() . ' to ' . $targetAccount->getCurrency()->getName());
        }

        $sourceAccount->setBalance($sourceAccount->getBalance() - $transferAmount);
        $this->entityManager->persist($sourceAccount);
        //floor the amount if there's numbers after the 2nd decimal point (this is how I currently understand currency rounding)
        $targetTransferAmount = floor($transferAmount * $exchangeRate->getRate() * 100) / 100;
        $targetAccount->setBalance((string)($targetAccount->getBalance() + $targetTransferAmount));
        $this->entityManager->persist($targetAccount);

        $transaction = new Transaction();
        $transaction->setSourceAccount($sourceAccount)
            ->setTargetAccount($targetAccount)
            ->setAmount($transferAmount)
            ->setCreatedAt(new \DateTimeImmutable());
        $this->entityManager->persist($transaction);

        $this->entityManager->flush();
    }

    /**
     * @throws \Exception
     */
    private function getExchangeRate(Currency $baseCurrency, Currency $targetCurrency): ?ExchangeRate
    {
        $exchangeRate = $this->exchangeRateRepository->findExchangeRate($baseCurrency->getId(), $targetCurrency->getId(), true);

        if(!$exchangeRate)
        {
            $this->exchangeRateService->updateExchangeRates($baseCurrency->getName(), $targetCurrency->getName());
        }

        return $this->exchangeRateRepository->findExchangeRate($baseCurrency->getId(), $targetCurrency->getId(), true);
    }
}
