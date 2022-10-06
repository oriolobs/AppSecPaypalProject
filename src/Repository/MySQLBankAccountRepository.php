<?php
declare(strict_types=1);
namespace Pw\SlimApp\Repository;
use PDO;
use Pw\SlimApp\Model\BankAccountForm;
use Pw\SlimApp\Model\BankAccountRepository;

final class MySQLBankAccountRepository implements BankAccountRepository
{
    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }
    public function checkBankAccount($email): bool
    {
        $statement = $this->database->connection()->prepare("SELECT * FROM (bank_account) WHERE email_owner = :email");
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result == false){
            return false;
        }else{
            $_SESSION['iban'] = $result['iban'];
            return true;
        }


    }

    public function postBankAccount(BankAccountForm $bankAccountForm): void
    {
        $statement = $this->database->connection()->prepare("INSERT INTO bank_account(iban, owner_name, email_owner) VALUES(:iban, :owner, :email);");
        $iban = $bankAccountForm->iban();
        $owner = $bankAccountForm->owner_name();
        $email = $_SESSION['email'];
        $statement->bindParam('iban', $iban, PDO::PARAM_STR);
        $statement->bindParam('owner', $owner, PDO::PARAM_STR);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();
    }

    public function loadMoney($money): void
    {
        $statement = $this->database->connection()->prepare("UPDATE user SET wallet = wallet + :money WHERE email = :email;");
        $statement->bindParam('money', $money,PDO::PARAM_STR);
        $statement->bindParam('email', $_SESSION['email'], PDO::PARAM_STR);
        $statement->execute();


        $statement2 = $this->database->connection()->prepare("INSERT INTO transaction(sender, receiver, amount) VALUES(:sender, :receiver, :amount);");
        $statement2->bindParam('sender', $_SESSION['email'], PDO::PARAM_STR);
        $statement2->bindParam('receiver', $_SESSION['email'], PDO::PARAM_STR);
        $statement2->bindParam('amount', $money, PDO::PARAM_STR);
        $statement2->execute();
    }
}
