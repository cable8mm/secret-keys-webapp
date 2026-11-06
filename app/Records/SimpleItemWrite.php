<?php

namespace App\Records;

final readonly class SimpleItemWrite
{
    public function __construct(
        public string $name,
        public ?string $service,
        public string $protectedValue,
    ) {}
}
