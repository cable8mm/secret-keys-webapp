<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['secrets', 'ai_items'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropForeign(['service_id']);
                $table->dropColumn('service_id');
            });

            Schema::table($tableName, function (Blueprint $table): void {
                $table->string('manifest_key')->nullable()->after('name');
                $table->string('manifest_version')->nullable()->after('manifest_key');
            });
        }

        Schema::dropIfExists('services');
    }

    public function down(): void
    {
        Schema::create('services', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('icon')->nullable();
            $table->boolean('is_secret')->default(true);
            $table->boolean('is_ai')->default(true);
            $table->timestamps();
        });

        foreach (['secrets', 'ai_items'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropColumn(['manifest_key', 'manifest_version']);
            });

            Schema::table($tableName, function (Blueprint $table): void {
                $table->foreignId('service_id')->nullable()->after('name')->constrained('services')->nullOnDelete();
            });
        }
    }
};
