<?php

namespace App\Filament\Resources\Providers;

use App\Filament\Resources\Providers\Pages\CreateProvider;
use App\Filament\Resources\Providers\Pages\EditProvider;
use App\Filament\Resources\Providers\Pages\ListProviders;
use App\Filament\Resources\Providers\Pages\ViewProvider;
use App\Models\Provider;
use App\Support\ProviderDefinition;
use BackedEnum;
use Closure;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Throwable;

class ProviderResource extends Resource
{
    protected static ?string $model = Provider::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;

    protected static string|\UnitEnum|null $navigationGroup = 'Data';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required()
                    ->unique(table: Provider::class, column: 'slug', ignoreRecord: true)
                    ->helperText('Lowercase identifier used in the definition.'),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->rows(2),
                TextInput::make('category'),
                Textarea::make('definition')
                    ->label('Definition (JSON)')
                    ->required()
                    ->rows(14)
                    ->columnSpanFull()
                    ->helperText('Defines schema_version, id, name, credentials.fields, and field metadata.')
                    ->rules([
                        fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                            try {
                                /** @var mixed $decoded */
                                $decoded = json_decode((string) $value, true, 512, JSON_THROW_ON_ERROR);

                                if (! is_array($decoded)) {
                                    $fail('The definition must be a JSON object.');

                                    return;
                                }

                                ProviderDefinition::validate($decoded);
                            } catch (Throwable $exception) {
                                $fail($exception->getMessage());
                            }
                        },
                    ]),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name'),
                TextEntry::make('slug'),
                TextEntry::make('category'),
                TextEntry::make('description'),
                TextEntry::make('definition')
                    ->label('Definition')
                    ->getStateUsing(fn (Provider $record): string => json_encode($record->definition, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                    ->extraAttributes(['style' => 'white-space: pre-wrap'])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('category')
                    ->badge()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProviders::route('/'),
            'create' => CreateProvider::route('/create'),
            'view' => ViewProvider::route('/{record}'),
            'edit' => EditProvider::route('/{record}/edit'),
        ];
    }
}
