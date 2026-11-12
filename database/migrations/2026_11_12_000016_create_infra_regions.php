<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infra_regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('infra_id')->constrained('infras')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['infra_id', 'name']);
        });

        Schema::table('infra_secrets', function (Blueprint $table) {
            $table->foreignId('region_id')->nullable()->after('infra_id')->constrained('infra_regions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('infra_secrets', function (Blueprint $table) {
            $table->dropForeign(['region_id']);
            $table->dropColumn('region_id');
        });

        Schema::dropIfExists('infra_regions');
    }
};
