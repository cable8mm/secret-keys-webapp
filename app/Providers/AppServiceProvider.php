<?php

namespace App\Providers;

use App\Records\EloquentRecordStore;
use App\Records\RecordStore;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RecordStore::class, EloquentRecordStore::class);
    }

    public function boot(): void
    {
        //
    }
}
