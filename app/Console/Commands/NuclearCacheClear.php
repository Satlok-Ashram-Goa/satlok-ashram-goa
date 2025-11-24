<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class NuclearCacheClear extends Command
{
    protected $signature = 'cache:nuclear';
    protected $description = 'Nuclear option: Clear ALL caches including Filament component cache';

    public function handle()
    {
        $this->info('🚀 Starting nuclear cache clear...');

        // Clear all Laravel caches
        $this->info('Clearing Laravel caches...');
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('optimize:clear');

        // Manually delete Filament cache directories
        $this->info('Deleting Filament cache directories...');
        $filamentCachePaths = [
            storage_path('framework/cache'),
            storage_path('framework/views'),
            base_path('bootstrap/cache'),
        ];

        foreach ($filamentCachePaths as $path) {
            if (File::exists($path)) {
                $files = File::allFiles($path);
                foreach ($files as $file) {
                    if ($file->getFilename() !== '.gitignore') {
                        File::delete($file->getPathname());
                    }
                }
                $this->info("Cleared: {$path}");
            }
        }

        // Rebuild caches
        $this->info('Rebuilding caches...');
        Artisan::call('config:cache');
        Artisan::call('route:cache');
        Artisan::call('view:cache');

        // Filament optimize
        $this->info('Running Filament optimize...');
        Artisan::call('filament:optimize');

        $this->info('✅ Nuclear cache clear complete!');
        $this->info('Please refresh your browser with Ctrl+Shift+R (or Cmd+Shift+R on Mac)');

        return 0;
    }
}
