<?php

declare(strict_types=1);

namespace Pw\SlimApp\Repository;

use PDO;
use Pw\SlimApp\Model\SignInForm;
use Pw\SlimApp\Model\SignInRepository;


final class MySQLSignInRepository implements SignInRepository
{
    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }
    public function signIn(SignInForm $form): bool
    {
        $statement = $this->database->connection()->prepare("SELECT password, user_email_status FROM user WHERE email = :email");
        $email = $form->email();
        $password = md5($form->password());
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        if($result == false){
            return false;
        }
        if($result['user_email_status'] == null){
            // USER NOT CREATED
            return false;
        }else{
            if($result['password'] != $password){
                // WRONG PASSWORD
                return false;
            }else{
                if($result['user_email_status'] == "not verified"){
                    // EMAIL NOT VERIFIED
                    return false;
                }else{
                    return true;
                    //EVERYTHING OK, SIGN IN
                }
            }
        }


    }
}