<?php

namespace App\Filament\Resources\AiItems\Pages;

use App\Filament\Resources\AiItems\AiItemResource;
use App\Support\CredentialSet;
use App\Support\ServiceManifest;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAiItem extends EditRecord
{
    protected static string $resource = AiItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if ($this->record->manifest_key !== null) {
            $data['definition'] = $this->record->manifest_key;
            $data['values'] = CredentialSet::decode($this->record->value);
            unset($data['value']);
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (filled($data['definition'] ?? null)) {
            $definition = ServiceManifest::definition((string) $data['definition']);
            $values = is_array($data['values'] ?? null) ? $data['values'] : [];

            unset($data['values'], $data['definition']);

            $data['value'] = CredentialSet::encode($values);
            $data['manifest_key'] = $definition['key'] ?? $record->manifest_key;
            $data['manifest_version'] = isset($definition['version']) ? (string) $definition['version'] : $record->manifest_version;
        }

        return parent::handleRecordUpdate($record, $data);
    }
}
