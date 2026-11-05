<?php

namespace App\Items;

final readonly class ItemDetail
{
    public function __construct(
        public int $id,
        public string $name,
        public ?int $projectId,
        public string $service,
        public string $environment,
        public string $value,
        public string $usageLocation,
        public string $issuedDate,
        public string $memo,
        public string $fingerprint,
    ) {}
}
