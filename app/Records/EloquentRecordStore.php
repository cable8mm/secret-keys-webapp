<?php

namespace App\Records;

use App\Models\AiItem;
use App\Models\InfraItem;
use App\Models\Project;
use App\Models\ProjectItem;
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
                (bool) $project->archived,
            ))
            ->all();
    }

    public function createProject(string $name, ?string $note = null): ProjectSummary
    {
        $project = Project::query()->create([
            'name' => $name,
            'note' => $note,
        ]);

        return new ProjectSummary(
            (int) $project->getKey(),
            (string) $project->name,
        );
    }

    public function renameProject(int $id, string $name, ?string $note = null): void
    {
        $project = Project::query()->find($id);

        if ($project === null) {
            throw new RuntimeException('Project not found.');
        }

        if ((bool) $project->is_fixed) {
            throw new ProjectIsFixed('Project is fixed.');
        }

        $project->name = $name;

        if ($note !== null) {
            $project->note = $note === '' ? null : $note;
        }

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

    public function setProjectArchived(int $id, bool $archived): void
    {
        $project = Project::query()->find($id);

        if ($project === null) {
            throw new RuntimeException('Project not found.');
        }

        if ((bool) $project->is_fixed) {
            throw new ProjectIsFixed('Project is fixed.');
        }

        $project->archived = $archived;
        $project->save();
    }

    public function findItem(int $id): ?ItemRecord
    {
        $item = ProjectItem::query()->with('project')->find($id);

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
            $item->kind,
            $item->tags,
            $item->expires_at?->toDateString(),
        );
    }

    /**
     * @return list<ItemRecord>
     */
    public function allItems(): array
    {
        return ProjectItem::query()
            ->with('project')
            ->get()
            ->map(fn (ProjectItem $item): ItemRecord => new ItemRecord(
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
                $item->kind,
                $item->tags,
                $item->expires_at?->toDateString(),
            ))
            ->all();
    }

    public function createItem(ItemWrite $write): int
    {
        $item = ProjectItem::query()->create([
            'project_id' => $write->projectId,
            'name' => $write->name,
            'service' => $write->service,
            'environment' => $write->environment,
            'kind' => $write->kind,
            'value' => $write->protectedValue,
            'tags' => $write->tags,
            'usage_location' => $write->usageLocation,
            'issued_date' => $write->issuedDate,
            'expires_at' => $write->expiresAt,
            'memo' => $write->memo,
        ]);

        return (int) $item->getKey();
    }

    public function updateItem(int $id, ItemWrite $write): void
    {
        $item = ProjectItem::query()->find($id);

        if ($item === null) {
            throw new RuntimeException('Item not found.');
        }

        $item->project_id = $write->projectId;
        $item->name = $write->name;
        $item->service = $write->service;
        $item->environment = $write->environment;
        $item->kind = $write->kind;
        $item->tags = $write->tags;
        $item->value = $write->protectedValue;
        $item->usage_location = $write->usageLocation;
        $item->issued_date = $write->issuedDate === null
            ? null
            : new Carbon($write->issuedDate);
        $item->expires_at = $write->expiresAt === null
            ? null
            : new Carbon($write->expiresAt);
        $item->memo = $write->memo;
        $item->save();
    }

    public function deleteItem(int $id): void
    {
        $deleted = ProjectItem::query()->whereKey($id)->delete();

        if ($deleted === 0) {
            throw new RuntimeException('Item not found.');
        }
    }

    public function findAiItem(int $id): ?SimpleItemRecord
    {
        return $this->findSimpleItem(AiItem::class, $id);
    }

    public function allAiItems(): array
    {
        return $this->allSimpleItems(AiItem::class);
    }

    public function createAiItem(SimpleItemWrite $write): int
    {
        return $this->createSimpleItem(AiItem::class, $write);
    }

    public function updateAiItem(int $id, SimpleItemWrite $write): void
    {
        $this->updateSimpleItem(AiItem::class, $id, $write);
    }

    public function deleteAiItem(int $id): void
    {
        $this->deleteSimpleItem(AiItem::class, $id);
    }

    public function findInfraItem(int $id): ?SimpleItemRecord
    {
        return $this->findSimpleItem(InfraItem::class, $id);
    }

    public function allInfraItems(): array
    {
        return $this->allSimpleItems(InfraItem::class);
    }

    public function createInfraItem(SimpleItemWrite $write): int
    {
        return $this->createSimpleItem(InfraItem::class, $write);
    }

    public function updateInfraItem(int $id, SimpleItemWrite $write): void
    {
        $this->updateSimpleItem(InfraItem::class, $id, $write);
    }

    public function deleteInfraItem(int $id): void
    {
        $this->deleteSimpleItem(InfraItem::class, $id);
    }

    /**
     * @param  class-string<AiItem|InfraItem>  $model
     */
    private function findSimpleItem(string $model, int $id): ?SimpleItemRecord
    {
        $item = $model::query()->find($id);

        if ($item === null) {
            return null;
        }

        return new SimpleItemRecord(
            (int) $item->getKey(),
            (string) $item->name,
            $item->service,
            (string) $item->value,
        );
    }

    /**
     * @param  class-string<AiItem|InfraItem>  $model
     * @return list<SimpleItemRecord>
     */
    private function allSimpleItems(string $model): array
    {
        return $model::query()
            ->get()
            ->map(fn ($item): SimpleItemRecord => new SimpleItemRecord(
                (int) $item->getKey(),
                (string) $item->name,
                $item->service,
                (string) $item->value,
            ))
            ->all();
    }

    /**
     * @param  class-string<AiItem|InfraItem>  $model
     */
    private function createSimpleItem(string $model, SimpleItemWrite $write): int
    {
        $item = $model::query()->create([
            'name' => $write->name,
            'service' => $write->service,
            'value' => $write->protectedValue,
        ]);

        return (int) $item->getKey();
    }

    /**
     * @param  class-string<AiItem|InfraItem>  $model
     */
    private function updateSimpleItem(string $model, int $id, SimpleItemWrite $write): void
    {
        $item = $model::query()->find($id);

        if ($item === null) {
            throw new RuntimeException('Item not found.');
        }

        $item->name = $write->name;
        $item->service = $write->service;
        $item->value = $write->protectedValue;
        $item->save();
    }

    /**
     * @param  class-string<AiItem|InfraItem>  $model
     */
    private function deleteSimpleItem(string $model, int $id): void
    {
        $deleted = $model::query()->whereKey($id)->delete();

        if ($deleted === 0) {
            throw new RuntimeException('Item not found.');
        }
    }
}
