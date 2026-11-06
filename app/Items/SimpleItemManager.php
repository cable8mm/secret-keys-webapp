<?php

namespace App\Items;

use App\Records\RecordStore;
use App\Records\SimpleItemRecord;
use App\Records\SimpleItemWrite;
use App\Values\CopyTextPreparer;
use App\Values\ValueProtector;
use Throwable;

abstract class SimpleItemManager
{
    public function __construct(
        protected RecordStore $records,
        protected ValueProtector $protector,
        protected FingerprintDeriver $fingerprint,
    ) {}

    abstract protected function findRecord(int $id): ?SimpleItemRecord;

    abstract protected function createRecord(SimpleItemWrite $write): int;

    abstract protected function updateRecord(int $id, SimpleItemWrite $write): void;

    abstract protected function deleteRecord(int $id): void;

    /**
     * @return list<array{code: string, message: string}>
     */
    public function validate(string $name, string $value): array
    {
        $errors = [];

        if (trim($name) === '') {
            $errors[] = ['code' => 'ERR-001', 'message' => 'Item Name is required.'];
        }

        if (trim($value) === '') {
            $errors[] = ['code' => 'ERR-002', 'message' => 'Value is required.'];
        }

        return $errors;
    }

    public function create(string $name, ?string $service, string $value): ItemOperationResult
    {
        $errors = $this->validate($name, $value);

        if ($errors !== []) {
            return ItemOperationResult::failure($errors);
        }

        try {
            $this->createRecord(new SimpleItemWrite(
                $name,
                $service === '' || $service === null ? null : $service,
                $this->protector->protect($value),
            ));
        } catch (Throwable) {
            return ItemOperationResult::failure([
                ['code' => 'ERR-007', 'message' => 'The Item could not be saved.'],
            ]);
        }

        return ItemOperationResult::success();
    }

    public function update(int $id, string $name, ?string $service, string $value): ItemOperationResult
    {
        $errors = $this->validate($name, $value);

        if ($errors !== []) {
            return ItemOperationResult::failure($errors);
        }

        try {
            if ($this->findRecord($id) === null) {
                return ItemOperationResult::failure([
                    ['code' => 'ERR-007', 'message' => 'The Item could not be saved.'],
                ]);
            }
        } catch (Throwable) {
            return ItemOperationResult::failure([
                ['code' => 'ERR-007', 'message' => 'The Item could not be saved.'],
            ]);
        }

        try {
            $this->updateRecord($id, new SimpleItemWrite(
                $name,
                $service === '' || $service === null ? null : $service,
                $this->protector->protect($value),
            ));
        } catch (Throwable) {
            return ItemOperationResult::failure([
                ['code' => 'ERR-007', 'message' => 'The Item could not be saved.'],
            ]);
        }

        return ItemOperationResult::success();
    }

    public function delete(int $id): ItemOperationResult
    {
        try {
            if ($this->findRecord($id) === null) {
                return ItemOperationResult::failure([
                    ['code' => 'ERR-007', 'message' => 'The Item could not be saved.'],
                ]);
            }
        } catch (Throwable) {
            return ItemOperationResult::failure([
                ['code' => 'ERR-007', 'message' => 'The Item could not be saved.'],
            ]);
        }

        try {
            $this->deleteRecord($id);
        } catch (Throwable) {
            return ItemOperationResult::failure([
                ['code' => 'ERR-007', 'message' => 'The Item could not be saved.'],
            ]);
        }

        return ItemOperationResult::success();
    }

    public function load(int $id): ItemLoadResult
    {
        try {
            $record = $this->findRecord($id);
        } catch (Throwable) {
            return ItemLoadResult::failure('ERR-008', 'Items could not be loaded.');
        }

        if ($record === null) {
            return ItemLoadResult::failure('ERR-008', 'Items could not be loaded.');
        }

        try {
            $value = $this->protector->unprotect($record->protectedValue);
        } catch (Throwable) {
            return ItemLoadResult::failure('ERR-008', 'Items could not be loaded.');
        }

        return ItemLoadResult::success(new ItemDetail(
            $record->id,
            $record->name,
            null,
            $record->service ?? '',
            '',
            $value,
            '',
            '',
            '',
            $this->fingerprint->derive($value),
        ));
    }

    public function copyText(int $id): ?string
    {
        try {
            $record = $this->findRecord($id);
        } catch (Throwable) {
            return null;
        }

        if ($record === null) {
            return null;
        }

        try {
            $value = $this->protector->unprotect($record->protectedValue);
        } catch (Throwable) {
            return null;
        }

        return (new CopyTextPreparer)->prepare($value);
    }
}
