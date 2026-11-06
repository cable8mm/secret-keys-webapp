<?php

namespace App\Models\Concerns;

use App\Values\ValueProtector;

trait HasSecretValue
{
    public function secretValue(): string
    {
        return app(ValueProtector::class)->unprotect($this->value);
    }

    public function secretKind(): ?string
    {
        return null;
    }
}
