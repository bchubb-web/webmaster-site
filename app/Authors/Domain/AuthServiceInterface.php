<?php

namespace App\Authors\Domain;

interface AuthServiceInterface
{
    public function signUp(string $email, string $password, string $name): bool;

    public function login(string $email, string $password): ?string;
}
