<?php

declare(strict_types=1);

namespace Pw\SlimApp\Repository;

use PDO;
use Pw\SlimApp\Model\RequestRepository;
use Pw\SlimApp\Model\Transaction;
use Pw\SlimApp\Model\TransactionRepository;

final class MySQLRequestRepository implements RequestRepository
{
    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function postRequest($money_request): void
    {
        $statement = $this->database->connection()->prepare("INSERT INTO request(requester, payer, amount, status) VALUES(:requester, :payer, :amount, 'not verified')");
        $statement->bindParam('requester', $_SESSION['email'], PDO::PARAM_STR );
        $statement->bindParam('payer', $money_request['email'], PDO::PARAM_STR);
        $statement->bindParam('amount', $money_request['amount'], PDO::PARAM_STR);
        $statement->execute();
    }


    public function getUnpaidRequests($email): void
    {
        $statement = $this->database->connection()->prepare("SELECT * FROM request WHERE payer = :email AND status='not verified'");
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();
        $GLOBALS['requests'] = $statement->fetchAll();
    }


    public function checkRequestReceiver($email, $id_request): bool
    {
        $statement = $this->database->connection()->prepare("SELECT payer FROM request WHERE id = :id");
        $statement->bindParam('id', $id_request, PDO::PARAM_STR);
        $statement->execute();
        $payer = $statement->fetch();
        if($payer[0] == $email){
            return true;
        }else{
            return false;
        }
    }

    public function checkEnoughMoney($email, $id_request): bool
    {
        $statement = $this->database->connection()->prepare("SELECT wallet FROM user WHERE email = :email");
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();
        $money = $statement->fetch();

        $statement2 = $this->database->connection()->prepare("SELECT amount FROM request WHERE id = :id");
        $statement2->bindParam('id', $id_request, PDO::PARAM_STR);
        $statement2->execute();
        $amount = $statement2->fetch();

        if($money[0]-$amount[0] < 0){
            return false;
        }else{
            return true;
        }
    }
    public function acceptRequest($email, $id_request): void
    {
        $statement = $this->database->connection()->prepare("SELECT amount, requester FROM request WHERE id = :id");
        $statement->bindParam('id', $id_request, PDO::PARAM_STR);
        $statement->execute();
        $amount_requester = $statement->fetch();

        $statement2 = $this->database->connection()->prepare("UPDATE request SET status = 'done' WHERE id = :id");
        $statement2->bindParam('id', $id_request, PDO::PARAM_STR);
        $statement2->execute();

        $statement3 = $this->database->connection()->prepare("UPDATE user SET wallet = wallet - :amount WHERE email = :email");
        $statement3->bindParam('email', $email, PDO::PARAM_STR);
        $statement3->bindParam('amount', $amount_requester[0], PDO::PARAM_STR);
        $statement3->execute();


        $statement4 = $this->database->connection()->prepare("UPDATE user SET wallet = wallet + :amount WHERE email = :email");
        $statement4->bindParam('email', $amount_requester[1], PDO::PARAM_STR);
        $statement4->bindParam('amount', $amount_requester[0], PDO::PARAM_STR);
        $statement4->execute();

        $statement5 = $this->database->connection()->prepare("INSERT INTO transaction(sender, receiver, amount) VALUES(:sender, :receiver, :amount)");
        $statement5->bindParam('sender', $email, PDO::PARAM_STR);
        $statement5->bindParam('receiver', $amount_requester[1], PDO::PARAM_STR);
        $statement5->bindParam('amount', $amount_requester[0], PDO::PARAM_STR);
        $statement5->execute();
    }
}
