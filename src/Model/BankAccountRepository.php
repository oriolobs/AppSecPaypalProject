<?php
declare(strict_types=1);

namespace Pw\SlimApp\Model;

interface BankAccountRepository
{
    public function checkBankAccount($email): bool;

    public function postBankAccount(BankAccountForm $bankAccountForm): void;

    public function loadMoney($money): void;
}
