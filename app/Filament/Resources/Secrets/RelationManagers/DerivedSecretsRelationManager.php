<?php

namespace App\Filament\Resources\Secrets\RelationManagers;

use App\Filament\Resources\SecretCopy;
use App\Filament\Resources\Secrets\Schemas\SecretForm;
use App\Filament\Resources\Secrets\SecretResource;
use App\Models\Secret;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class DerivedSecretsRelationManager extends RelationManager
{
    protected static string $relationship = 'derivedSecrets';

    protected static ?string $title = 'Used by';

    public function isReadOnly(): bool
    {
        return false;
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return SecretForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('folder.name')
                    ->label('Folder')
                    ->searchable(),
                TextColumn::make('service.name')
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->fillForm(fn (): array => $this->getDerivedDefaults()),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record): string => SecretResource::getUrl('view', ['record' => $record])),
                SecretCopy::copyAction(),
            ]);
    }

    /**
     * Pre-fill everything from the parent except name, folder, url, and memo.
     *
     * @return array<string, mixed>
     */
    protected function getDerivedDefaults(): array
    {
        $parent = $this->getOwnerRecord();
        assert($parent instanceof Secret);

        return [
            'derived_from_id' => $parent->getKey(),
            'value' => SecretCopy::decryptForForm($parent->value),
            'service_id' => $parent->service_id,
            'environment' => $parent->environment,
            'kind' => $parent->kind,
            'expires_at' => $parent->expires_at?->format('Y-m-d'),
            'tags' => $parent->tags->pluck('name')->all(),
        ];
    }
}
