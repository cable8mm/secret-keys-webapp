<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('project_item_tag', function (Blueprint $table) {
            $table->foreignId('project_item_id')->constrained('project_items')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->primary(['project_item_id', 'tag_id']);
        });

        foreach (DB::table('project_items')->select('id', 'tags')->get() as $row) {
            if (! is_string($row->tags) || trim($row->tags) === '') {
                continue;
            }

            foreach (explode(',', $row->tags) as $name) {
                $name = trim($name);

                if ($name === '') {
                    continue;
                }

                $tagId = DB::table('tags')->where('name', $name)->value('id');

                if ($tagId === null) {
                    $tagId = DB::table('tags')->insertGetId([
                        'name' => $name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('project_item_tag')->insertOrIgnore([
                    'project_item_id' => $row->id,
                    'tag_id' => $tagId,
                ]);
            }
        }

        Schema::table('project_items', function (Blueprint $table) {
            $table->dropColumn('tags');
        });
    }

    public function down(): void
    {
        Schema::table('project_items', function (Blueprint $table) {
            $table->text('tags')->nullable()->after('kind');
        });

        $namesByItem = [];
        foreach (DB::table('project_item_tag')->join('tags', 'tags.id', '=', 'project_item_tag.tag_id')->select('project_item_id', 'name')->get() as $row) {
            $namesByItem[$row->project_item_id][] = $row->name;
        }

        foreach ($namesByItem as $itemId => $names) {
            DB::table('project_items')->where('id', $itemId)->update([
                'tags' => implode(', ', $names),
            ]);
        }

        Schema::dropIfExists('project_item_tag');
        Schema::dropIfExists('tags');
    }
};
