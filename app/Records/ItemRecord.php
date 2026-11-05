<?php

namespace App\Records;

final readonly class ItemRecord
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $service,
        public ?string $environment,
        public string $projectName,
        public string $protectedValue,
    ) {}
}
