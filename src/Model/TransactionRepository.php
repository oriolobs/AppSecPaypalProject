<?php
declare(strict_types=1);
namespace Pw\SlimApp\Model;

interface TransactionRepository
{
    public function getLast5Transactions($email): void;

    public function getAllTransactions($email): void;
}
