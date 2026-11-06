<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('projects')->where('is_fixed', true)->delete();

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('is_fixed');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('is_fixed')->default(false)->after('name');
        });

        DB::table('projects')->insert([
            ['name' => 'Infra', 'is_fixed' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'AI', 'is_fixed' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
};
