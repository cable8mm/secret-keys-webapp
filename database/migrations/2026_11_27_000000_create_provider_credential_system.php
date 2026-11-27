<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('providers', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->text('definition');
            $table->timestamps();
        });

        Schema::create('credentials', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('provider_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('credential_values');
            $table->timestamps();
        });

        foreach ($this->providers() as $provider) {
            DB::table('providers')->insert([
                'slug' => $provider['id'],
                'name' => $provider['name'],
                'description' => $provider['description'],
                'category' => $provider['category'],
                'definition' => json_encode($provider, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('credentials');
        Schema::dropIfExists('providers');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function providers(): array
    {
        return [
            [
                'schema_version' => 1,
                'id' => 'openai',
                'name' => 'OpenAI',
                'description' => 'OpenAI API credentials',
                'category' => 'ai',
                'credentials' => ['fields' => [
                    ['key' => 'api_key', 'label' => 'API Key', 'type' => 'secret', 'required' => true, 'sensitive' => true],
                ]],
            ],
            [
                'schema_version' => 1,
                'id' => 'aws',
                'name' => 'Amazon Web Services',
                'description' => 'AWS access credentials',
                'category' => 'cloud',
                'credentials' => ['fields' => [
                    ['key' => 'access_key_id', 'label' => 'Access Key ID', 'type' => 'text', 'required' => true, 'sensitive' => false],
                    ['key' => 'secret_access_key', 'label' => 'Secret Access Key', 'type' => 'secret', 'required' => true, 'sensitive' => true],
                    ['key' => 'region', 'label' => 'Region', 'type' => 'text', 'required' => false, 'sensitive' => false, 'placeholder' => 'ap-northeast-2'],
                ]],
            ],
            [
                'schema_version' => 1,
                'id' => 'cloudflare',
                'name' => 'Cloudflare',
                'description' => 'Cloudflare API token',
                'category' => 'cloud',
                'credentials' => ['fields' => [
                    ['key' => 'api_token', 'label' => 'API Token', 'type' => 'secret', 'required' => true, 'sensitive' => true],
                ]],
            ],
            [
                'schema_version' => 1,
                'id' => 'github',
                'name' => 'GitHub',
                'description' => 'GitHub personal access token',
                'category' => 'developer',
                'credentials' => ['fields' => [
                    ['key' => 'personal_access_token', 'label' => 'Personal Access Token', 'type' => 'secret', 'required' => true, 'sensitive' => true],
                ]],
            ],
            [
                'schema_version' => 1,
                'id' => 'github-app',
                'name' => 'GitHub App',
                'description' => 'GitHub App credentials',
                'category' => 'developer',
                'credentials' => ['fields' => [
                    ['key' => 'app_id', 'label' => 'App ID', 'type' => 'text', 'required' => true, 'sensitive' => false],
                    ['key' => 'private_key', 'label' => 'Private Key', 'type' => 'textarea', 'required' => true, 'sensitive' => true, 'multiline' => true],
                    ['key' => 'installation_id', 'label' => 'Installation ID', 'type' => 'text', 'required' => true, 'sensitive' => false],
                ]],
            ],
            [
                'schema_version' => 1,
                'id' => 'stripe',
                'name' => 'Stripe',
                'description' => 'Stripe API keys',
                'category' => 'payments',
                'credentials' => ['fields' => [
                    ['key' => 'secret_key', 'label' => 'Secret Key', 'type' => 'secret', 'required' => true, 'sensitive' => true],
                    ['key' => 'publishable_key', 'label' => 'Publishable Key', 'type' => 'text', 'required' => false, 'sensitive' => false],
                ]],
            ],
        ];
    }
};
