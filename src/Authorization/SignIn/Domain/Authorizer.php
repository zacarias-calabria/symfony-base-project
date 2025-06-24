<?php

declare(strict_types=1);

namespace App\Authorization\SignIn\Domain;

interface Authorizer
{
    public function signIn(): SignInResponse;
}
