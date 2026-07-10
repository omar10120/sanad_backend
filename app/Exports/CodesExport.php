<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Response;
use App\Models\Code;
use App\Models\CodePackage;

class CodesExport
{
    public function exportByPackage($packageId)
    {
        // استخدام الملف القالب الذي يحتوي على VBA code
        $templatePath = public_path('assets/template-export-code.xlsm');
        
        if (!file_exists($templatePath)) {
            throw new \Exception('Template file not found: ' . $templatePath);
        }

        try {
            // تحميل الملف القالب
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();

            // مسح البيانات الموجودة (الاحتفاظ بالعناوين)
            $highestRow = $sheet->getHighestRow();
            if ($highestRow > 1) {
                $sheet->removeRow(2, $highestRow - 1);
            }

            // إضافة رؤوس الأعمدة (إذا لم تكن موجودة)
            if ($sheet->getCell('A1')->getValue() == '') {
                $sheet->setCellValue('A1', 'Package Name');
                $sheet->setCellValue('B1', 'Code');
                $sheet->setCellValue('C1', 'Subject Names');
                $sheet->setCellValue('D1', 'Student');
                $sheet->setCellValue('E1', 'Expired At');
                $sheet->setCellValue('F1', 'QR Code');

                // تنسيق رؤوس الأعمدة
                $sheet->getStyle('A1:F1')->getFont()->setBold(true);
                $sheet->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $sheet->getStyle('A1:F1')->getFill()->getStartColor()->setRGB('CCCCCC');
            }

            // الحصول على الحزمة والأكواد من قاعدة البيانات
            $package = CodePackage::with(['codes', 'codePackageSubjects.subject', 'codePackageSubjects.unit'])->findOrFail($packageId);
            $codes = $package->codes;
            $subjects = app(\App\Services\CodeService::class)->formatPackageSubjectsAsText($package);

            $row = 2;
            foreach ($codes as $code) {
                // إضافة اسم الحزمة
                $sheet->setCellValue('A' . $row, $package->name);

                // إضافة الكود
                $sheet->setCellValue('B' . $row, $code->code);

                // إضافة المواد المرتبطة
                $sheet->setCellValue('C' . $row, $subjects);

                // إضافة معلومات الطالب
                if ($code->student) {
                    $studentInfo = $code->student->id . '-' . 
                                 $code->student->first_name . '-' . 
                                 $code->student->father_name . '-' . 
                                 $code->student->last_name;
                    $sheet->setCellValue('D' . $row, $studentInfo);
                } else {
                    $sheet->setCellValue('D' . $row, 'Not Used');
                }

                // إضافة تاريخ الصلاحية
                $sheet->setCellValue('E' . $row, $package->expires_at);

                // إضافة QR Code باستخدام صيغة Excel مع VBA functions
                $qrFormula = '=IF(ISBLANK(B' . $row . '), "", IMAGE("https://quickchart.io/qr?text="&ENCODEURL(B' . $row . ')))';
                $sheet->setCellValue('F' . $row, $qrFormula);
                
                // تنسيق خلية QR Code
                $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . $row)->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                // زيادة السطر التالي
                $row++;
            }

            // تنسيق عرض الأعمدة
            $sheet->getColumnDimension('A')->setWidth(20);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(50);
            $sheet->getColumnDimension('D')->setWidth(30);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(20);

            $sheet->getRowDimension('1')->setRowHeight(25);
            for ($i = 2; $i < $row; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(100);
            }

            // إنشاء كاتب Excel مع الحفاظ على VBA code
            $writer = new Xlsx($spreadsheet);
            $filename = 'codes_package_' . $package->name . '_' . date('Y-m-d_H-i-s') . '.xlsm';

            // إرسال الملف مباشرة للمتصفح
            return Response::stream(
                function () use ($writer) {
                    $writer->save('php://output');
                },
                200,
                [
                    'Content-Type' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
                    'Content-Disposition' => 'attachment;filename="' . $filename . '"',
                    'Cache-Control' => 'max-age=0',
                ]
            );

        } catch (\Exception $e) {
            throw new \Exception('Error processing template file: ' . $e->getMessage());
        }
    }
}
