<x-filament-panels::page>
    <div class="mb-6">
        <x-filament::button
            color="gray"
            icon="heroicon-m-arrow-left"
            wire:click="back"
            data-element="EL-209"
        >
            Back to Items
        </x-filament::button>
    </div>

    @if (filled($errorMessage))
        <div class="mb-6" data-element="EL-211" data-error-code="{{ $errorCode }}">
            <x-filament::callout
                color="danger"
                icon="heroicon-o-exclamation-triangle"
                :description="$errorMessage"
            />
        </div>
    @endif

    <x-filament::section>
        <div class="flex items-end gap-4">
            <div class="flex-1">
                <x-filament::fieldset label="Project Name">
                    <x-filament::input.wrapper>
                        <x-filament::input
                            id="project-name"
                            type="text"
                            wire:model="projectName"
                            data-element="EL-201"
                        />
                    </x-filament::input.wrapper>
                </x-filament::fieldset>
            </div>

            <x-filament::button wire:click="createProject" data-element="EL-202">
                Create Project
            </x-filament::button>
        </div>
    </x-filament::section>

    @if ($projectsVisible)
        <div class="mt-6">
            @if (count($projects) === 0)
                <div data-element="EL-210">
                    <x-filament::empty-state heading="No Projects yet." :contained="false" />
                </div>
            @else
                <div data-element="EL-203">
                    <x-filament::section>
                        <ul class="divide-y divide-gray-200 dark:divide-white/10">
                            @foreach ($projects as $project)
                                <li class="py-3 text-sm" wire:key="project-{{ $project['id'] }}">
                                    {{ $project['name'] }}
                                </li>
                            @endforeach
                        </ul>
                    </x-filament::section>
                </div>
            @endif
        </div>
    @endif
</x-filament-panels::page>
