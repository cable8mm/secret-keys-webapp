<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('secrets', function (Blueprint $table): void {
            $table->foreignId('derived_from_id')
                ->nullable()
                ->constrained('secrets')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('secrets', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('derived_from_id');
        });
    }
};
