<?php

namespace App\Service;

use App\Entity\Account;

class AccountTransferService
{
    public function transferBetweenAccounts(Account $sourceAccount, Account $targetAccount, float $transferAmount)
    {
        if ($sourceAccount->getBalance() < $transferAmount) {
            throw new \Exception('Source account does not have enough balance');
        }

        $sourceAccount->setBalance($sourceAccount->getBalance() - $transferAmount);
        $targetAccount->setBalance($targetAccount->getBalance());
        $sourceCurrency = $sourceAccount->getCurrency();
        $targetCurrency = $targetAccount->getCurrency();
    }
}
