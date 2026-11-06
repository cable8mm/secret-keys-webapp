<?php

namespace App\Items;

final class ServiceOptions
{
    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            'GitHub',
            'OpenAI',
            'Anthropic',
            'Gemini',
            'Stripe',
            'Coolify',
            'Cloudflare',
            'AWS',
        ];
    }
}
