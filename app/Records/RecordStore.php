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

    public function projectExists(int $id): bool;

    public function findItem(int $id): ?ItemRecord;

    /**
     * @return list<ItemRecord>
     */
    public function allItems(): array;

    public function createItem(ItemWrite $write): int;

    public function updateItem(int $id, ItemWrite $write): void;

    public function deleteItem(int $id): void;
}
