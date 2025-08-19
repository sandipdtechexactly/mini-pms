<?php

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;

if (! function_exists('format_date')) {
    /**
     * Format a date string or Carbon instance.
     *
     * @param  mixed  $date
     * @param  string  $format
     * @param  string|null  $timezone
     * @return string|null
     */
    function format_date($date, string $format = 'Y-m-d H:i:s', string $timezone = null): ?string
    {
        if (is_null($date)) {
            return null;
        }

        if (! $date instanceof Carbon) {
            $date = Carbon::parse($date);
        }

        if ($timezone) {
            $date->setTimezone($timezone);
        }

        return $date->format($format);
    }
}

if (! function_exists('format_currency')) {
    /**
     * Format a number as currency.
     *
     * @param  float  $amount
     * @param  string  $currency
     * @param  int  $decimals
     * @return string
     */
    function format_currency(float $amount, string $currency = 'USD', int $decimals = 2): string
    {
        $formatter = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amount, $currency);
    }
}

if (! function_exists('gravatar')) {
    /**
     * Generate a Gravatar image URL or image tag.
     *
     * @param  string  $email
     * @param  int  $size
     * @param  string  $default
     * @param  string  $rating
     * @param  bool  $img
     * @param  array  $attributes
     * @return string|HtmlString
     */
    function gravatar(
        string $email, 
        int $size = 80, 
        string $default = 'mp', 
        string $rating = 'g',
        bool $img = true,
        array $attributes = []
    ) {
        $hash = md5(strtolower(trim($email)));
        $url = "https://www.gravatar.com/avatar/{$hash}?s={$size}&d={$default}&r={$rating}";

        if (! $img) {
            return $url;
        }

        $attributes['src'] = $url;
        $attributes['width'] = $size;
        $attributes['height'] = $size;

        if (! isset($attributes['alt'])) {
            $attributes['alt'] = 'Gravatar';
        }

        $html = '<img';
        foreach ($attributes as $key => $value) {
            $html .= ' ' . $key . '="' . e($value, false) . '"';
        }
        $html .= '>';

        return new HtmlString($html);
    }
}

if (! function_exists('active_class')) {
    /**
     * Return the "active" class if the current route matches the given patterns.
     *
     * @param  array|string  $patterns
     * @param  string  $class
     * @return string
     */
    function active_class($patterns, string $class = 'active'): string
    {
        $currentRoute = request()->route();
        
        if (is_null($currentRoute)) {
            return '';
        }

        $currentRouteName = $currentRoute->getName();
        
        foreach ((array) $patterns as $pattern) {
            if (Str::is($pattern, $currentRouteName)) {
                return $class;
            }
        }

        return '';
    }
}

if (! function_exists('is_active')) {
    /**
     * Determine if the current route matches the given patterns.
     *
     * @param  array|string  $patterns
     * @return bool
     */
    function is_active($patterns): bool
    {
        return ! empty(active_class($patterns, true));
    }
}

if (! function_exists('current_user')) {
    /**
     * Get the current authenticated user.
     *
     * @return \App\Models\User|null
     */
    function current_user()
    {
        return Auth::user();
    }
}

if (! function_exists('is_admin')) {
    /**
     * Check if the current user is an admin.
     *
     * @return bool
     */
    function is_admin(): bool
    {
        $user = current_user();
        return $user && $user->hasRole('admin');
    }
}

if (! function_exists('is_manager')) {
    /**
     * Check if the current user is a manager.
     *
     * @return bool
     */
    function is_manager(): bool
    {
        $user = current_user();
        return $user && $user->hasRole('manager');
    }
}

if (! function_exists('is_developer')) {
    /**
     * Check if the current user is a developer.
     *
     * @return bool
     */
    function is_developer(): bool
    {
        $user = current_user();
        return $user && $user->hasRole('developer');
    }
}

if (! function_exists('generate_random_string')) {
    /**
     * Generate a random string of the specified length.
     *
     * @param  int  $length
     * @param  string  $keyspace
     * @return string
     */
    function generate_random_string(int $length = 10, string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'): string
    {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        
        for ($i = 0; $i < $length; $i++) {
            $pieces[] = $keyspace[random_int(0, $max)];
        }
        
        return implode('', $pieces);
    }
}

if (! function_exists('truncate_text')) {
    /**
     * Truncate text to a specified length and add an ellipsis if needed.
     *
     * @param  string  $text
     * @param  int  $length
     * @param  string  $ending
     * @return string
     */
    function truncate_text(string $text, int $length = 100, string $ending = '...'): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }
        
        return rtrim(mb_substr($text, 0, $length)) . $ending;
    }
}

if (! function_exists('format_bytes')) {
    /**
     * Format bytes to a human-readable format.
     *
     * @param  int  $bytes
     * @param  int  $precision
     * @return string
     */
    function format_bytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (! function_exists('array_to_html_attributes')) {
    /**
     * Convert an array of attributes to HTML attributes.
     *
     * @param  array  $attributes
     * @return string
     */
    function array_to_html_attributes(array $attributes): string
    {
        $html = [];
        
        foreach ($attributes as $key => $value) {
            $element = $key . '="' . e($value, false) . '"';
            $html[] = $element;
        }
        
        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }
}

if (! function_exists('is_production')) {
    /**
     * Check if the application is in production environment.
     *
     * @return bool
     */
    function is_production(): bool
    {
        return app()->environment('production');
    }
}

if (! function_exists('is_local')) {
    /**
     * Check if the application is in local environment.
     *
     * @return bool
     */
    function is_local(): bool
    {
        return app()->environment('local');
    }
}

if (! function_exists('is_testing')) {
    /**
     * Check if the application is in testing environment.
     *
     * @return bool
     */
    function is_testing(): bool
    {
        return app()->environment('testing');
    }
}
