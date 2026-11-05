<?php

namespace Tests\Support;

use App\Records\ProjectSummary;
use App\Records\RecordStore;
use RuntimeException;

final class FakeRecordStore implements RecordStore
{
    /**
     * @var list<ProjectSummary>
     */
    public array $projects = [];

    public bool $failRead = false;

    public bool $failWrite = false;

    public int $createCalls = 0;

    public function allProjects(): array
    {
        if ($this->failRead) {
            throw new RuntimeException('Read failed.');
        }

        return $this->projects;
    }

    public function createProject(string $name): ProjectSummary
    {
        $this->createCalls++;

        if ($this->failWrite) {
            throw new RuntimeException('Write failed.');
        }

        $project = new ProjectSummary(count($this->projects) + 1, $name);
        $this->projects[] = $project;

        return $project;
    }
}
