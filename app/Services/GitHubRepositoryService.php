<?php

namespace App\Services;

use Github\AuthMethod;
use Github\Client;
use Github\ResultPager;

class GitHubRepositoryService
{
    /**
     * @var array<string, array{full_name: string, html_url: ?string, description: ?string, archived: bool}>|null
     */
    protected static ?array $cache = null;

    public function isConfigured(): bool
    {
        return filled(config('services.github.token'));
    }

    /**
     * @return array<string, array{full_name: string, html_url: ?string, description: ?string, archived: bool}>
     */
    public function repositories(): array
    {
        if (static::$cache !== null) {
            return static::$cache;
        }

        $token = config('services.github.token');

        if (blank($token)) {
            return static::$cache = [];
        }

        $client = new Client;
        $client->authenticate((string) $token, null, AuthMethod::ACCESS_TOKEN);

        $pager = new ResultPager($client);

        $repositories = $pager->fetchAll(
            $client->currentUser(),
            'repositories',
            ['all', 'full_name', 'asc', null, 'owner,organization_member'],
        );

        return static::$cache = collect($repositories)
            ->filter(fn (array $repository): bool => filled($repository['full_name'] ?? null))
            ->mapWithKeys(fn (array $repository): array => [
                $repository['full_name'] => [
                    'full_name' => $repository['full_name'],
                    'html_url' => $repository['html_url'] ?? null,
                    'description' => $repository['description'] ?? null,
                    'archived' => (bool) ($repository['archived'] ?? false),
                ],
            ])
            ->all();
    }
}
