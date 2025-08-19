<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

/**
 * Trait LogsActivity
 * 
 * Provides functionality for logging model activity.
 */
trait LogsActivity
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            $model->logActivity('created');
        });

        static::updated(function ($model) {
            $model->logActivity('updated');
        });

        static::deleted(function ($model) {
            $model->logActivity('deleted');
        });
    }

    /**
     * Log an activity for the model.
     *
     * @param string $event
     * @param string|null $description
     * @return void
     */
    public function logActivity(string $event, ?string $description = null): void
    {
        $log = [
            'event' => $event,
            'model' => get_class($this),
            'model_id' => $this->getKey(),
            'changes' => $this->getChangesForLog($event),
            'description' => $description ?: $this->getActivityDescription($event),
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        $this->saveActivityLog($log);
    }

    /**
     * Save the activity log.
     *
     * @param array $logData
     * @return void
     */
    protected function saveActivityLog(array $logData): void
    {
        if (config('logging.log_activity', false)) {
            // You can customize where to store the logs
            // For example, in the database or in log files
            $this->logToDatabase($logData);
            // Or log to file
            // $this->logToFile($logData);
        }
    }

    /**
     * Log activity to the database.
     *
     * @param array $logData
     * @return void
     */
    protected function logToDatabase(array $logData): void
    {
        // Make sure the activity_logs table exists
        if (\Schema::hasTable('activity_logs')) {
            \DB::table('activity_logs')->insert($logData);
        }
    }

    /**
     * Log activity to a file.
     *
     * @param array $logData
     * @return void
     */
    protected function logToFile(array $logData): void
    {
        $channel = config('logging.activity_channel', 'daily');
        
        Log::channel($channel)->info('Activity Log', $logData);
    }

    /**
     * Get the description for the activity.
     *
     * @param string $event
     * @return string
     */
    protected function getActivityDescription(string $event): string
    {
        $modelName = class_basename($this);
        
        return sprintf(
            '%s %s %s',
            Auth::user() ? Auth::user()->name : 'System',
            $event,
            Str::lower($modelName)
        );
    }

    /**
     * Get the changes for the activity log.
     *
     * @param string $event
     * @return array
     */
    protected function getChangesForLog(string $event): array
    {
        if ($event === 'updated') {
            return [
                'old' => Arr::except($this->getOriginal(), $this->hidden),
                'new' => $this->getChanges(),
            ];
        }

        if ($event === 'created' || $event === 'deleted') {
            return [
                'attributes' => $this->getAttributes(),
            ];
        }

        return [];
    }

    /**
     * Get the activity logs for the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activityLogs()
    {
        return $this->morphMany(\App\Models\ActivityLog::class, 'subject');
    }
}
