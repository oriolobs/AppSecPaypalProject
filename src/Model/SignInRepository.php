<?php

declare(strict_types=1);

namespace Pw\SlimApp\Model;

interface SignInRepository
{
    public function signIn(SignInForm $form): bool;
}