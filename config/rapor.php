<?php

$threshold = env('RAPOR_NILAI_TUNTAS');

return [
    // Leave unset until the school confirms a common threshold for all subjects.
    'nilai_tuntas' => is_numeric($threshold) && $threshold >= 0 && $threshold <= 100 ? (float) $threshold : null,
    'seed_account_password' => env('SEED_ACCOUNT_PASSWORD'),
];
