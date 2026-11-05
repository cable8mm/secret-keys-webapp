<?php

namespace Tests\Support;

use App\Records\ItemRecord;
use App\Records\ItemWrite;
use App\Records\ProjectSummary;
use App\Records\RecordStore;
use RuntimeException;

final class FakeRecordStore implements RecordStore
{
    /**
     * @var list<ProjectSummary>
     */
    public array $projects = [];

    /**
     * @var list<ItemRecord>
     */
    public array $items = [];

    public bool $failRead = false;

    public bool $failWrite = false;

    public int $createCalls = 0;

    public int $renameCalls = 0;

    public int $itemCreateCalls = 0;

    public int $itemUpdateCalls = 0;

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

    public function renameProject(int $id, string $name): void
    {
        $this->renameCalls++;

        if ($this->failWrite) {
            throw new RuntimeException('Write failed.');
        }

        foreach ($this->projects as $index => $project) {
            if ($project->id === $id) {
                $this->projects[$index] = new ProjectSummary($id, $name);

                return;
            }
        }

        throw new RuntimeException('Project not found.');
    }

    public function projectExists(int $id): bool
    {
        if ($this->failRead) {
            throw new RuntimeException('Read failed.');
        }

        foreach ($this->projects as $project) {
            if ($project->id === $id) {
                return true;
            }
        }

        return false;
    }

    public function allItems(): array
    {
        if ($this->failRead) {
            throw new RuntimeException('Read failed.');
        }

        return $this->items;
    }

    public function createItem(ItemWrite $write): int
    {
        $this->itemCreateCalls++;

        if ($this->failWrite) {
            throw new RuntimeException('Write failed.');
        }

        $projectName = 'Unknown';

        foreach ($this->projects as $project) {
            if ($project->id === $write->projectId) {
                $projectName = $project->name;
            }
        }

        $id = count($this->items) + 1;

        $this->items[] = new ItemRecord(
            $id,
            $write->projectId,
            $write->name,
            $write->service,
            $write->environment,
            $projectName,
            $write->protectedValue,
            $write->usageLocation,
            $write->issuedDate,
            $write->memo,
        );

        return $id;
    }

    public function findItem(int $id): ?ItemRecord
    {
        if ($this->failRead) {
            throw new RuntimeException('Read failed.');
        }

        foreach ($this->items as $item) {
            if ($item->id === $id) {
                return $item;
            }
        }

        return null;
    }

    public function updateItem(int $id, ItemWrite $write): void
    {
        $this->itemUpdateCalls++;

        if ($this->failWrite) {
            throw new RuntimeException('Write failed.');
        }

        foreach ($this->items as $index => $item) {
            if ($item->id === $id) {
                $projectName = 'Unknown';

                foreach ($this->projects as $project) {
                    if ($project->id === $write->projectId) {
                        $projectName = $project->name;
                    }
                }

                $this->items[$index] = new ItemRecord(
                    $id,
                    $write->projectId,
                    $write->name,
                    $write->service,
                    $write->environment,
                    $projectName,
                    $write->protectedValue,
                    $write->usageLocation,
                    $write->issuedDate,
                    $write->memo,
                );

                return;
            }
        }

        throw new RuntimeException('Item not found.');
    }
}
