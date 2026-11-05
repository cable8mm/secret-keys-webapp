<?php

namespace App\Records;

interface RecordStore
{
    /**
     * @return list<ProjectSummary>
     */
    public function allProjects(): array;

    public function createProject(string $name): ProjectSummary;

    public function renameProject(int $id, string $name): void;
}
