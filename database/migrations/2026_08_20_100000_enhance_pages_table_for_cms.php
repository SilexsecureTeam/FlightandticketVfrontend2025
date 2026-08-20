<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->string('status')->default('published')->after('content');
            $table->string('menu_location')->default('none')->after('status');
            $table->unsignedInteger('menu_order')->default(0)->after('menu_location');
            $table->string('custom_url')->nullable()->after('menu_order');
            $table->boolean('is_system')->default(false)->after('custom_url');
            $table->string('meta_title')->nullable()->after('is_system');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->string('og_image')->nullable()->after('meta_keywords');
            $table->string('canonical_url')->nullable()->after('og_image');
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
