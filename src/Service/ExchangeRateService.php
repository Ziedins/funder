<?php

namespace App\Service;

use App\Client\ExchangeRateClient;
use App\Entity\Currency;
use App\Entity\ExchangeRate;
use App\Repository\CurrencyRepository;
use App\Repository\ExchangeRateRepository;
use Doctrine\ORM\EntityManagerInterface;

class ExchangeRateService
{
    private CurrencyRepository $currencyRepository;
    private ExchangeRateRepository $exchangeRateRepository;
    private \DateTime $today;
    private EntityManagerInterface $entityManager;
    private ExchangeRateClient $exchangeRateClient;

    public function __construct(
        CurrencyRepository     $currencyRepository,
        ExchangeRateRepository $exchangeRateRepository,
        EntityManagerInterface $entityManager,
        ExchangeRateClient     $exchangeRateClient
    )
    {
        $this->currencyRepository = $currencyRepository;
        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->entityManager = $entityManager;
        $this->today = new \DateTime();
        $this->exchangeRateClient = $exchangeRateClient;
    }

    /**
     * @param string $currencyIso
     * @param bool $fetchMissing
     * @return void
     * @throws \Exception
     */
    public function updateExchangeRates(string $currencyIso, bool $fetchMissing = false): void
    {
        $currency = $this->currencyRepository->findOneBy(['name' => $currencyIso]);

        if (!$currency && $fetchMissing) {
            //todo fetch missing Currency
        } elseif (!$currency) {
            throw new \Exception('Currency not found: ' . $currencyIso);
        }

        $rates = $this->exchangeRateClient->getLatestRates($currencyIso);

        $this->persistCurrencyRates($currency, $rates);
    }

    private function persistCurrencyRates(Currency $currency, array $rates): void
    {
        foreach ($rates as $currencyIso => $rate) {
            $targetCurrency = $this->currencyRepository->findOneBy(['name' => $currencyIso]);
            if (!$targetCurrency) {
                $targetCurrency = new Currency();
                $targetCurrency->setName($currencyIso);
                $this->entityManager->persist($targetCurrency);
                $this->entityManager->flush();
            }

            $updatedExchangeRate = $this->updateExchangeRate($currency, $targetCurrency, $rate);
            $this->entityManager->persist($updatedExchangeRate);
        }

        $this->entityManager->flush();
    }

    private function updateExchangeRate(Currency $baseCurrency, Currency $targetCurrency, float $rate): ExchangeRate
    {
        $exchangeRate = $this->exchangeRateRepository->findExchangeRate($baseCurrency->getId(), $targetCurrency->getId());
        if ($exchangeRate && $exchangeRate->getUpdatedAt()->format('Y-m-d') < $this->today->format('Y-m-d')) {

            $exchangeRate->setUpdatedAt($this->today)
                ->setRate($rate);
            return $exchangeRate;
        } elseif (!$exchangeRate) {
            $exchangeRate = new ExchangeRate();
            $exchangeRate->setRate($rate)
                ->setUpdatedAt($this->today)
                ->setBaseCurrency($baseCurrency)
                ->setTargetCurrency($targetCurrency)
                ->setRate($rate);
            return $exchangeRate;
        }

        return $exchangeRate;
    }
}
