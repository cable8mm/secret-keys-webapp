<x-filament-panels::page>
    @if (count($formErrors) > 0)
        <div class="mb-6 space-y-2" data-element="EL-118" data-error-codes="{{ implode(',', array_column($formErrors, 'code')) }}">
            @foreach ($formErrors as $error)
                <x-filament::callout color="danger" icon="heroicon-o-exclamation-triangle" :description="$error['message']" />
            @endforeach
        </div>
    @endif

    <div class="space-y-6">
        <div class="space-y-4">
            <x-filament::fieldset label="Item Name">
                <x-filament::input.wrapper>
                    <x-filament::input type="text" wire:model="itemName" data-element="EL-101" />
                </x-filament::input.wrapper>
            </x-filament::fieldset>
            <x-filament::fieldset label="Project">
                <select wire:model="projectId" data-element="EL-102" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-white/5">
                    <option value="">Select a Project</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
                    @endforeach
                </select>
            </x-filament::fieldset>
            <x-filament::fieldset label="Service">
                <x-filament::input.wrapper>
                    <x-filament::input type="text" wire:model="service" data-element="EL-103" />
                </x-filament::input.wrapper>
            </x-filament::fieldset>
            <x-filament::fieldset label="Environment">
                <x-filament::input.wrapper>
                    <x-filament::input type="text" wire:model="environment" data-element="EL-104" />
                </x-filament::input.wrapper>
            </x-filament::fieldset>
            <x-filament::fieldset label="Value">
                <textarea
                    wire:model="value"
                    data-element="EL-105"
                    rows="4"
                    class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-white/5"
                    @if (! $revealed) style="-webkit-text-security: disc; text-security: disc;" @endif
                ></textarea>
                <div class="flex gap-2 mt-1">
                    <x-filament::button color="gray" wire:click="toggleReveal" data-element="EL-106">
                        {{ $revealed ? 'Hide' : 'Show' }}
                    </x-filament::button>
                </div>
            </x-filament::fieldset>
            <div data-element="EL-108"></div>
            <x-filament::fieldset label="Usage Location">
                <x-filament::input.wrapper>
                    <x-filament::input type="text" wire:model="usageLocation" data-element="EL-109" />
                </x-filament::input.wrapper>
            </x-filament::fieldset>
            <x-filament::fieldset label="Issued Date">
                <x-filament::input.wrapper>
                    <x-filament::input type="date" wire:model="issuedDate" data-element="EL-110" />
                </x-filament::input.wrapper>
            </x-filament::fieldset>
            <x-filament::fieldset label="Memo">
                <x-filament::input.wrapper>
                    <x-filament::input type="text" wire:model="memo" data-element="EL-111" />
                </x-filament::input.wrapper>
            </x-filament::fieldset>
        </div>

        <div class="flex gap-2">
            <x-filament::button wire:click="save" data-element="EL-112">Save</x-filament::button>
            <x-filament::button color="gray" wire:click="cancel" data-element="EL-113">Cancel</x-filament::button>
        </div>

        @if ($projects === [])
            <div class="mt-4" data-element="EL-117">
                <x-filament::button color="gray" wire:click="openProjects">Manage Projects</x-filament::button>
            </div>
        @endif
    </div>
</x-filament-panels::page>
