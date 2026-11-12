<?php

namespace App\Filament\Resources\Infras\RelationManagers;

use App\Models\InfraRegion;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class RegionsRelationManager extends RelationManager
{
    protected static string $relationship = 'regions';

    protected static ?string $title = 'Regions';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(
                        table: InfraRegion::class,
                        column: 'name',
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule, RelationManager $livewire) => $rule->where('infra_id', $livewire->getOwnerRecord()->getKey()),
                    ),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
