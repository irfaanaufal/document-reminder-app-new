<?php

namespace App\Providers;
use App\Models\DocumentReminder;
use App\Policies\DocumentReminderPolicy;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(DocumentReminder::class, DocumentReminderPolicy::class);

        Gate::define('manageDocumentType', function (\App\Models\User $user) {
            return $user->canManageDocumentTypes();
        });
    }
}
