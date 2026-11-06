<x-filament-panels::page>
    <x-filament::section heading="Project Details">
        <x-filament::fieldset label="Project Name">
            <p>{{ $project->name }}</p>
        </x-filament::fieldset>
    </x-filament::section>

    <div class="mt-6">
        <a href="{{ \App\Filament\Pages\ProjectsEdit::getUrl(['project' => $project->id]) }}" class="text-sm text-primary-600 hover:underline">Edit</a>
        <a href="{{ \App\Filament\Pages\Projects::getUrl() }}" class="text-sm text-gray-600 hover:underline">Back to list</a>
    </div>
</x-filament-panels::page>
