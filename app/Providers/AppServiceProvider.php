<?php

namespace App\Providers;

use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Tanggal & waktu tampil dalam Bahasa Indonesia di seluruh aplikasi.
        Carbon::setLocale('id');
    }
}
