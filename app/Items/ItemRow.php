<?php

namespace App\Items;

final readonly class ItemRow
{
    public function __construct(
        public int $id,
        public int $projectId,
        public string $name,
        public ?string $service,
        public ?string $environment,
        public string $projectName,
        public string $fingerprint,
        public ?string $usageLocation = null,
        public ?string $memo = null,
    ) {}
}
