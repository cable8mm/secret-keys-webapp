<?php

namespace App\Filament\Resources;

use App\Items\FingerprintDeriver;
use App\Models\AiItem;
use App\Models\InfraItem;
use App\Models\ProjectItem;
use App\Values\CopyTextPreparer;
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
                    $value = $record->secretValue();
                } catch (Throwable) {
                    $livewire->dispatch('copy-failed');

                    return;
                }

                $kind = $record->secretKind();
                $text = (new CopyTextPreparer)->prepare($value, $kind ?: null);

                $livewire->dispatch('copy-prepared', text: $text);
            });
    }

    public static function fingerprint(ProjectItem|AiItem|InfraItem $record): string
    {
        try {
            $value = $record->secretValue();
        } catch (Throwable) {
            return '';
        }

        return app(FingerprintDeriver::class)->derive($value);
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
