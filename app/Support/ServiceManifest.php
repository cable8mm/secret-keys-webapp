<?php

namespace App\Support;

use InvalidArgumentException;

final class ServiceManifest
{
    /** @var array<string, mixed> | null */
    private static ?array $manifest = null;

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        if (self::$manifest === null) {
            /** @var mixed $decoded */
            $decoded = json_decode((string) file_get_contents(config_path('service-manifests.json')), true, 512, JSON_THROW_ON_ERROR);

            if (! is_array($decoded)) {
                throw new InvalidArgumentException('Service manifest must be an object.');
            }

            self::validate($decoded);
            self::$manifest = $decoded;
        }

        return self::$manifest;
    }

    /**
     * @param  array<string, mixed>  $manifest
     */
    public static function validate(array $manifest): void
    {
        $kinds = ['token', 'password', 'pem', 'other'];

        foreach ($manifest as $serviceKey => $service) {
            if (! is_string($serviceKey) || ! is_array($service)) {
                throw new InvalidArgumentException('Each service must be an object keyed by its identifier.');
            }

            if (! is_string($service['label'] ?? null) || $service['label'] === '') {
                throw new InvalidArgumentException("Service [{$serviceKey}] needs a label.");
            }

            if (! is_array($service['containers'] ?? null)) {
                throw new InvalidArgumentException("Service [{$serviceKey}] needs containers.");
            }

            foreach ($service['containers'] as $container) {
                if (! in_array($container, ['secrets', 'ai_items'], true)) {
                    throw new InvalidArgumentException("Service [{$serviceKey}] has an unknown container.");
                }
            }

            foreach ($service['sets'] ?? [] as $setName => $versions) {
                if (! is_array($versions) || $versions === []) {
                    throw new InvalidArgumentException("Set [{$serviceKey}/{$setName}] needs at least one version.");
                }

                foreach ($versions as $versionKey => $definition) {
                    if (! is_array($definition)) {
                        throw new InvalidArgumentException("Set [{$serviceKey}/{$setName}] has an invalid version.");
                    }

                    if ((string) ($definition['version'] ?? '') !== (string) $versionKey) {
                        throw new InvalidArgumentException("Set [{$serviceKey}/{$setName}] version key mismatch.");
                    }

                    if (! is_string($definition['label'] ?? null) || $definition['label'] === '') {
                        throw new InvalidArgumentException("Set [{$serviceKey}/{$setName}] needs a label.");
                    }

                    foreach ($definition['fields'] ?? [] as $field) {
                        if (! is_array($field)) {
                            throw new InvalidArgumentException("Set [{$serviceKey}/{$setName}] has an invalid field.");
                        }

                        if (! is_string($field['key'] ?? null) || $field['key'] === '') {
                            throw new InvalidArgumentException("Set [{$serviceKey}/{$setName}] has a field without a key.");
                        }

                        if (! is_string($field['label'] ?? null) || $field['label'] === '') {
                            throw new InvalidArgumentException("Set [{$serviceKey}/{$setName}] has a field without a label.");
                        }

                        if (! is_string($field['purpose'] ?? null) || $field['purpose'] === '') {
                            throw new InvalidArgumentException("Set [{$serviceKey}/{$setName}] has a field without a purpose.");
                        }

                        if (! in_array($field['kind'] ?? null, $kinds, true)) {
                            throw new InvalidArgumentException("Set [{$serviceKey}/{$setName}] has a field with an unknown kind.");
                        }

                        if (array_key_exists('value', $field)) {
                            throw new InvalidArgumentException('The manifest must not contain secret values.');
                        }
                    }
                }
            }
        }
    }

    /**
     * @return array<string, string>
     */
    public static function setOptions(): array
    {
        $options = [];

        foreach (self::all() as $serviceKey => $service) {
            foreach ($service['sets'] ?? [] as $setName => $versions) {
                $latest = (string) max(array_map('intval', array_keys($versions)));
                $options["{$serviceKey}/{$setName}"] = "{$service['label']} — {$versions[$latest]['label']}";
            }
        }

        return $options;
    }

    /**
     * @return array{key: string, service: string, set: string, version: int, label: string, fields: array<int, array<string, mixed>>, customKeys: bool} | null
     */
    public static function definition(string $key, ?int $version = null): ?array
    {
        [$serviceKey, $setName] = array_pad(explode('/', $key, 2), 2, '');

        $service = self::all()[$serviceKey] ?? null;
        $versions = $service['sets'][$setName] ?? null;

        if (! is_array($versions) || $versions === []) {
            return null;
        }

        $version ??= max(array_map('intval', array_keys($versions)));

        $definition = $versions[(string) $version] ?? null;

        if (! is_array($definition)) {
            return null;
        }

        return [
            'key' => $key,
            'service' => $serviceKey,
            'set' => $setName,
            'version' => (int) $definition['version'],
            'label' => (string) $definition['label'],
            'fields' => $definition['fields'] ?? [],
            'customKeys' => (bool) ($definition['customKeys'] ?? false),
        ];
    }
}
