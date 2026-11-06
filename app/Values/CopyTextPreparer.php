<?php

namespace App\Values;

final class CopyTextPreparer
{
    public function prepare(string $value, ?string $kind = null): string
    {
        if ($kind === 'pem') {
            $text = str_replace(["\r\n", "\r"], "\n", $value);
            $text = trim($text);

            return $text === '' ? '' : $text."\n";
        }

        return trim($value);
    }
}
