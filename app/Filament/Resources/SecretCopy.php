<?php

namespace App\Filament\Resources;

use App\Models\AiItem;
use App\Models\InfraItem;
use App\Models\ProjectItem;
use App\Values\ValueProtector;
use Filament\Actions\Action;
use Throwable;

final class SecretCopy
{
    public static function copyAction(): Action
    {
        return Action::make('copy')
            ->iconButton()
            ->icon('heroicon-o-clipboard-document')
            ->action(function (ProjectItem|AiItem|InfraItem $record, $livewire): void {
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
