<?php

namespace App\Records;

use App\Models\Item;
use App\Models\Project;
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

        $project->name = $name;
        $project->save();
    }

    public function projectExists(int $id): bool
    {
        return Project::query()->whereKey($id)->exists();
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
                (string) $item->name,
                $item->service,
                $item->environment,
                (string) $item->project->name,
                (string) $item->value,
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
}
