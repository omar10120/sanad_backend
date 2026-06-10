<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateAllResourcesClassesCompact extends Command
{
protected $signature = 'generate:compactresourcesclasses';
protected $description = 'Generate AllResourcesClasses_Compact.php with all app/Http/Resources classes minified and cleaned for AI ingestion';

public function handle()
{
$resourcesPath = app_path('Http/Resources');
$outputPath = base_path('AllResourcesClasses_Compact.php');

if (!File::exists($resourcesPath)) {
    $this->error("The Resources directory does not exist at app/Http/Resources.");
    return;
}

$files = File::allFiles($resourcesPath);

$resources = [];

foreach ($files as $file) {
    if ($file->getExtension() === 'php') {
        $resources[] = $file;
    }
}

usort($resources, function ($a, $b) {
    return strcmp($a->getFilename(), $b->getFilename());
});

$content = "// === All app/Http/Resources Classes Content (Minified) ===\n";

foreach ($resources as $file) {
$filename = str_replace(base_path() . '/', '', $file->getRealPath());
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

$content .= "// ===== $filename =====\n";
$content .= $fileContent . "\n";
}

File::put($outputPath, $content);

$this->info("AllResourcesClasses_Compact.php generated successfully with minified, cleaned content.");
}
}
