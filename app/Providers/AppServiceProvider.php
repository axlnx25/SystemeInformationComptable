<?php

namespace App\Providers;

use App\Models\Journal;
use App\Models\Operation;
use App\Policies\JournalPolicy;
use App\Policies\OperationPolicy;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(Journal::class, JournalPolicy::class);
        Gate::policy(Operation::class, OperationPolicy::class);
    }
}
