<?php

namespace App\Records;

final readonly class ProjectSummary
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}
