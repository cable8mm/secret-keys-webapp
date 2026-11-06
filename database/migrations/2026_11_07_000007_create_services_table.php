<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('icon')->nullable();
            $table->boolean('is_project')->default(true);
            $table->boolean('is_ai')->default(true);
            $table->boolean('is_infra')->default(true);
            $table->timestamps();
        });

        foreach (['GitHub', 'OpenAI', 'Anthropic', 'Gemini', 'Stripe', 'Coolify', 'Cloudflare', 'AWS'] as $name) {
            DB::table('services')->insert([
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (['project_items', 'ai_items', 'infra_items'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('service');
            });

            Schema::table($table, function (Blueprint $table) {
                $table->foreignId('service_id')->nullable()->after('name')->constrained('services')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        foreach (['project_items', 'ai_items', 'infra_items'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['service_id']);
                $table->dropColumn('service_id');
            });

            Schema::table($table, function (Blueprint $table) {
                $table->string('service')->nullable()->after('name');
            });
        }

        Schema::dropIfExists('services');
    }
};
