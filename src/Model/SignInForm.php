<?php

declare(strict_types=1);

namespace Pw\SlimApp\Model;

final class SignInForm
{
    private string $email;
    private string $password;

    public function __construct(string $email, string $password){
        $this->email = $email;
        $this->password = $password;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
