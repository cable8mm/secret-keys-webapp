<?php

namespace App\Items;

use App\Records\ItemRecord;
use App\Records\RecordStore;
use App\Support\TextComparison;
use App\Values\ValueProtector;
use Throwable;

final class ItemFinder
{
    public function __construct(
        private RecordStore $records,
        private ValueProtector $protector,
        private FingerprintDeriver $fingerprint,
        private TextComparison $comparison,
    ) {}

    public function list(?string $search = null, ?int $projectId = null, ?string $environment = null): ItemListResult
    {
        try {
            $records = $this->records->allItems();
        } catch (Throwable) {
            return ItemListResult::failure('ERR-008', 'Items could not be loaded.');
        }

        $rows = [];

        foreach ($records as $record) {
            try {
                $value = $this->protector->unprotect($record->protectedValue);
            } catch (Throwable) {
                return ItemListResult::failure('ERR-008', 'Items could not be loaded.');
            }

            $rows[] = new ItemRow(
                $record->id,
                $record->projectId,
                $record->name,
                $record->service,
                $record->environment,
                $record->projectName,
                $this->fingerprint->derive($value),
                $record->usageLocation,
                $record->memo,
            );
        }

        usort(
            $rows,
            fn (ItemRow $left, ItemRow $right): int => $this->compareRows($left, $right),
        );

        if ($projectId !== null) {
            $rows = array_values(array_filter(
                $rows,
                fn (ItemRow $row): bool => $row->projectId === $projectId,
            ));
        }

        if ($environment !== null && $environment !== '') {
            $rows = array_values(array_filter(
                $rows,
                fn (ItemRow $row): bool => ($row->environment ?? '') === $environment,
            ));
        }

        $needle = trim((string) $search);

        if ($needle !== '') {
            $needle = mb_strtolower($needle, 'UTF-8');

            $rows = array_values(array_filter(
                $rows,
                function (ItemRow $row) use ($needle): bool {
                    return $this->contains($row->name, $needle)
                        || $this->contains($row->service ?? '', $needle)
                        || $this->contains($row->projectName, $needle)
                        || $this->contains($row->environment ?? '', $needle)
                        || $this->contains($row->usageLocation ?? '', $needle)
                        || $this->contains($row->memo ?? '', $needle);
                },
            ));
        }

        return ItemListResult::success($rows);
    }

    /**
     * @param  list<ItemRecord>|null  $records
     * @return list<string>
     */
    public function distinctEnvironments(?array $records = null): array
    {
        $all = $records ?? $this->records->allItems();
        $values = [];

        foreach ($all as $record) {
            if (filled($record->environment ?? '')) {
                $values[$record->environment] = true;
            }
        }

        $environments = array_keys($values);

        usort(
            $environments,
            fn (string $left, string $right): int => $this->comparison->compare($left, $right),
        );

        return $environments;
    }

    private function contains(string $haystack, string $needle): bool
    {
        return mb_strpos(mb_strtolower($haystack, 'UTF-8'), $needle, 0, 'UTF-8') !== false;
    }

    private function compareRows(ItemRow $left, ItemRow $right): int
    {
        $projectOrder = $this->comparison->compare($left->projectName, $right->projectName);

        if ($projectOrder !== 0) {
            return $projectOrder;
        }

        return $this->comparison->compare($left->name, $right->name);
    }
}
