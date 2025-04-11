<?php

namespace App\Tests\Controller;

use App\Entity\Account;
use App\Entity\Currency;
use App\Entity\Customer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ApiControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private \Symfony\Bundle\FrameworkBundle\KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        self::ensureKernelShutdown();
    }

    private function createCurrency(string $currencyName)
    {
        $currency = new Currency();
        $currency->setName($currencyName);
        $this->entityManager->persist($currency);
        $this->entityManager->flush();

        return $currency;
    }

    private function createAccount(float $balance, Currency $currency): Account
    {
        $customer = new Customer();
        $customer->setName("test".rand(1, 99));
        $this->entityManager->persist($customer);
        $account = new Account();
        $account->setBalance($balance)
            ->setCustomer($customer)
            ->setCurrency($currency);
        $this->entityManager->persist($account);
        $this->entityManager->flush();

        return $account;
    }

    public function testSuccessfulTransfer(): void
    {
        $client = ApiControllerTest::createClient();

        $currency = $this->createCurrency("EUR");
        $from = $this->createAccount(22, $currency);
        $to = $this->createAccount(33, $currency);

        $payload = [
            'sourceAccountId' => $from->getId(),
            'targetAccountId' => $to->getId(),
            'amount' => 3
        ];

        $client->request(
            'POST',
            '/transfer',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertTrue($response['success']);

        // Refresh entities and assert balances
        $this->entityManager->refresh($from);
        $this->entityManager->refresh($to);
        $this->assertEquals(19, $from->getBalance());
        $this->assertEquals(36, $to->getBalance());
    }
}
