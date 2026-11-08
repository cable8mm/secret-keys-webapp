<?php

namespace App\Filament\Resources;

use App\Models\AiItem;
use App\Models\InfraItem;
use App\Models\Secret;
use App\Values\ValueProtector;
use Filament\Actions\Action;
use Throwable;

final class SecretCopy
{
    /**
     * Client-side copy for the value field in create/edit forms.
     * Same native pattern as TextInput's copyable(): Alpine reads the
     * displayed textarea, no Livewire roundtrip.
     */
    public static function formCopyAction(): Action
    {
        return Action::make('copyValue')
            ->icon('heroicon-o-clipboard-document')
            ->label('Copy')
            ->alpineClickHandler(<<<'JS'
                window.navigator.clipboard.writeText($el.closest('[data-field-wrapper]').querySelector('textarea').value)
                JS);
    }

    public static function copyAction(): Action
    {
        return Action::make('copy')
            ->iconButton()
            ->icon('heroicon-o-clipboard-document')
            ->action(function (Secret|AiItem|InfraItem $record, $livewire): void {
                try {
                    $text = $record->secretValue();
                } catch (Throwable) {
                    $livewire->dispatch('copy-failed');

                    return;
                }

                $livewire->dispatch('copy-prepared', text: $text);
            });
    }

    public static function decryptForForm(?string $state): ?string
    {
        if ($state === null || $state === '') {
            return $state;
        }

        try {
            return app(ValueProtector::class)->unprotect($state);
        } catch (Throwable) {
            return $state;
        }
    }

    public static function encryptForStorage(?string $state): ?string
    {
        if ($state === null || $state === '') {
            return $state;
        }

        return app(ValueProtector::class)->protect($state);
    }
}
