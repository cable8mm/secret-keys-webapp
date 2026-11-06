<?php

namespace Tests\Support;

use App\Projects\ProjectHasItems;
use App\Projects\ProjectIsFixed;
use App\Records\ItemRecord;
use App\Records\ItemWrite;
use App\Records\ProjectSummary;
use App\Records\RecordStore;
use App\Records\SimpleItemRecord;
use App\Records\SimpleItemWrite;
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

    public int $deleteProjectCalls = 0;

    public int $itemCreateCalls = 0;

    public int $itemUpdateCalls = 0;

    public int $itemDeleteCalls = 0;

    /**
     * @var list<SimpleItemRecord>
     */
    public array $aiItems = [];

    /**
     * @var list<SimpleItemRecord>
     */
    public array $infraItems = [];

    public function allProjects(): array
    {
        if ($this->failRead) {
            throw new RuntimeException('Read failed.');
        }

        return $this->projects;
    }

    public function createProject(string $name, ?string $note = null): ProjectSummary
    {
        $this->createCalls++;

        if ($this->failWrite) {
            throw new RuntimeException('Write failed.');
        }

        $project = new ProjectSummary(count($this->projects) + 1, $name);
        $this->projects[] = $project;

        return $project;
    }

    public function renameProject(int $id, string $name, ?string $note = null): void
    {
        $this->renameCalls++;

        if ($this->failWrite) {
            throw new RuntimeException('Write failed.');
        }

        foreach ($this->projects as $index => $project) {
            if ($project->id === $id) {
                if ($project->isFixed) {
                    throw new ProjectIsFixed('Project is fixed.');
                }

                $this->projects[$index] = new ProjectSummary($id, $name, $project->isFixed);

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

    public function setProjectArchived(int $id, bool $archived): void
    {
        if ($this->failWrite) {
            throw new RuntimeException('Write failed.');
        }

        foreach ($this->projects as $index => $project) {
            if ($project->id === $id) {
                if ($project->isFixed) {
                    throw new ProjectIsFixed('Project is fixed.');
                }

                $this->projects[$index] = new ProjectSummary($id, $project->name, $project->isFixed, $archived);

                return;
            }
        }

        throw new RuntimeException('Project not found.');
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
            $write->kind,
            $write->tags,
            $write->expiresAt,
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
                    $write->kind,
                    $write->tags,
                    $write->expiresAt,
                );

                return;
            }
        }

        throw new RuntimeException('Item not found.');
    }

    public function deleteItem(int $id): void
    {
        $this->itemDeleteCalls++;

        if ($this->failWrite) {
            throw new RuntimeException('Write failed.');
        }

        foreach ($this->items as $index => $item) {
            if ($item->id === $id) {
                array_splice($this->items, $index, 1);

                return;
            }
        }

        throw new RuntimeException('Item not found.');
    }

    public function findAiItem(int $id): ?SimpleItemRecord
    {
        return $this->findSimpleItem($this->aiItems, $id);
    }

    public function allAiItems(): array
    {
        if ($this->failRead) {
            throw new RuntimeException('Read failed.');
        }

        return $this->aiItems;
    }

    public function createAiItem(SimpleItemWrite $write): int
    {
        return $this->createSimpleItem($this->aiItems, $write, 'aiItemCreateCalls');
    }

    public function updateAiItem(int $id, SimpleItemWrite $write): void
    {
        $this->updateSimpleItem($this->aiItems, $id, $write);
    }

    public function deleteAiItem(int $id): void
    {
        $this->deleteSimpleItem($this->aiItems, $id);
    }

    public function findInfraItem(int $id): ?SimpleItemRecord
    {
        return $this->findSimpleItem($this->infraItems, $id);
    }

    public function allInfraItems(): array
    {
        if ($this->failRead) {
            throw new RuntimeException('Read failed.');
        }

        return $this->infraItems;
    }

    public function createInfraItem(SimpleItemWrite $write): int
    {
        return $this->createSimpleItem($this->infraItems, $write, 'infraItemCreateCalls');
    }

    public function updateInfraItem(int $id, SimpleItemWrite $write): void
    {
        $this->updateSimpleItem($this->infraItems, $id, $write);
    }

    public function deleteInfraItem(int $id): void
    {
        $this->deleteSimpleItem($this->infraItems, $id);
    }

    public int $aiItemCreateCalls = 0;

    public int $infraItemCreateCalls = 0;

    /**
     * @param  list<SimpleItemRecord>  $items
     */
    private function findSimpleItem(array $items, int $id): ?SimpleItemRecord
    {
        if ($this->failRead) {
            throw new RuntimeException('Read failed.');
        }

        foreach ($items as $item) {
            if ($item->id === $id) {
                return $item;
            }
        }

        return null;
    }

    /**
     * @param  list<SimpleItemRecord>  $items
     */
    private function createSimpleItem(array &$items, SimpleItemWrite $write, string $counter): int
    {
        $this->{$counter}++;

        if ($this->failWrite) {
            throw new RuntimeException('Write failed.');
        }

        $id = count($items) + 1;
        $items[] = new SimpleItemRecord($id, $write->name, $write->service, $write->protectedValue);

        return $id;
    }

    /**
     * @param  list<SimpleItemRecord>  $items
     */
    private function updateSimpleItem(array &$items, int $id, SimpleItemWrite $write): void
    {
        if ($this->failWrite) {
            throw new RuntimeException('Write failed.');
        }

        foreach ($items as $index => $item) {
            if ($item->id === $id) {
                $items[$index] = new SimpleItemRecord($id, $write->name, $write->service, $write->protectedValue);

                return;
            }
        }

        throw new RuntimeException('Item not found.');
    }

    /**
     * @param  list<SimpleItemRecord>  $items
     */
    private function deleteSimpleItem(array &$items, int $id): void
    {
        if ($this->failWrite) {
            throw new RuntimeException('Write failed.');
        }

        foreach ($items as $index => $item) {
            if ($item->id === $id) {
                array_splice($items, $index, 1);

                return;
            }
        }

        throw new RuntimeException('Item not found.');
    }

    public function deleteProject(int $id): void
    {
        $this->deleteProjectCalls++;

        if ($this->failWrite) {
            throw new RuntimeException('Write failed.');
        }

        foreach ($this->projects as $index => $project) {
            if ($project->id === $id) {
                if ($project->isFixed) {
                    throw new ProjectIsFixed('Project is fixed.');
                }

                foreach ($this->items as $item) {
                    if ($item->projectId === $id) {
                        throw new ProjectHasItems('Project has Items.');
                    }
                }

                array_splice($this->projects, $index, 1);

                return;
            }
        }

        throw new RuntimeException('Project not found.');
    }
}
