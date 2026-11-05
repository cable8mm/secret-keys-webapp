<?php

namespace App\Items;

final class FingerprintDeriver
{
    public function derive(string $value): string
    {
        $trimmed = trim($value);

        if (mb_strlen($trimmed, 'UTF-8') < 8) {
            return '';
        }

        return mb_substr($trimmed, -4, null, 'UTF-8');
    }
}
