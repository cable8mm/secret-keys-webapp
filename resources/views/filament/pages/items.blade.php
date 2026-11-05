<x-filament-panels::page>
    <div class="mb-6">
        <x-filament::button tag="a" :href="\App\Filament\Pages\ItemForm::getUrl()" data-element="EL-006">
            New Item
        </x-filament::button>
    </div>

    @if (filled($errorMessage))
        <div class="mb-6" data-element="EL-010">
            <x-filament::callout color="danger" icon="heroicon-o-exclamation-triangle" :description="$errorMessage" />
        </div>
    @endif

    @if (count($items) > 0)
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
</x-filament-panels::page>
