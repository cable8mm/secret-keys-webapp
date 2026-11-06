<x-filament-panels::page>
    @if (filled($errorMessage))
        <div class="mb-6" data-element="EL-211" data-error-code="{{ $errorCode }}">
            <x-filament::callout color="danger" icon="heroicon-o-exclamation-triangle" :description="$errorMessage" />
        </div>
    @endif

    @if (filled($successMessage))
        <div class="mb-6" data-element="EL-211" data-error-code="success">
            <x-filament::callout color="success" icon="heroicon-o-check-circle" :description="$successMessage" />
        </div>
    @endif

    <div class="flex justify-between items-end gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold">Projects</h2>
            @if (! $hasUserProjects)
                <p class="text-sm text-gray-500" data-element="EL-210">No projects yet.</p>
            @endif
        </div>

        <div class="flex items-end gap-2">
            <x-filament::input.wrapper class="w-64">
                <x-filament::input type="text" wire:model="projectName" data-element="EL-201" placeholder="Project Name" />
            </x-filament::input.wrapper>
            <x-filament::button wire:click="createProject" data-element="EL-202">
                Create Project
            </x-filament::button>
        </div>
    </div>

    @if (count($projects) > 0)
    <div data-element="EL-203">
        <x-filament::section>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium">Project Name</th>
                        <th class="px-4 py-2 text-right text-sm font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    @foreach ($projects as $project)
                        <tr class="py-4" wire:key="project-{{ $project['id'] }}">
                            <td class="px-4">{{ $project['name'] }}</td>
                            <td class="px-4 text-right space-x-2">
                                @if (! $project['is_fixed'])
                                    <a href="{{ \App\Filament\Pages\ProjectsEdit::getUrl(['project' => $project['id']]) }}" class="text-sm text-primary-600 hover:underline">Edit</a>
                                    <x-filament::button color="gray" size="sm" wire:click="archiveProject({{ $project['id'] }})">Archive</x-filament::button>
                                    <x-filament::button color="danger" size="sm" wire:click="mountAction('delete', {project: {{ $project['id'] }}})" data-element="EL-206">Delete</x-filament::button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-filament::section>
    </div>
    @endif

    @if (count($archivedProjects) > 0)
    <div class="mt-8">
        <h3 class="text-lg font-semibold mb-2">Archived</h3>
        <x-filament::section>
            <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10">
                <thead>
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium">Project Name</th>
                        <th class="px-4 py-2 text-right text-sm font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    @foreach ($archivedProjects as $project)
                        <tr class="py-4" wire:key="archived-project-{{ $project['id'] }}">
                            <td class="px-4">{{ $project['name'] }}</td>
                            <td class="px-4 text-right space-x-2">
                                <x-filament::button color="gray" size="sm" wire:click="restoreProject({{ $project['id'] }})">Restore</x-filament::button>
                                <x-filament::button color="danger" size="sm" wire:click="mountAction('delete', {project: {{ $project['id'] }}})">Delete</x-filament::button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-filament::section>
    </div>
    @endif
</x-filament-panels::page>
