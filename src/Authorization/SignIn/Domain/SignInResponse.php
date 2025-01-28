<?php

declare(strict_types=1);

namespace App\Authorization\SignIn\Domain;

interface SignInResponse
{
    public function response(): mixed;
}
