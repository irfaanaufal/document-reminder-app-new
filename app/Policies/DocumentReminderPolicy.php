<?php

namespace App\Policies;

use App\Models\DocumentReminder;
use App\Models\User;

class DocumentReminderPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DocumentReminder $reminder): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DocumentReminder $reminder): bool
    {
        return $user->isAdmin() || $user->id === $reminder->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DocumentReminder $reminder): bool
    {
        return $user->isAdmin() || $user->id === $reminder->user_id;
    }
}
