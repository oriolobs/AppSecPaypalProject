<?php

declare(strict_types=1);

namespace Pw\SlimApp\Model;

use DateTime;

final class User
{
    private string $email;
    private string $password;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private string $birthday;
    private string $phone_number;
    private string $user_activation_code;
    private string $user_email_status;
    private string $image_user_url;
    private float $wallet;

    public function __construct(
        string $email,
        string $password,
        DateTime $createdAt,
        DateTime $updatedAt,
        string $birthday,
        string $phone_number,
        string $user_activation_code,
        string $user_email_status,
        float $wallet
    ) {
        $this->email = $email;
        $this->password = md5($password);
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->birthday = $birthday;
        $this->phone_number = $phone_number;
        $this->user_activation_code = $user_activation_code;
        $this->user_email_status = $user_email_status;
        $this->wallet = $wallet;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function checkEmail(string $email): bool{
        $ok = FALSE;
        //$domain;

        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            list($email, $domain) = explode('@', $email);
            if($domain == 'salle.url.edu')
                $ok = TRUE;
        }   //if

        return $ok;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function checkPassword(string $password): boolean{
        $ok = FALSE;

        if(strlen($password) > 5)
            if(preg_match("/[A-Z]/", $password) && preg_match("/[a-z]/",
                    $password) && preg_match('~[0-9]~', $password))
                $ok = TRUE;
        return $ok;
    }

    public function createdAt(): DateTime
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function birthday(): string
    {
        return $this->birthday;
    }

    public function checkBirthday(string $birthday): bool{
        $ok = FALSE;
        $current_date;
        $interval;

        $aux = new DateTime($birthday);

        $current_date = new DateTime(date("Y-m-d"));
        $interval = $current_date->diff($aux);

        if(intval($interval->y) > 18)
            $ok = TRUE;

        return $ok;
    }

    public function phone_number(): string
    {
        return $this->phone_number;
    }

    public function checkPhoneNumber(string $phone_number): boolean{
        $ok = FALSE;

        if(strlen($phone_number) == 9)
            $ok = TRUE;

        return $ok;
    }

    public function user_activation_code(): string
    {
        return $this->user_activation_code;
    }

    public function user_email_status(): string
    {
        return $this->user_email_status;
    }

    public function image_user_url(): string
    {
        return $this->image_user_url;
    }

    public function setImage_user_url(string $image_user_url)
    {
        $this->image_user_url = $image_user_url;
    }


    public function wallet(): float
    {return $this->wallet;}


    public function setWallet(float $wallet): void
    {$this->wallet = $wallet;}
}
