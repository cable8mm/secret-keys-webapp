<?php

namespace App\Projects;

final readonly class ProjectOperationResult
{
    private function __construct(
        public bool $success,
        public ?string $errorCode,
        public ?string $error,
    ) {}

    public static function success(): self
    {
        return new self(true, null, null);
    }

    public static function failure(string $errorCode, string $error): self
    {
        return new self(false, $errorCode, $error);
    }
}
