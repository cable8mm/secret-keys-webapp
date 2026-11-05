<?php

namespace App\Items;

final readonly class ItemRow
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $service,
        public ?string $environment,
        public string $projectName,
        public string $fingerprint,
    ) {}
}
