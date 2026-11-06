<?php

namespace App\Records;

use App\Models\Item;
use App\Models\Project;
use App\Projects\ProjectHasItems;
use App\Projects\ProjectIsFixed;
use Illuminate\Support\Carbon;
use RuntimeException;

final class EloquentRecordStore implements RecordStore
{
    /**
     * @return list<ProjectSummary>
     */
    public function allProjects(): array
    {
        return Project::query()
            ->get()
            ->map(fn (Project $project): ProjectSummary => new ProjectSummary(
                (int) $project->getKey(),
                (string) $project->name,
                (bool) $project->is_fixed,
            ))
            ->all();
    }

    public function createProject(string $name): ProjectSummary
    {
        $project = Project::query()->create([
            'name' => $name,
        ]);

        return new ProjectSummary(
            (int) $project->getKey(),
            (string) $project->name,
        );
    }

    public function renameProject(int $id, string $name): void
    {
        $project = Project::query()->find($id);

        if ($project === null) {
            throw new RuntimeException('Project not found.');
        }

        if ((bool) $project->is_fixed) {
            throw new ProjectIsFixed('Project is fixed.');
        }

        $project->name = $name;
        $project->save();
    }

    public function deleteProject(int $id): void
    {
        $project = Project::query()->withCount('items')->find($id);

        if ($project === null) {
            throw new RuntimeException('Project not found.');
        }

        if ($project->items_count > 0) {
            throw new ProjectHasItems('Project has Items.');
        }

        if ((bool) $project->is_fixed) {
            throw new ProjectIsFixed('Project is fixed.');
        }

        $project->delete();
    }

    public function projectExists(int $id): bool
    {
        return Project::query()->whereKey($id)->exists();
    }

    public function findItem(int $id): ?ItemRecord
    {
        $item = Item::query()->with('project')->find($id);

        if ($item === null) {
            return null;
        }

        return new ItemRecord(
            (int) $item->getKey(),
            (int) $item->project_id,
            (string) $item->name,
            $item->service,
            $item->environment,
            (string) $item->project->name,
            (string) $item->value,
            $item->usage_location,
            $item->issued_date?->toDateString(),
            $item->memo,
        );
    }

    /**
     * @return list<ItemRecord>
     */
    public function allItems(): array
    {
        return Item::query()
            ->with('project')
            ->get()
            ->map(fn (Item $item): ItemRecord => new ItemRecord(
                (int) $item->getKey(),
                (int) $item->project_id,
                (string) $item->name,
                $item->service,
                $item->environment,
                (string) $item->project->name,
                (string) $item->value,
                $item->usage_location,
                $item->issued_date?->toDateString(),
                $item->memo,
            ))
            ->all();
    }

    public function createItem(ItemWrite $write): int
    {
        $item = Item::query()->create([
            'project_id' => $write->projectId,
            'name' => $write->name,
            'service' => $write->service,
            'environment' => $write->environment,
            'value' => $write->protectedValue,
            'usage_location' => $write->usageLocation,
            'issued_date' => $write->issuedDate,
            'memo' => $write->memo,
        ]);

        return (int) $item->getKey();
    }

    public function updateItem(int $id, ItemWrite $write): void
    {
        $item = Item::query()->find($id);

        if ($item === null) {
            throw new RuntimeException('Item not found.');
        }

        $item->project_id = $write->projectId;
        $item->name = $write->name;
        $item->service = $write->service;
        $item->environment = $write->environment;
        $item->value = $write->protectedValue;
        $item->usage_location = $write->usageLocation;
        $item->issued_date = $write->issuedDate === null
            ? null
            : new Carbon($write->issuedDate);
        $item->memo = $write->memo;
        $item->save();
    }

    public function deleteItem(int $id): void
    {
        $deleted = Item::query()->whereKey($id)->delete();

        if ($deleted === 0) {
            throw new RuntimeException('Item not found.');
        }
    }
}
