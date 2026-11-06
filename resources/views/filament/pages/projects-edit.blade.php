<x-filament-panels::page>
    @if (filled($errorMessage))
        <div class="mb-6" data-element="EL-211" data-error-code="{{ $errorCode }}">
            <x-filament::callout color="danger" icon="heroicon-o-exclamation-triangle" :description="$errorMessage" />
        </div>
    @endif

    <x-filament::section heading="Edit Project">
        <x-filament::fieldset label="Project Name">
            <x-filament::input.wrapper>
                <x-filament::input type="text" wire:model="projectName" data-element="EL-204" />
            </x-filament::input.wrapper>
        </x-filament::fieldset>
        <x-filament::fieldset label="Project Note">
            <x-filament::input.wrapper>
                <x-filament::input type="text" wire:model="projectNote" data-element="EL-214" />
            </x-filament::input.wrapper>
        </x-filament::fieldset>
    </x-filament::section>

    <div class="mt-6">
        <x-filament::button wire:click="updateProject" data-element="EL-205">Save Changes</x-filament::button>
        <a href="{{ \App\Filament\Pages\Projects::getUrl() }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
    </div>
</x-filament-panels::page>
