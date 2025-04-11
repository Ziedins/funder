<?php

namespace App\Client;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ExchangeRateClient
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @throws \Exception
     */
    public function getLatestRates(string $currencyIso): array
    {
        $url = $this->parameterBag->get('app.exchange_rate_url') .
            '/latest?access_key=' . $this->parameterBag->get('app.exchange_rate_token') .
            '&base=' . $currencyIso;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $responseJson = curl_exec($curl);
        curl_close($curl);

        $response = json_decode($responseJson, true);

        if (isset($response['success']) && $response['success'] === true && isset($response['rates'])) {
            return $response['rates'];
        } else if (isset($response['error']['code']) && $response['success'] === false && $response['error']['code'] === 105) {
            throw new \Exception('This currency is not available in our service: ' . $currencyIso);
        } else {
            throw new \Exception('Failed to retrieve rates for currency: ' . $currencyIso);
        }
    }
}
