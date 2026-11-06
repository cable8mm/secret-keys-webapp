<?php

namespace App\Filament\Resources\Tags\RelationManagers;

use App\Filament\Resources\ProjectItems\ProjectItemResource;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ProjectItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'projectItems';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable(),
                TextColumn::make('service.name')
                    ->searchable(),
                TextColumn::make('environment')
                    ->badge(),
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record): string => ProjectItemResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
