<?php

namespace App\Support;

use InvalidArgumentException;

final class ProviderDefinition
{
    /** @var list<string> */
    public const TYPES = ['text', 'secret', 'textarea', 'url', 'email', 'number', 'select'];

    /**
     * @param  array<string, mixed>  $definition
     */
    public static function validate(array $definition): void
    {
        $id = is_string($definition['id'] ?? null) ? $definition['id'] : 'unknown';

        if (! is_int($definition['schema_version'] ?? null) || $definition['schema_version'] < 1) {
            throw new InvalidArgumentException("Provider [{$id}]: schema_version must be a positive integer.");
        }

        if ($id === 'unknown' || preg_match('/^[a-z0-9-]+$/', $id) !== 1) {
            throw new InvalidArgumentException('Provider id must be a lowercase slug.');
        }

        if (! is_string($definition['name'] ?? null) || trim($definition['name']) === '') {
            throw new InvalidArgumentException("Provider [{$id}]: name is required.");
        }

        foreach (['description', 'category'] as $property) {
            if (array_key_exists($property, $definition) && $definition[$property] !== null && ! is_string($definition[$property])) {
                throw new InvalidArgumentException("Provider [{$id}]: {$property} must be a string.");
            }
        }

        $fields = $definition['credentials']['fields'] ?? null;

        if (! is_array($fields) || $fields === []) {
            throw new InvalidArgumentException("Provider [{$id}]: credentials.fields must not be empty.");
        }

        $seen = [];

        foreach ($fields as $field) {
            if (! is_array($field)) {
                throw new InvalidArgumentException("Provider [{$id}]: each field must be an object.");
            }

            $key = $field['key'] ?? null;

            if (! is_string($key) || preg_match('/^[a-z0-9_]+$/', $key) !== 1) {
                throw new InvalidArgumentException("Provider [{$id}]: field key must be a lowercase snake_case identifier.");
            }

            if (isset($seen[$key])) {
                throw new InvalidArgumentException("Provider [{$id}]: duplicate field key [{$key}].");
            }

            $seen[$key] = true;

            if (! is_string($field['label'] ?? null) || trim($field['label']) === '') {
                throw new InvalidArgumentException("Provider [{$id}]: field [{$key}] needs a label.");
            }

            if (! in_array($field['type'] ?? null, self::TYPES, true)) {
                throw new InvalidArgumentException("Provider [{$id}]: field [{$key}] has an unknown type.");
            }

            if (! is_bool($field['required'] ?? null)) {
                throw new InvalidArgumentException("Provider [{$id}]: field [{$key}] needs a boolean required flag.");
            }

            if (! is_bool($field['sensitive'] ?? null)) {
                throw new InvalidArgumentException("Provider [{$id}]: field [{$key}] needs a boolean sensitive flag.");
            }

            foreach (['description', 'placeholder', 'default', 'group'] as $property) {
                if (array_key_exists($property, $field) && ! is_string($field[$property])) {
                    throw new InvalidArgumentException("Provider [{$id}]: field [{$key}] {$property} must be a string.");
                }
            }

            if (array_key_exists('multiline', $field) && ! is_bool($field['multiline'])) {
                throw new InvalidArgumentException("Provider [{$id}]: field [{$key}] multiline must be a boolean.");
            }

            if (array_key_exists('validation', $field)) {
                if (! is_array($field['validation'])) {
                    throw new InvalidArgumentException("Provider [{$id}]: field [{$key}] validation must be an object.");
                }

                foreach (['min', 'max'] as $rule) {
                    if (array_key_exists($rule, $field['validation']) && ! is_int($field['validation'][$rule])) {
                        throw new InvalidArgumentException("Provider [{$id}]: field [{$key}] {$rule} must be an integer.");
                    }
                }

                if (array_key_exists('pattern', $field['validation'])) {
                    if (! is_string($field['validation']['pattern']) || @preg_match($field['validation']['pattern'], '') === false) {
                        throw new InvalidArgumentException("Provider [{$id}]: field [{$key}] has an invalid pattern.");
                    }
                }
            }

            if (($field['type'] ?? null) === 'select') {
                $options = $field['options'] ?? null;

                if (! is_array($options) || $options === []) {
                    throw new InvalidArgumentException("Provider [{$id}]: select field [{$key}] needs options.");
                }
            }
        }
    }

    /**
     * @param  array<string, mixed>  $definition
     * @return array<int, array<string, mixed>>
     */
    public static function fields(array $definition): array
    {
        /** @var array<int, array<string, mixed>> $fields */
        $fields = $definition['credentials']['fields'] ?? [];

        return $fields;
    }

    /**
     * @param  array<string, mixed>  $definition
     * @return array<int, string>
     */
    public static function sensitiveKeys(array $definition): array
    {
        $keys = [];

        foreach (self::fields($definition) as $field) {
            if ($field['sensitive'] === true) {
                $keys[] = $field['key'];
            }
        }

        return $keys;
    }

    /**
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $values
     * @return array<string, string>
     */
    public static function validateValues(array $definition, array $values): array
    {
        $errors = [];

        foreach (self::fields($definition) as $field) {
            $key = $field['key'];
            $value = $values[$key] ?? null;

            if ($value === null || $value === '') {
                if ($field['required'] === true) {
                    $errors[$key] = "{$field['label']} is required.";
                }

                continue;
            }

            if (! is_string($value)) {
                $errors[$key] = "{$field['label']} must be text.";

                continue;
            }

            $type = $field['type'];

            if ($type === 'url' && filter_var($value, FILTER_VALIDATE_URL) === false) {
                $errors[$key] = "{$field['label']} must be a valid URL.";
            }

            if ($type === 'email' && filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                $errors[$key] = "{$field['label']} must be a valid email address.";
            }

            if ($type === 'number' && ! is_numeric($value)) {
                $errors[$key] = "{$field['label']} must be a number.";
            }

            if ($type === 'select' && ! array_key_exists($value, $field['options'] ?? [])) {
                $errors[$key] = "{$field['label']} is not a valid option.";
            }

            $validation = $field['validation'] ?? [];

            if (isset($validation['min']) && mb_strlen($value) < $validation['min']) {
                $errors[$key] = "{$field['label']} must be at least {$validation['min']} characters.";
            }

            if (isset($validation['max']) && mb_strlen($value) > $validation['max']) {
                $errors[$key] = "{$field['label']} must be at most {$validation['max']} characters.";
            }

            if (isset($validation['pattern']) && preg_match($validation['pattern'], $value) !== 1) {
                $errors[$key] = "{$field['label']} has an invalid format.";
            }
        }

        return $errors;
    }
}
