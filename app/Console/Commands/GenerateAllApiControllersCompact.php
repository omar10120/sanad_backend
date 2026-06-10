<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateAllApiControllersCompact extends Command
{
protected $signature = 'generate:compactapicontrollers';
protected $description = 'Generate AllApiControllers_Compact.php with all Api controllers minified and cleaned for AI ingestion';

public function handle()
{
$apiControllerPath = app_path('Http/Controllers/Api');
$outputPath = base_path('AllApiControllers_Compact.php');

if (!File::exists($apiControllerPath)) {
    $this->error("The Api Controllers directory does not exist at app/Http/Controllers/Api.");
    return;
}

$files = File::allFiles($apiControllerPath);

$controllers = [];

foreach ($files as $file) {
    if ($file->getExtension() === 'php') {
        $controllers[] = $file;
    }
}

usort($controllers, function ($a, $b) {
    return strcmp($a->getFilename(), $b->getFilename());
});

$content = "// === All Api Controllers Content (Minified) ===\n";

foreach ($controllers as $file) {
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

$this->info("AllApiControllers_Compact.php generated successfully with minified, cleaned content.");
}
}
