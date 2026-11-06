<x-filament-panels::page>
    @if (count($formErrors) > 0)
        <div class="mb-6 space-y-2" data-element="EL-118" data-error-codes="{{ implode(',', array_column($formErrors, 'code')) }}">
            @foreach ($formErrors as $error)
                <x-filament::callout color="danger" icon="heroicon-o-exclamation-triangle" :description="$error['message']" />
            @endforeach
        </div>
    @endif

    @if ($itemLoaded || $itemId === null)
        <div class="space-y-6">
            <div class="space-y-4">
                <x-filament::fieldset label="Item Name">
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="itemName" data-element="EL-101" @disabled($loading) />
                    </x-filament::input.wrapper>
                </x-filament::fieldset>
                <x-filament::fieldset label="Project">
                    <select wire:model="projectId" data-element="EL-102" @disabled($loading) class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-white/5">
                        <option value="">Select a Project</option>
                        @foreach ($projects as $project)
                            <option value="{{ $project['id'] }}">{{ $project['name'] }}</option>
                        @endforeach
                    </select>
                </x-filament::fieldset>
                <x-filament::fieldset label="Service">
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="service" data-element="EL-103" list="service-options" @disabled($loading) />
                        <datalist id="service-options">
                            @foreach (\App\Items\ServiceOptions::all() as $option)
                                <option value="{{ $option }}"></option>
                            @endforeach
                        </datalist>
                    </x-filament::input.wrapper>
                </x-filament::fieldset>
                <x-filament::fieldset label="Environment">
                    <select wire:model="environment" data-element="EL-104" @disabled($loading) class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-white/5">
                        <option value="">Select an Environment</option>
                        <option value="live">live</option>
                        <option value="test">test</option>
                        <option value="dev">dev</option>
                        <option value="none">none</option>
                    </select>
                </x-filament::fieldset>
                <x-filament::fieldset label="Kind">
                    <select wire:model="kind" data-element="EL-119" @disabled($loading) class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-white/5">
                        <option value="">Select a Kind</option>
                        <option value="token">token</option>
                        <option value="password">password</option>
                        <option value="pem">pem</option>
                        <option value="other">other</option>
                    </select>
                </x-filament::fieldset>
                <x-filament::fieldset label="Value">
                    <textarea
                        wire:model="value"
                        data-element="EL-105"
                        rows="4"
                        @disabled($loading)
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-white/10 dark:bg-white/5"
                        @if (! $revealed) style="-webkit-text-security: disc; text-security: disc;" @endif
                    >{{ $value }}</textarea>
                    <div class="flex gap-2 mt-1">
                        <x-filament::button color="gray" wire:click="toggleReveal" data-element="EL-106">
                            {{ $revealed ? 'Hide' : 'Show' }}
                        </x-filament::button>
                        <x-filament::button color="gray" wire:click="copyValue" data-element="EL-107">
                            Copy
                        </x-filament::button>
                    </div>
                </x-filament::fieldset>
                <div data-element="EL-108">{{ $fingerprint }}</div>
                <x-filament::fieldset label="Usage Location">
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="usageLocation" data-element="EL-109" @disabled($loading) />
                    </x-filament::input.wrapper>
                </x-filament::fieldset>
                <x-filament::fieldset label="Issued Date">
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model="issuedDate" data-element="EL-110" @disabled($loading) />
                    </x-filament::input.wrapper>
                </x-filament::fieldset>
                <x-filament::fieldset label="Memo">
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="memo" data-element="EL-111" @disabled($loading) />
                    </x-filament::input.wrapper>
                </x-filament::fieldset>
                <x-filament::fieldset label="Tags">
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="tags" data-element="EL-120" @disabled($loading) />
                    </x-filament::input.wrapper>
                </x-filament::fieldset>
                <x-filament::fieldset label="Expiry Date">
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model="expiresAt" data-element="EL-121" @disabled($loading) />
                    </x-filament::input.wrapper>
                </x-filament::fieldset>
            </div>

            <div class="flex gap-2">
                <x-filament::button wire:click="save" data-element="EL-112">Save</x-filament::button>
                <x-filament::button color="gray" wire:click="cancel" data-element="EL-113">Cancel</x-filament::button>
            </div>

            @if ($itemId !== null)
                <div class="mt-4" data-element="EL-114">
                    <x-filament::button color="danger" data-element="EL-114" wire:click="armDelete">Delete Item</x-filament::button>
                </div>

                @if ($deleteArmed)
                    <div class="mt-2 flex gap-2" data-element="EL-115-116 group">
                        <x-filament::button color="danger" wire:click="confirmDelete" data-element="EL-115">Confirm Delete</x-filament::button>
                        <x-filament::button color="gray" wire:click="cancelDelete" data-element="EL-116">Cancel Delete</x-filament::button>
                    </div>
                @endif
            @endif

            @if ($itemId === null && ! $hasUserProjects)
                <div class="mt-4" data-element="EL-117">
                    <x-filament::button color="gray" wire:click="openProjects">Manage Projects</x-filament::button>
                </div>
            @endif
        </div>
    @endif
</x-filament-panels::page>
