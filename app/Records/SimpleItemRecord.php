<?php

namespace App\Records;

final readonly class SimpleItemRecord
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $service,
        public string $protectedValue,
    ) {}
}
