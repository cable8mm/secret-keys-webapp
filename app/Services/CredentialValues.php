<?php

namespace App\Services;

use App\Support\ProviderDefinition;
use App\Values\ValueProtector;
use Throwable;

final class CredentialValues
{
    public function __construct(private readonly ValueProtector $protector) {}

    /**
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $values
     */
    public function encode(array $definition, array $values): string
    {
        $sensitive = ProviderDefinition::sensitiveKeys($definition);
        $stored = [];

        foreach ($values as $key => $value) {
            $text = (string) $value;

            $stored[$key] = in_array($key, $sensitive, true) && $text !== ''
                ? $this->protector->protect($text)
                : $text;
        }

        return json_encode($stored, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param  array<string, mixed>  $definition
     * @return array<string, string>
     */
    public function decode(array $definition, ?string $json): array
    {
        if ($json === null || $json === '') {
            return [];
        }

        /** @var mixed $decoded */
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($decoded)) {
            return [];
        }

        $sensitive = ProviderDefinition::sensitiveKeys($definition);
        $values = [];

        foreach ($decoded as $key => $value) {
            if (in_array($key, $sensitive, true) && is_string($value) && $value !== '') {
                try {
                    $value = $this->protector->unprotect($value);
                } catch (Throwable) {
                    $value = '';
                }
            }

            $values[$key] = (string) $value;
        }

        return $values;
    }
}
