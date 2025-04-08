<?php

namespace App\Service;

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

    public function __construct(
        CurrencyRepository     $currencyRepository,
        ExchangeRateRepository $exchangeRateRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->currencyRepository = $currencyRepository;
        $this->exchangeRateRepository = $exchangeRateRepository;
        $this->entityManager = $entityManager;
        $this->today = new \DateTime();
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

        //TODO fetch latestRates
        $response = json_decode(DummyRateService::latestRates($currency->getName()), true);

        if (isset($response['success']) && $response['success'] && isset($response['rates'])) {
            $this->persistCurrencyRates($currency, $response['rates']);
        } else {
            throw new \Exception('Failed to retrieve rates for currency: ' . $currency->getName());
        }
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
