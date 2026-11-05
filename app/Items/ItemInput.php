<?php

namespace App\Items;

final readonly class ItemInput
{
    public function __construct(
        public string $name,
        public ?int $projectId,
        public string $service,
        public string $environment,
        public string $value,
        public string $usageLocation,
        public string $issuedDate,
        public string $memo,
    ) {}
}
