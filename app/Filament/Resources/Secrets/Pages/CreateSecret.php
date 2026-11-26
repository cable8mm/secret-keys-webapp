<?php

namespace App\Filament\Resources\Secrets\Pages;

use App\Filament\Resources\Secrets\SecretResource;
use App\Support\CredentialSet;
use App\Support\ServiceManifest;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSecret extends CreateRecord
{
    protected static string $resource = SecretResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function handleRecordCreation(array $data): Model
    {
        if (filled($data['definition'] ?? null)) {
            $definition = ServiceManifest::definition((string) $data['definition']);
            $values = is_array($data['values'] ?? null) ? $data['values'] : [];

            unset($data['values'], $data['definition']);

            $data['value'] = CredentialSet::encode($values);
            $data['manifest_key'] = $definition['key'] ?? null;
            $data['manifest_version'] = isset($definition['version']) ? (string) $definition['version'] : null;
        }

        return parent::handleRecordCreation($data);
    }
}
