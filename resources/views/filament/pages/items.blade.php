<x-filament-panels::page>
    <div class="mb-6 flex gap-2">
        <x-filament::button tag="a" :href="\App\Filament\Pages\ItemForm::getUrl()" data-element="EL-006">
            New Item
        </x-filament::button>
        <x-filament::button tag="a" :href="\App\Filament\Pages\Projects::getUrl()" data-element="EL-007">
            Projects
        </x-filament::button>
    </div>

    <div class="mb-6">
        <x-filament::input.wrapper>
            <x-filament::input
                type="text"
                wire:model.live="search"
                data-element="EL-001"
                placeholder="Search"
            />
        </x-filament::input.wrapper>
    </div>

    <div class="mb-6 flex gap-4">
        <select wire:model.live="projectFilter" data-element="EL-002" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-white/5">
            <option value="">All Projects</option>
            @foreach ($projects as $project)
                <option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
            @endforeach
        </select>

        <select wire:model.live="environmentFilter" data-element="EL-003" class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-white/5">
            <option value="">All Environments</option>
            @foreach ($environments as $environment)
                <option value="{{ $environment }}">{{ $environment }}</option>
            @endforeach
        </select>
    </div>

    @if (filled($errorMessage))
        <div class="mb-6" data-element="EL-010">
            <x-filament::callout color="danger" icon="heroicon-o-exclamation-triangle" :description="$errorMessage" />
        </div>
    @endif

    @if (! filled($errorMessage))
        @if (count($items) === 0 && $search === '')
            <div data-element="EL-008">
                <x-filament::empty-state heading="No Items yet." :contained="false" />
            </div>
        @elseif (count($items) === 0)
            <div data-element="EL-009">
                <x-filament::empty-state heading="No matching Items." :contained="false" />
            </div>
        @else
            <div data-element="EL-004">
                <x-filament::section>
                    <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-white/10">
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @foreach ($items as $item)
                                <tr data-item-id="{{ $item['id'] }}">
                                    <td class="py-2">
                                        <a data-element="EL-004-row-link" href="{{ \App\Filament\Pages\ItemForm::getUrl(['item' => $item['id']]) }}">{{ $item['name'] }}</a>
                                    </td>
                                    <td class="py-2">{{ $item['service'] }}</td>
                                    <td class="py-2">{{ $item['projectName'] }}</td>
                                    <td class="py-2">{{ $item['environment'] }}</td>
                                    <td class="py-2">{{ $item['fingerprint'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </x-filament::section>
            </div>
        @endif
    @endif
</x-filament-panels::page>
