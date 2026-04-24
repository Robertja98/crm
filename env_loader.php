<?php
// env_loader.php - Loads environment variables from .env if present
function load_env($envFile = __DIR__ . '/.env') {
    if (!file_exists($envFile)) return;
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        list($name, $value) = array_map('trim', explode('=', $line, 2));
        if ($name === '') continue;

        // Always apply current .env values so stale process/system env does not override local config.
        putenv("$name=$value");
        $_ENV[$name] = $value;
    }
}
