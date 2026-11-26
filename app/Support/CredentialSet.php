<?php

namespace App\Support;

use App\Models\AiItem;
use App\Models\Secret;
use App\Values\ValueProtector;
use Throwable;

final class CredentialSet
{
    /**
     * @param  array<string, string>  $values
     */
    public static function encode(array $values): string
    {
        return app(ValueProtector::class)->protect(json_encode($values, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }

    /**
     * @return array<string, string>
     */
    public static function decode(?string $value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        try {
            /** @var mixed $decoded */
            $decoded = json_decode(app(ValueProtector::class)->unprotect($value), true, 512, JSON_THROW_ON_ERROR);

            return is_array($decoded) ? array_map('strval', $decoded) : [];
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * @return array<string, string>
     */
    public static function fieldLabels(Secret|AiItem $record): array
    {
        if ($record->manifest_key === null) {
            return [];
        }

        $definition = ServiceManifest::definition($record->manifest_key, $record->manifest_version === null ? null : (int) $record->manifest_version);

        if ($definition === null) {
            return [];
        }

        if ($definition['customKeys']) {
            $labels = [];

            foreach (array_keys(self::decode($record->value)) as $key) {
                $labels[$key] = $key;
            }

            return $labels;
        }

        $labels = [];

        foreach ($definition['fields'] as $field) {
            $labels[$field['key']] = $field['label'];
        }

        return $labels;
    }
}
