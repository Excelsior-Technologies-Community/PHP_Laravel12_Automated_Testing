<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix Windows Apache/XAMPP Symfony Process permission denied on C:\WINDOWS
        $tempPath = storage_path('app/temp');
        if (!File::exists($tempPath)) {
            File::makeDirectory($tempPath, 0755, true);
        }

        putenv("TMP={$tempPath}");
        putenv("TEMP={$tempPath}");
        putenv("TMPDIR={$tempPath}");
        $_ENV['TMP'] = $tempPath;
        $_ENV['TEMP'] = $tempPath;
        $_ENV['TMPDIR'] = $tempPath;
        $_SERVER['TMP'] = $tempPath;
        $_SERVER['TEMP'] = $tempPath;
        $_SERVER['TMPDIR'] = $tempPath;
    }
}
