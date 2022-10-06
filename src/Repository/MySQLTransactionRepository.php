<?php
declare(strict_types=1);
namespace Pw\SlimApp\Repository;

use PDO;
use Pw\SlimApp\Model\Transaction;
use Pw\SlimApp\Model\TransactionRepository;

final class MySQLTransactionRepository implements TransactionRepository
{
    private PDOSingleton $database;
    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function getLast5Transactions($email): void
    {
        $statement = $this->database->connection()->prepare("SELECT * FROM transaction WHERE sender = :email OR receiver = :email LIMIT 5;");
        $statement->bindParam('email', $_SESSION['email'], PDO::PARAM_STR);
        $statement->execute();
        $transactions['1'] = $statement->fetch();
        $transactions['2'] = $statement->fetch();
        $transactions['3'] = $statement->fetch();
        $transactions['4'] = $statement->fetch();
        $transactions['5'] = $statement->fetch();
        $GLOBALS['transactions'] = $transactions;
    }

    public function getAllTransactions($email): void
    {
        $statement = $this->database->connection()->prepare("SELECT * FROM transaction WHERE sender = :email OR receiver=:email");
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();
        $GLOBALS['transactions'] = $statement->fetchAll();
    }
}
