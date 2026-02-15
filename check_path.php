<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$filePath = 'documents/locataires/1/logo-cefmj_1771005843.jpeg';
echo "Checking: " . $filePath . "
";
echo "Disk local root: " . config('filesystems.disks.local.root') . "
";
echo "Exists on local: " . (Illuminate\Support\Facades\Storage::disk('local')->exists($filePath) ? 'YES' : 'NO') . "
";
echo "Exists on public: " . (Illuminate\Support\Facades\Storage::disk('public')->exists($filePath) ? 'YES' : 'NO') . "
";

$fullPath = config('filesystems.disks.local.root') . '/' . $filePath;
echo "Full path check: " . $fullPath . "
";
echo "File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "
";
