<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->text('tags')->nullable()->after('kind');
            $table->date('expires_at')->nullable()->after('issued_date');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->text('note')->nullable()->after('is_fixed');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['tags', 'expires_at']);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};
