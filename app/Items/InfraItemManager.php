<?php

namespace App\Items;

use App\Records\SimpleItemRecord;
use App\Records\SimpleItemWrite;

final class InfraItemManager extends SimpleItemManager
{
    protected function findRecord(int $id): ?SimpleItemRecord
    {
        return $this->records->findInfraItem($id);
    }

    protected function createRecord(SimpleItemWrite $write): int
    {
        return $this->records->createInfraItem($write);
    }

    protected function updateRecord(int $id, SimpleItemWrite $write): void
    {
        $this->records->updateInfraItem($id, $write);
    }

    protected function deleteRecord(int $id): void
    {
        $this->records->deleteInfraItem($id);
    }
}
