<?php

namespace App\Items;

final readonly class ItemListResult
{
    /**
     * @param  list<ItemRow>  $rows
     */
    private function __construct(
        public bool $success,
        public array $rows,
        public ?string $errorCode,
        public ?string $error,
    ) {}

    /**
     * @param  list<ItemRow>  $rows
     */
    public static function success(array $rows): self
    {
        return new self(true, $rows, null, null);
    }

    public static function failure(string $errorCode, string $error): self
    {
        return new self(false, [], $errorCode, $error);
    }
}
