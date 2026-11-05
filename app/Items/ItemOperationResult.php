<?php

namespace App\Items;

final readonly class ItemOperationResult
{
    /**
     * @param  list<array{code: string, message: string}>  $errors
     */
    private function __construct(
        public bool $success,
        public array $errors,
    ) {}

    public static function success(): self
    {
        return new self(true, []);
    }

    /**
     * @param  list<array{code: string, message: string}>  $errors
     */
    public static function failure(array $errors): self
    {
        return new self(false, $errors);
    }
}
