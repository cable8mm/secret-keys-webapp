<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('project_items', 'secrets');
        Schema::rename('projects', 'folders');
        Schema::rename('project_item_tag', 'secret_tag');

        // SQLite grammar has no renameColumn support (no doctrine/dbal),
        // so rename columns with raw statements (SQLite >= 3.25).
        // RENAME COLUMN propagates to FK references automatically.
        DB::statement('ALTER TABLE secrets RENAME COLUMN project_id TO folder_id');
        DB::statement('ALTER TABLE secret_tag RENAME COLUMN project_item_id TO secret_id');
        DB::statement('ALTER TABLE services RENAME COLUMN is_project TO is_secret');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE services RENAME COLUMN is_secret TO is_project');
        DB::statement('ALTER TABLE secret_tag RENAME COLUMN secret_id TO project_item_id');
        DB::statement('ALTER TABLE secrets RENAME COLUMN folder_id TO project_id');

        Schema::rename('secret_tag', 'project_item_tag');
        Schema::rename('folders', 'projects');
        Schema::rename('secrets', 'project_items');
    }
};
