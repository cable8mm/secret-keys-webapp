<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Models\Project;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $resolved = $this->getRecord();

        if ($resolved instanceof Project && (bool) $resolved->is_fixed) {
            $this->redirect(ProjectResource::getUrl('index'));
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
