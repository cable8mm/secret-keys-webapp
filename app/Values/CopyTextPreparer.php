<?php

namespace App\Values;

final class CopyTextPreparer
{
    public function prepare(string $value): string
    {
        return trim($value);
    }
}
