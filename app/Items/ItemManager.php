<?php

namespace App\Items;

use App\Records\ItemWrite;
use App\Records\RecordStore;
use App\Values\ValueProtector;
use Throwable;

final class ItemManager
{
    public function __construct(
        private RecordStore $records,
        private ValueProtector $protector,
    ) {}

    public function create(ItemInput $input): ItemOperationResult
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
                return ItemOperationResult::failure([
                    ['code' => 'ERR-007', 'message' => 'The Item could not be saved.'],
                ]);
            }
        }

        if ($errors !== []) {
            return ItemOperationResult::failure($errors);
        }

        try {
            $this->records->createItem(new ItemWrite(
                $input->projectId,
                $input->name,
                $this->protector->protect($input->value),
                $this->blankOrNull($input->service),
                $this->blankOrNull($input->environment),
                $this->blankOrNull($input->usageLocation),
                $this->blankOrNullDate($input->issuedDate),
                $this->blankOrNull($input->memo),
            ));
        } catch (Throwable) {
            return ItemOperationResult::failure([
                ['code' => 'ERR-007', 'message' => 'The Item could not be saved.'],
            ]);
        }

        return ItemOperationResult::success();
    }

    private function blankOrNull(string $value): ?string
    {
        return $value === '' ? null : $value;
    }

    private function blankOrNullDate(string $value): ?string
    {
        return $value === '' ? null : $value;
    }
}
