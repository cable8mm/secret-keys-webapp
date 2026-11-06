<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('value');
            $table->string('service')->nullable();
            $table->timestamps();
        });

        Schema::create('infra_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('value');
            $table->string('service')->nullable();
            $table->timestamps();
        });

        $fixed = DB::table('projects')->where('is_fixed', true)->pluck('id', 'name');

        foreach (['Infra' => 'infra_items', 'AI' => 'ai_items'] as $name => $table) {
            $projectId = $fixed[$name] ?? null;

            if ($projectId === null) {
                continue;
            }

            $rows = DB::table('items')->where('project_id', $projectId)->get();

            foreach ($rows as $row) {
                DB::table($table)->insert([
                    'name' => $row->name,
                    'value' => $row->value,
                    'service' => $row->service,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }

            DB::table('items')->where('project_id', $projectId)->delete();
        }

        Schema::rename('items', 'project_items');
    }

    public function down(): void
    {
        Schema::rename('project_items', 'items');

        $fixed = DB::table('projects')->where('is_fixed', true)->pluck('id', 'name');

        foreach (['Infra' => 'infra_items', 'AI' => 'ai_items'] as $name => $table) {
            $projectId = $fixed[$name] ?? null;

            if ($projectId === null) {
                continue;
            }

            $rows = DB::table($table)->get();

            foreach ($rows as $row) {
                DB::table('items')->insert([
                    'project_id' => $projectId,
                    'name' => $row->name,
                    'value' => $row->value,
                    'service' => $row->service,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }
        }

        Schema::dropIfExists('ai_items');
        Schema::dropIfExists('infra_items');
    }
};
