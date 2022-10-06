<?php

declare(strict_types=1);

namespace Pw\SlimApp\Repository;

use PDO;
use Pw\SlimApp\Model\User;
use Pw\SlimApp\Model\UserRepository;

use DateTime;

final class MySQLUserRepository implements UserRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function userExists(string $email): bool
    {
        $exists = FALSE;
        $query = "SELECT `email` FROM `user` WHERE `email` = :email";

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch();

        if($result != '')
          $exists = TRUE;

        return (bool)$exists;
    }

    public function save(User $user): void
    {
        $query = <<<'QUERY'
        INSERT INTO user(email, password, created_at, updated_at, birthday, phone_number, user_activation_code, user_email_status, wallet)
        VALUES(:email, :password, :created_at, :updated_at, :birthday, :phone_number, :user_activation_code,:user_email_status, 20.0)
QUERY;
        $statement = $this->database->connection()->prepare($query);

        $email = $user->email();
        $password = $user->password();
        $createdAt = $user->createdAt()->format(self::DATE_FORMAT);
        $updatedAt = $user->updatedAt()->format(self::DATE_FORMAT);
        $birthday = $user->birthday();
        $phone_number = $user->phone_number();
        $user_activation_code = $user->user_activation_code();
        $user_email_status = $user->user_email_status();

        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('created_at', $createdAt, PDO::PARAM_STR);
        $statement->bindParam('updated_at', $updatedAt, PDO::PARAM_STR);
        $statement->bindParam('birthday', $birthday, PDO::PARAM_STR);
        $statement->bindParam('phone_number', $phone_number, PDO::PARAM_STR);
        $statement->bindParam('user_activation_code', $user_activation_code, PDO::PARAM_STR);
        $statement->bindParam('user_email_status', $user_email_status, PDO::PARAM_STR);

        $statement->execute();
    }

    public function verifyAccount(string $email): bool
    {
      $exists = FALSE;
      $query = "SELECT `user_email_status` FROM `user` WHERE `email` = :email";

      $statement = $this->database->connection()->prepare($query);
      $statement->bindParam('email', $email, PDO::PARAM_STR);
      $statement->execute();
      $result = $statement->fetch();

      if($result != 'not verified')
        $exists = TRUE;

      return (bool)$exists;
    }

    public function updateAccountToVerified(string $email): void
    {
      $query = "UPDATE `user` SET `user_email_status` = 'verified' WHERE `email` = :email";

      $statement = $this->database->connection()->prepare($query);
      $statement->bindParam('email', $email, PDO::PARAM_STR);

      $statement->execute();
      //echo "ACCOUNT UPDATED TO VERIFIED";
    }

    public function getToken(string $user_activation_code): string
    {
      $query = "SELECT `user_email_status` FROM `user` WHERE `user_activation_code` = :user_activation_code";

      $statement = $this->database->connection()->prepare($query);
      $statement->bindParam('user_activation_code', $user_activation_code, PDO::PARAM_STR);
      $statement->execute();

      $result = $statement->fetch();

      return $result[0];
    }

    public function getPassword(string $email): string
    {
      $query = "SELECT `password` FROM `user` WHERE `email` = :email";

      $statement = $this->database->connection()->prepare($query);
      $statement->bindParam('email', $email, PDO::PARAM_STR);
      $statement->execute();

      $result = $statement->fetch();

      return $result[0];
    }

    public function changePassword(string $email, string $new_password): void
    {
      $query = "UPDATE `user` SET `password` = :new_password WHERE `email` = :email";

      $statement = $this->database->connection()->prepare($query);
      $statement->bindParam('email', $email, PDO::PARAM_STR);
      $statement->bindParam('new_password', $new_password, PDO::PARAM_STR);
      $statement->execute();
    }

    public function getUserInfo(string $email): User
    {
        $query = "SELECT * FROM `user` WHERE `email` = :email";

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();

        $result = $statement->fetch();
        if(isset($result['wallet'])){
            $user = new User(
                $result['email'],
                '',
                new DateTime(),
                new DateTime(),
                $result['birthday'],
                $result['phone_number'],
                '',
                '',

                floatval($result['wallet']),
            );

        }else{
            $user = new User(
                $result['email'],
                '',
                new DateTime(),
                new DateTime(),
                $result['birthday'],
                $result['phone_number'],
                '',
                '',
                0,
            );
        }


        $user->setImage_user_url($result[8]);

        return $user;
    }

    public function setProfile(string $email, string $profile_picture, string $phone_number): void
    {

      if($profile_picture == ""){
        $query = "UPDATE `user` SET `phone_number` = :phone_number WHERE `email` = :email";

        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('phone_number', $phone_number, PDO::PARAM_STR);
        $statement->execute();
      } //if
      else{
        if($phone_number == ""){
          $query = "UPDATE `user` SET `profile_picture` = :profile_picture WHERE `email` = :email";

          $statement = $this->database->connection()->prepare($query);
          $statement->bindParam('email', $email, PDO::PARAM_STR);
          $statement->bindParam('profile_picture', $profile_picture, PDO::PARAM_STR);
          $statement->execute();
        } //if
        else{
          $query = "UPDATE `user` SET `profile_picture` = :profile_picture, `phone_number` = :phone_number WHERE `email` = :email";

          $statement = $this->database->connection()->prepare($query);
          $statement->bindParam('email', $email, PDO::PARAM_STR);
          $statement->bindParam('profile_picture', $profile_picture, PDO::PARAM_STR);
          $statement->bindParam('phone_number', $phone_number, PDO::PARAM_STR);
          $statement->execute();
        } //else
      } //else
    }

    public function checkValidOperation(string $sender, float $amount): bool{

          $query = "SELECT IF((`wallet` - :amount) >= 0, 'true', 'false') FROM `user` WHERE `email` = :sender";

          $statement = $this->database->connection()->prepare($query);
          $statement->bindParam('amount', $amount, PDO::PARAM_STR);
          $statement->bindParam('sender', $sender, PDO::PARAM_STR);
          $statement->execute();

          $result = $statement->fetch();

          if($result[0] == 'false'){
            return FALSE;
          } //if
          else{
            return TRUE;
          } //else
    }

    public function addTransaction(string $sender, string $reciver, float $amount){

          $query = "INSERT INTO transaction(sender, receiver, amount)
          VALUES(:sender, :reciver, :amount)";

          $statement = $this->database->connection()->prepare($query);
          $statement->bindParam('sender', $sender, PDO::PARAM_STR);
          $statement->bindParam('reciver', $reciver, PDO::PARAM_STR);
          $statement->bindParam('amount', $amount, PDO::PARAM_STR);
          $statement->execute();
    }

    public function sendMoney(string $sender, string $reciver, float $amount){

          $query = "SELECT `user_email_status` FROM `user` WHERE `email` = :reciver";

          $statement = $this->database->connection()->prepare($query);
          $statement->bindParam('reciver', $reciver, PDO::PARAM_STR);

          $statement->execute();

          $result = $statement->fetch();

          if($result[0] == 'not verified'){
            $_SESSION['sendMoney'] = "USER NOT VERIFIED";
          }
          else{

              $query = "UPDATE `user` SET `wallet` = `wallet` - :amount WHERE `email` = :sender";

              $statement = $this->database->connection()->prepare($query);
              $statement->bindParam('amount', $amount, PDO::PARAM_STR);
              $statement->bindParam('sender', $sender, PDO::PARAM_STR);
              $statement->execute();

              $query = "UPDATE `user` SET `wallet` = `wallet` + :amount WHERE `email` = :reciver";

              $statement = $this->database->connection()->prepare($query);
              $statement->bindParam('amount', $amount, PDO::PARAM_STR);
              $statement->bindParam('reciver', $reciver, PDO::PARAM_STR);
              $statement->execute();
          } //else
    }
}
