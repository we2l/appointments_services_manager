<?php

namespace App\Services\Contracts;

use App\Models\User;

interface AuthServiceInterface
{
    public function registerAuth(array $data): array;
    public function loginAuth(array $data): array;
}
