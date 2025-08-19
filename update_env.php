<?php

$envPath = __DIR__ . '/.env';
$env = file_get_contents($envPath);

$updates = [
    'DB_CONNECTION=sqlite' => 'DB_CONNECTION=mysql',
    'DB_DATABASE=laravel' => 'DB_DATABASE=mini_pms',
    'DB_USERNAME=root' => 'DB_USERNAME=root',
    'DB_PASSWORD=' => 'DB_PASSWORD=',
];

foreach ($updates as $search => $replace) {
    $env = str_replace($search, $replace, $env);
}

file_put_contents($envPath, $env);

echo "Environment file updated successfully.\n";
