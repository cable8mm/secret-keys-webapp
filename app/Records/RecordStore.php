<?php

namespace App\Records;

interface RecordStore
{
    /**
     * @return list<ProjectSummary>
     */
    public function allProjects(): array;

    public function createProject(string $name, ?string $note = null): ProjectSummary;

    public function renameProject(int $id, string $name, ?string $note = null): void;

    public function deleteProject(int $id): void;

    public function projectExists(int $id): bool;

    public function setProjectArchived(int $id, bool $archived): void;

    public function findItem(int $id): ?ItemRecord;

    /**
     * @return list<ItemRecord>
     */
    public function allItems(): array;

    public function createItem(ItemWrite $write): int;

    public function updateItem(int $id, ItemWrite $write): void;

    public function deleteItem(int $id): void;

    public function findAiItem(int $id): ?SimpleItemRecord;

    /**
     * @return list<SimpleItemRecord>
     */
    public function allAiItems(): array;

    public function createAiItem(SimpleItemWrite $write): int;

    public function updateAiItem(int $id, SimpleItemWrite $write): void;

    public function deleteAiItem(int $id): void;

    public function findInfraItem(int $id): ?SimpleItemRecord;

    /**
     * @return list<SimpleItemRecord>
     */
    public function allInfraItems(): array;

    public function createInfraItem(SimpleItemWrite $write): int;

    public function updateInfraItem(int $id, SimpleItemWrite $write): void;

    public function deleteInfraItem(int $id): void;
}
