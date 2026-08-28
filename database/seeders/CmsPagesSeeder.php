<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class CmsPagesSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('cms:sync-seo');
    }
}
