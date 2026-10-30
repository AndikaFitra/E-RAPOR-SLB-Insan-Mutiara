<?php

$threshold = env('RAPOR_NILAI_TUNTAS');

return [
    // Leave unset until the school confirms a common threshold for all subjects.
    'nilai_tuntas' => is_numeric($threshold) && $threshold >= 0 && $threshold <= 100 ? (float) $threshold : null,
    'seed_account_password' => env('SEED_ACCOUNT_PASSWORD'),
    'seed_account_passwords' => [
        'admin' => env('SEED_ADMIN_PASSWORD'),
        'kepala' => env('SEED_KEPALA_PASSWORD'),
        'wali_tunarungu' => env('SEED_WALI_TUNARUNGU_PASSWORD'),
        'wali_tunagrahita' => env('SEED_WALI_TUNAGRAHITA_PASSWORD'),
    ],
];
