<?php

namespace App\Records;

final readonly class ItemWrite
{
    public function __construct(
        public int $projectId,
        public string $name,
        public string $protectedValue,
        public ?string $service,
        public ?string $environment,
        public ?string $usageLocation,
        public ?string $issuedDate,
        public ?string $memo,
        public ?string $kind = null,
        public ?string $tags = null,
        public ?string $expiresAt = null,
    ) {}
}
