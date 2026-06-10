<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateAllControllersCompact extends Command
{
    protected $signature = 'generate:compactcontrollers';

    protected $description = 'Generate AllControllers_Compact.php with all controllers, minified and cleaned for AI ingestion';

    public function handle()
    {
        $controllerPath = app_path('Http/Controllers');
        $outputPath = base_path('AllControllers_Compact.php');

        $files = File::allFiles($controllerPath);

        $controllers = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $controllers[] = $file;
            }
        }

        usort($controllers, function ($a, $b) {
            return strcmp($a->getFilename(), $b->getFilename());
        });

        $content = "// === All Controllers Content (Minified) ===\n";

        foreach ($controllers as $file) {
            $filename = $file->getFilename();
            $fileContent = File::get($file->getRealPath());

            // إزالة <?php و use و declare
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

        $this->info("AllControllers_Compact.php generated successfully with minified, cleaned content.");
    }
}
