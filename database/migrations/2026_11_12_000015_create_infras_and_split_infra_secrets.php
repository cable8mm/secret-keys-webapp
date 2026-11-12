<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('infras', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('note')->nullable();
            $table->boolean('archived')->default(false);
            $table->timestamps();
        });

        Schema::rename('infra_items', 'infra_secrets');

        Schema::table('infra_secrets', function (Blueprint $table) {
            $table->foreignId('infra_id')->nullable()->after('id')->constrained('infras')->restrictOnDelete();
        });

        $services = DB::table('infra_secrets')
            ->join('services', 'services.id', '=', 'infra_secrets.service_id')
            ->select('services.id', 'services.name')
            ->distinct()
            ->orderBy('services.name')
            ->get();

        foreach ($services as $service) {
            $infraId = DB::table('infras')->insertGetId([
                'name' => $service->name,
                'archived' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('infra_secrets')
                ->where('service_id', $service->id)
                ->update(['infra_id' => $infraId]);
        }

        if (DB::table('infra_secrets')->whereNull('infra_id')->exists()) {
            $infraId = DB::table('infras')->insertGetId([
                'name' => 'Uncategorized',
                'archived' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('infra_secrets')->whereNull('infra_id')->update(['infra_id' => $infraId]);
        }

        Schema::table('infra_secrets', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('is_infra');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('is_infra')->default(true)->after('is_ai');
        });

        Schema::table('infra_secrets', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('name')->constrained('services')->nullOnDelete();
        });

        $secrets = DB::table('infra_secrets')
            ->join('infras', 'infras.id', '=', 'infra_secrets.infra_id')
            ->select('infra_secrets.id', 'infras.name')
            ->get();

        foreach ($secrets as $secret) {
            $serviceId = DB::table('services')->where('name', $secret->name)->value('id');

            if ($serviceId === null) {
                $serviceId = DB::table('services')->insertGetId([
                    'name' => $secret->name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('infra_secrets')
                ->where('id', $secret->id)
                ->update(['service_id' => $serviceId]);
        }

        Schema::table('infra_secrets', function (Blueprint $table) {
            $table->dropForeign(['infra_id']);
            $table->dropColumn('infra_id');
        });

        Schema::rename('infra_secrets', 'infra_items');

        Schema::dropIfExists('infras');
    }
};
