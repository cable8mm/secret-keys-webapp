<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_items', function (Blueprint $table) {
            $table->text('url')->nullable()->after('usage_location');
        });

        DB::table('project_items')->update([
            'url' => DB::raw('"usage_location"'),
        ]);

        Schema::table('project_items', function (Blueprint $table) {
            $table->dropColumn('usage_location');
        });
    }

    public function down(): void
    {
        Schema::table('project_items', function (Blueprint $table) {
            $table->text('usage_location')->nullable()->after('url');
        });

        DB::table('project_items')->update([
            'usage_location' => DB::raw('"url"'),
        ]);

        Schema::table('project_items', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }
};
