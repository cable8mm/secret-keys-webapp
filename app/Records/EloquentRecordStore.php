<?php

namespace App\Records;

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
}
