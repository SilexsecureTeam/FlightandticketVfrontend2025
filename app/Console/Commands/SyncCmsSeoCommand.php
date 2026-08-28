<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;

class SyncCmsSeoCommand extends Command
{
    protected $signature = 'cms:sync-seo';

    protected $description = 'Sync default CMS page SEO settings from config/cms-seo.php';

    public function handle(): int
    {
        $pages = config('cms-seo.pages', []);

        foreach ($pages as $data) {
            Page::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'status' => 'published',
                    'is_system' => true,
                ])
            );

            $this->line('Synced SEO: ' . $data['slug']);
        }

        $this->info('Default CMS page SEO synced from config/cms-seo.php');

        return self::SUCCESS;
    }
}
