<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\Customer;
use App\Repository\CurrencyRepository;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;

class DummyService
{
    private CustomerRepository $customerRepository;
    private EntityManagerInterface $entityManager;
    private CurrencyRepository $currencyRepository;

    public function __construct(CustomerRepository $customerRepository, CurrencyRepository $currencyRepository, EntityManagerInterface $entityManager)
    {
        $this->customerRepository = $customerRepository;
        $this->entityManager = $entityManager;
        $this->currencyRepository = $currencyRepository;
    }

    public static function latestRates(string $currencyIso): string
    {
        return '{"success":true,"timestamp":1743951544,"base":"EUR","date":"2025-04-06","rates":{"AED":4.02547,"AFN":78.958383,"ALL":99.102869,"AMD":431.181955,"ANG":1.961978,"AOA":1003.890567,"ARS":1184.765046,"AUD":1.813586,"AWG":1.97271,"AZN":1.867466,"BAM":1.955265,"BBD":2.22659,"BDT":133.983319,"BGN":1.957778,"BHD":0.412787,"BIF":3277.602688,"BMD":1.09595,"BND":1.474296,"BOB":7.619914,"BRL":6.405394,"BSD":1.102698,"BTC":1.3020076e-5,"BTN":94.079244,"BWP":15.358795,"BYN":3.608812,"BYR":21480.619234,"BZD":2.215094,"CAD":1.559263,"CDF":3148.664634,"CHF":0.944431,"CLF":0.02729,"CLP":1047.223301,"CNY":7.980215,"CNH":7.994999,"COP":4582.945323,"CRC":557.847278,"CUC":1.09595,"CUP":29.042674,"CVE":110.234821,"CZK":25.256829,"DJF":196.376238,"DKK":7.461451,"DOP":69.640934,"DZD":146.03502,"EGP":55.406831,"ERN":16.439249,"ETB":145.347308,"EUR":1,"FJD":2.537019,"FKP":0.848847,"GBP":0.850992,"GEL":3.01429,"GGP":0.848847,"GHS":17.092321,"GIP":0.848847,"GMD":78.364643,"GNF":9543.387299,"GTQ":8.51067,"GYD":230.706839,"HKD":8.520518,"HNL":28.214276,"HRK":7.531044,"HTG":144.290497,"HUF":405.95125,"IDR":18351.682095,"ILS":4.102536,"IMP":0.848847,"INR":93.739724,"IQD":1444.604509,"IRR":46139.49374,"ISK":144.852129,"JEP":0.848847,"JMD":173.912388,"JOD":0.776923,"JPY":161.033451,"KES":142.530979,"KGS":95.094267,"KHR":4414.791359,"KMF":493.729615,"KPW":986.354973,"KRW":1599.550347,"KWD":0.337323,"KYD":0.918948,"KZT":559.11693,"LAK":23885.460858,"LBP":98806.249733,"LKR":326.960488,"LRD":220.54962,"LSL":21.028443,"LTL":3.236056,"LVL":0.66293,"LYD":5.33354,"MAD":10.502325,"MDL":19.485665,"MGA":5113.600046,"MKD":61.518158,"MMK":2300.773509,"MNT":3844.69323,"MOP":8.828083,"MRU":43.97796,"MUR":48.956499,"MVR":16.881727,"MWK":1912.176502,"MXN":22.397605,"MYR":4.862772,"MZN":70.042575,"NAD":21.028443,"NGN":1679.894432,"NIO":40.578891,"NOK":11.801632,"NPR":150.52679,"NZD":1.958628,"OMR":0.421635,"PAB":1.102798,"PEN":4.052091,"PGK":4.551754,"PHP":62.891131,"PKR":309.568949,"PLN":4.273706,"PYG":8840.579707,"QAR":4.019799,"RON":4.977847,"RSD":117.117937,"RUB":92.974546,"RWF":1589.164933,"SAR":4.112539,"SBD":9.114284,"SCR":15.716697,"SDG":658.12198,"SEK":10.951065,"SGD":1.474715,"SHP":0.861245,"SLE":24.933268,"SLL":22981.523891,"SOS":630.227462,"SRD":40.162734,"STD":22683.951476,"SVC":9.649358,"SYP":14249.362274,"SZL":21.036241,"THB":37.713872,"TJS":12.003414,"TMT":3.835825,"TND":3.376876,"TOP":2.566829,"TRY":41.607525,"TTD":7.469955,"TWD":36.360884,"TZS":2949.992378,"UAH":45.388374,"UGX":4030.896458,"USD":1.09595,"UYU":46.647229,"UZS":14248.099286,"VES":76.89351,"VND":28280.988741,"VUV":133.834687,"WST":3.068195,"XAF":655.777467,"XAG":0.037037,"XAU":0.000361,"XCD":2.96186,"XDR":0.815577,"XOF":655.777467,"XPF":119.331742,"YER":269.220506,"ZAR":20.960317,"ZMK":9864.868719,"ZMW":30.57363,"ZWL":352.89544}}';
    }

    public function AddJohnDoe(): void
    {
        if (!$this->customerRepository->findOneBy(['name' => 'John'])) {
            $john = new Customer();
            $john->setName('John');
            $this->entityManager->persist($john);
            $this->entityManager->flush();

            $currency = $this->currencyRepository->findOneBy(['name' => 'USD']);

            $johnAccount = new Account();
            $johnAccount->setCustomer($john);
            $johnAccount->setCurrency($currency);
            $johnAccount->setBalance('10');
            $this->entityManager->persist($johnAccount);
            $this->entityManager->flush();
        }

        if (!$this->customerRepository->findOneBy(['name' => 'Doe'])) {
            $john = new Customer();
            $john->setName('Doe');
            $this->entityManager->persist($john);
            $this->entityManager->flush();

            $currency = $this->currencyRepository->findOneBy(['name' => 'EUR']);

            $johnAccount = new Account();
            $johnAccount->setCustomer($john);
            $johnAccount->setCurrency($currency);
            $johnAccount->setBalance('20');
            $this->entityManager->persist($johnAccount);
            $this->entityManager->flush();
        }
    }
}
