<?php

namespace App\Projects;

use App\Records\ProjectSummary;

final readonly class ProjectListResult
{
    /**
     * @param  list<ProjectSummary>  $projects
     */
    private function __construct(
        public bool $success,
        public array $projects,
        public ?string $errorCode,
        public ?string $error,
    ) {}

    /**
     * @param  list<ProjectSummary>  $projects
     */
    public static function success(array $projects): self
    {
        return new self(true, $projects, null, null);
    }

    public static function failure(string $errorCode, string $error): self
    {
        return new self(false, [], $errorCode, $error);
    }
}
