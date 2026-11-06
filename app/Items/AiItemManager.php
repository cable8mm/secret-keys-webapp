<?php

namespace App\Items;

use App\Records\SimpleItemRecord;
use App\Records\SimpleItemWrite;

final class AiItemManager extends SimpleItemManager
{
    protected function findRecord(int $id): ?SimpleItemRecord
    {
        return $this->records->findAiItem($id);
    }

    protected function createRecord(SimpleItemWrite $write): int
    {
        return $this->records->createAiItem($write);
    }

    protected function updateRecord(int $id, SimpleItemWrite $write): void
    {
        $this->records->updateAiItem($id, $write);
    }

    protected function deleteRecord(int $id): void
    {
        $this->records->deleteAiItem($id);
    }
}
