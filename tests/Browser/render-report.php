<?php

// Preview artifact from the dedicated browser fixture, never the project database.
$root = dirname(__DIR__, 2);
$database = $root.'/storage/framework/testing/browser.sqlite';
foreach (['APP_ENV'=>'testing','DB_CONNECTION'=>'sqlite','DB_DATABASE'=>$database,'DB_URL'=>'','CACHE_STORE'=>'array','SESSION_DRIVER'=>'file','APP_CONFIG_CACHE'=>$root.'/storage/framework/testing/browser-config.php'] as $key=>$value) {
    putenv("$key=$value"); $_ENV[$key]=$value; $_SERVER[$key]=$value;
}
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (config('database.default') !== 'sqlite' || config('database.connections.sqlite.database') !== $database) {
    throw new RuntimeException('Refusing to read a database other than the browser fixture.');
}
$student = \App\Models\Siswa::whereHas('penugasanKelas')->firstOrFail();
$year = \App\Models\TahunAjaran::getAktif();
$service = app(\App\Services\RaporService::class);
$data = $service->data($student, $year);
$path = $root.'/storage/framework/testing/report-preview.pdf';
file_put_contents($path, $service->pdf($data));
echo $path.PHP_EOL;
