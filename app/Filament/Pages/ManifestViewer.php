<?php

namespace App\Filament\Pages;

use App\Support\ServiceManifest;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ManifestViewer extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Data';

    protected static ?string $navigationLabel = 'Manifest';

    protected static ?int $navigationSort = 7;

    public function content(Schema $schema): Schema
    {
        $services = [];

        foreach (ServiceManifest::all() as $serviceKey => $service) {
            $sets = [];

            foreach ($service['sets'] ?? [] as $setName => $versions) {
                foreach ($versions as $versionKey => $definition) {
                    $sets[] = Section::make("{$setName} · v{$versionKey}")
                        ->schema([
                            TextEntry::make('key')
                                ->label('Set key')
                                ->state("{$serviceKey}/{$setName}"),
                            TextEntry::make('label')
                                ->state($definition['label']),
                            TextEntry::make('customKeys')
                                ->label('Custom keys')
                                ->state($definition['customKeys'] ?? false ? 'yes' : 'no'),
                            RepeatableEntry::make('fields')
                                ->state($definition['fields'] ?? [])
                                ->schema([
                                    TextEntry::make('key'),
                                    TextEntry::make('label'),
                                    TextEntry::make('purpose'),
                                    TextEntry::make('kind')
                                        ->badge(),
                                ]),
                        ]);
                }
            }

            $services[] = Section::make($service['label'])
                ->description("Key: {$serviceKey} · Containers: ".implode(', ', $service['containers'] ?? []))
                ->schema($sets);
        }

        return $schema->components($services);
    }
}
