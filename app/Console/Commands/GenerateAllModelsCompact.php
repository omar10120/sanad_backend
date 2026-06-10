<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateAllModelsCompact extends Command
{
protected $signature = 'generate:compactmodels';
protected $description = 'Generate AllModels_Compact.php with all models minified and cleaned for AI ingestion';

public function handle()
{
$modelPath = app_path('Models');
$outputPath = base_path('AllModels_Compact.php');

if (!File::exists($modelPath)) {
    $this->error("The Models directory does not exist at app/Models.");
    return;
}

$files = File::allFiles($modelPath);

$models = [];

foreach ($files as $file) {
    if ($file->getExtension() === 'php') {
        $models[] = $file;
    }
}

usort($models, function ($a, $b) {
    return strcmp($a->getFilename(), $b->getFilename());
});

$content = "// === All Models Content (Minified) ===\n";

foreach ($models as $file) {
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

$this->info("AllModels_Compact.php generated successfully with minified, cleaned content.");
}
}
