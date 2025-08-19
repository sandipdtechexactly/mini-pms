<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\User\AccountCreated;
use App\Notifications\User\AccountUpdated;
use App\Notifications\User\PasswordUpdated;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     */
    public function creating(User $user): void
    {
        // Set default values or perform actions before user is created
        if (empty($user->password)) {
            $user->password = bcrypt(Str::random(16)); // Temporary password
        }
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        try {
            // Send welcome email with temporary password
            $user->notify(new AccountCreated($user, $user->password));
            
            // Log the user creation
            Log::info("User created: {$user->email}");
            
        } catch (\Exception $e) {
            Log::error("Error in UserObserver@created: " . $e->getMessage());
        }
    }

    /**
     * Handle the User "updating" event.
     */
    public function updating(User $user): void
    {
        // Check if the email was changed
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            // You might want to send an email verification notification here
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        try {
            // Check if the password was updated
            if ($user->wasChanged('password')) {
                $user->notify(new PasswordUpdated());
                Log::info("User password updated: {$user->email}");
            }
            
            // Check if the email was updated
            if ($user->wasChanged('email')) {
                // Send email verification notification
                $user->sendEmailVerificationNotification();
                Log::info("User email updated: {$user->getOriginal('email')} -> {$user->email}");
            }
            
            // Log other updates
            if (count($user->getChanges()) > 0) {
                $user->notify(new AccountUpdated($user));
                Log::info("User updated: {$user->email}", ['changes' => $user->getChanges()]);
            }
            
        } catch (\Exception $e) {
            Log::error("Error in UserObserver@updated: " . $e->getMessage());
        }
    }

    /**
     * Handle the User "deleting" event.
     */
    public function deleting(User $user): void
    {
        // Prevent deletion of admin users
        if ($user->hasRole('admin')) {
            throw new \Exception('Admin users cannot be deleted.');
        }
        
        // You might want to handle related data here
        // For example, revoke API tokens, cancel subscriptions, etc.
        $user->tokens()->delete();
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        Log::info("User deleted: {$user->email}");
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        Log::info("User restored: {$user->email}");
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        Log::info("User permanently deleted: {$user->email}");
    }
    
    /**
     * Handle the User "saving" event.
     */
    public function saving(User $user): void
    {
        // Ensure email is always lowercase
        if ($user->isDirty('email')) {
            $user->email = strtolower($user->email);
        }
    }
    
    /**
     * Handle the User "saved" event.
     */
    public function saved(User $user): void
    {
        // You can perform actions after the user is saved
        // For example, update search indexes, clear caches, etc.
    }
}
