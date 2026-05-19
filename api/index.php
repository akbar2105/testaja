<?php

// Vercel hanya mengizinkan tulis/baca di dalam /tmp
$tmpStorage = '/tmp/storage';

// Buat struktur folder storage di dalam /tmp
$directories = [
    $tmpStorage . '/framework/cache/data',
    $tmpStorage . '/framework/sessions',
    $tmpStorage . '/framework/testing',
    $tmpStorage . '/framework/views',
    $tmpStorage . '/logs',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Beritahu Laravel untuk menggunakan /tmp/storage
$_ENV['APP_STORAGE'] = $tmpStorage;

// Teruskan request ke aplikasi utama Laravel
require __DIR__ . '/../public/index.php';
