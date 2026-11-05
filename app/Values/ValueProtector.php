<?php

namespace App\Values;

use Illuminate\Support\Facades\Crypt;

final class ValueProtector
{
    public function protect(string $value): string
    {
        return Crypt::encryptString($value);
    }

    public function unprotect(string $protected): string
    {
        return Crypt::decryptString($protected);
    }
}
