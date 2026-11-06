<?php

namespace App\Items;

use App\Records\ItemWrite;
use App\Records\RecordStore;
use App\Values\CopyTextPreparer;
use App\Values\ValueProtector;
use Throwable;

final class ItemManager
{
    public function __construct(
        private RecordStore $records,
        private ValueProtector $protector,
        private FingerprintDeriver $fingerprint,
    ) {}

    public function create(ItemInput $input): ItemOperationResult
    {
        $errors = $this->validate($input);

        if ($errors !== []) {
            return ItemOperationResult::failure($errors);
        }

        return $this->persist(null, $input);
    }

    public function update(int $id, ItemInput $input): ItemOperationResult
    {
        $errors = $this->validate($input);

        if ($errors !== []) {
            return ItemOperationResult::failure($errors);
        }

        try {
            if ($this->records->findItem($id) === null) {
                return ItemOperationResult::failure([
                    ['code' => 'ERR-007', 'message' => 'The Item could not be saved.'],
                ]);
            }
        } catch (Throwable) {
            return ItemOperationResult::failure([
                ['code' => 'ERR-007', 'message' => 'The Item could not be saved.'],
            ]);
        }

        return $this->persist($id, $input);
    }

    public function load(int $id): ItemLoadResult
    {
        try {
            $record = $this->records->findItem($id);
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
            $record->projectId,
            $record->service ?? '',
            $record->environment ?? '',
            $value,
            $record->usageLocation ?? '',
            $record->issuedDate ?? '',
            $record->memo ?? '',
            $this->fingerprint->derive($value),
            $record->kind ?? '',
        ));
    }

    public function delete(int $id): ItemOperationResult
    {
        try {
            if ($this->records->findItem($id) === null) {
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
            $this->records->deleteItem($id);
        } catch (Throwable) {
            return ItemOperationResult::failure([
                ['code' => 'ERR-007', 'message' => 'The Item could not be saved.'],
            ]);
        }

        return ItemOperationResult::success();
    }

    public function copyText(int $id): ?string
    {
        try {
            $record = $this->records->findItem($id);
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

        return (new CopyTextPreparer)->prepare($value, $record->kind);
    }

    /**
     * @return list<array{code: string, message: string}>
     */
    private function validate(ItemInput $input): array
    {
        $errors = [];

        if (trim($input->name) === '') {
            $errors[] = ['code' => 'ERR-001', 'message' => 'Item Name is required.'];
        }

        if (trim($input->value) === '') {
            $errors[] = ['code' => 'ERR-002', 'message' => 'Value is required.'];
        }

        if ($input->projectId === null) {
            $errors[] = ['code' => 'ERR-003', 'message' => 'Project is required.'];
        } else {
            try {
                if (! $this->records->projectExists($input->projectId)) {
                    $errors[] = ['code' => 'ERR-003', 'message' => 'Project is required.'];
                }
            } catch (Throwable) {
                $errors[] = ['code' => 'ERR-007', 'message' => 'The Item could not be saved.'];
            }
        }

        return $errors;
    }

    private function persist(?int $id, ItemInput $input): ItemOperationResult
    {
        try {
            $write = new ItemWrite(
                (int) $input->projectId,
                $input->name,
                $this->protector->protect($input->value),
                $input->service === '' ? null : $input->service,
                $input->environment === '' ? null : $input->environment,
                $input->usageLocation === '' ? null : $input->usageLocation,
                $input->issuedDate === '' ? null : $input->issuedDate,
                $input->memo === '' ? null : $input->memo,
                $input->kind === '' ? null : $input->kind,
            );

            if ($id === null) {
                $this->records->createItem($write);
            } else {
                $this->records->updateItem($id, $write);
            }
        } catch (Throwable) {
            return ItemOperationResult::failure([
                ['code' => 'ERR-007', 'message' => 'The Item could not be saved.'],
            ]);
        }

        return ItemOperationResult::success();
    }
}
