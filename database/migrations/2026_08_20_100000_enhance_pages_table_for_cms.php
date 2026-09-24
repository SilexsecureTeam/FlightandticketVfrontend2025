<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pages')) {
            return;
        }

        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'status')) {
                $table->string('status')->default('published')->after('content');
            }
            if (!Schema::hasColumn('pages', 'menu_location')) {
                $table->string('menu_location')->default('none')->after('status');
            }
            if (!Schema::hasColumn('pages', 'menu_order')) {
                $table->unsignedInteger('menu_order')->default(0)->after('menu_location');
            }
            if (!Schema::hasColumn('pages', 'custom_url')) {
                $table->string('custom_url')->nullable()->after('menu_order');
            }
            if (!Schema::hasColumn('pages', 'is_system')) {
                $table->boolean('is_system')->default(false)->after('custom_url');
            }
            if (!Schema::hasColumn('pages', 'meta_title')) {
                $table->string('meta_title')->nullable()->after('is_system');
            }
            if (!Schema::hasColumn('pages', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (!Schema::hasColumn('pages', 'meta_keywords')) {
                $table->string('meta_keywords')->nullable()->after('meta_description');
            }
            if (!Schema::hasColumn('pages', 'og_image')) {
                $table->string('og_image')->nullable()->after('meta_keywords');
            }
            if (!Schema::hasColumn('pages', 'canonical_url')) {
                $table->string('canonical_url')->nullable()->after('og_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'menu_location',
                'menu_order',
                'custom_url',
                'is_system',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'og_image',
                'canonical_url',
            ]);
        });
    }
};
