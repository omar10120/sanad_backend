<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateAllMigrationsCompact extends Command
{
protected $signature = 'generate:compactmigrations';
protected $description = 'Generate AllMigrations_Compact.php with all migrations minified and cleaned for AI ingestion';

public function handle()
{
$migrationPath = database_path('migrations');
$outputPath = base_path('AllMigrations_Compact.php');

$files = File::allFiles($migrationPath);

$migrations = [];

foreach ($files as $file) {
    if ($file->getExtension() === 'php') {
        $migrations[] = $file;
    }
}

usort($migrations, function ($a, $b) {
    return strcmp($a->getFilename(), $b->getFilename());
});

$content = "// === All Migrations Content (Minified) ===\n";

foreach ($migrations as $file) {
$filename = $file->getFilename();
$fileContent = File::get($file->getRealPath());

$fileContent = str_replace(['<?php', '?>'], '', $fileContent);
$fileContent = preg_replace('/^use .*;/m', '', $fileContent);
$fileContent = preg_replace('/^declare\(.*\);/m', '', $fileContent);

// إزالة التعليقات //
$fileContent = preg_replace('/\/\/.*$/m', '', $fileContent);
// إزالة التعليقات #
$fileContent = preg_replace('/#.*$/m', '', $fileContent);
// إزالة التعليقات /* */
$fileContent = preg_replace('#/\*.*?\*/#s', '', $fileContent);

// إزالة الأسطر الفارغة
$fileContent = preg_replace('/^\s*$(?:\r\n?|\n)/m', '', $fileContent);

// إضافة عنوان اسم الملف فقط
$content .= "// ===== $filename =====\n";
$content .= $fileContent . "\n";
}

File::put($outputPath, $content);

$this->info("AllMigrations_Compact.php generated successfully with minified, cleaned content.");
}
}
