<?php

namespace App\Items;

final readonly class ItemLoadResult
{
    private function __construct(
        public bool $success,
        public ?ItemDetail $detail,
        public ?string $errorCode,
        public ?string $error,
    ) {}

    public static function success(ItemDetail $detail): self
    {
        return new self(true, $detail, null, null);
    }

    public static function failure(string $errorCode, string $error): self
    {
        return new self(false, null, $errorCode, $error);
    }
}
