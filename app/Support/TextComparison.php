<?php

namespace App\Support;

final class TextComparison
{
    public function compare(string $left, string $right): int
    {
        return strcmp(
            mb_strtolower($left, 'UTF-8'),
            mb_strtolower($right, 'UTF-8'),
        );
    }
}
