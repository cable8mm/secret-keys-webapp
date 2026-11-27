<?php

namespace App\Filament\Resources\Credentials\Pages;

use App\Filament\Resources\Credentials\CredentialResource;
use App\Models\Provider;
use App\Services\CredentialValues;
use App\Support\ProviderDefinition;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class EditCredential extends EditRecord
{
    protected static string $resource = CredentialResource::class;

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
        $provider = Provider::find($this->record->provider_id);

        if ($provider !== null) {
            /** @var array<string, mixed> $definition */
            $definition = json_decode((string) $provider->definition, true, 512, JSON_THROW_ON_ERROR);

            $data['values'] = app(CredentialValues::class)->decode($definition, $this->record->credential_values);
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $provider = Provider::findOrFail($data['provider_id']);

        /** @var array<string, mixed> $definition */
        $definition = json_decode((string) $provider->definition, true, 512, JSON_THROW_ON_ERROR);

        $values = is_array($data['values'] ?? null) ? $data['values'] : [];
        $errors = ProviderDefinition::validateValues($definition, $values);

        if ($errors !== []) {
            $messages = [];

            foreach ($errors as $key => $message) {
                $messages["values.{$key}"] = $message;
            }

            throw ValidationException::withMessages($messages);
        }

        $data['credential_values'] = app(CredentialValues::class)->encode($definition, $values);
        unset($data['values']);

        return parent::handleRecordUpdate($record, $data);
    }
}
