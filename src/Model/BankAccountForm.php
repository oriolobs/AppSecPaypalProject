<?php
declare(strict_types=1);

namespace Pw\SlimApp\Model;

final class BankAccountForm
{
    private string $owner_name;
    private string $iban;

    public function __construct(string $owner_name, string $iban)
    {
        $this->owner_name = $owner_name;
        $this->iban = $iban;
    }

    public function iban():string
    {
        return $this->iban;

    }

    public function owner_name():string
    {
        return $this->owner_name;
    }
}