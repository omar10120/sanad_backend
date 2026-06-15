<?php

namespace App\Exports;

use App\Models\CodePackage;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Response;

class CodesPdfExport
{
    /**
     * Generate a simple SVG QR code fallback
     */
    private function generateSimpleQrCode($text, $size = 150)
    {
        // Create a simple SVG with the code text
        $svg = '<svg width="' . $size . '" height="' . $size . '" xmlns="http://www.w3.org/2000/svg">';
        $svg .= '<rect width="' . $size . '" height="' . $size . '" fill="white" stroke="black" stroke-width="1"/>';
        $svg .= '<text x="' . ($size/2) . '" y="' . ($size/2) . '" text-anchor="middle" dy=".3em" font-family="Arial Unicode MS, DejaVu Sans, Tahoma, Arial" font-size="12" fill="black">' . htmlspecialchars($text) . '</text>';
        $svg .= '</svg>';
        
        return $svg;
    }

    /**
     * Check if text contains Arabic characters
     */
    private function containsArabic($text)
    {
        return preg_match('/[\x{0600}-\x{06FF}]/u', $text);
    }

    /**
     * Format subjects text with proper RTL handling
     */
    private function formatSubjects($subjects)
    {
        // Split subjects and format each one properly
        $subjectArray = explode(', ', $subjects);
        $formattedSubjects = [];
        
        foreach ($subjectArray as $subject) {
            $subject = trim($subject);
            if ($this->containsArabic($subject)) {
                // For Arabic text, use a more robust approach
                $formattedSubjects[] = $this->formatArabicText($subject);
            } else {
                $formattedSubjects[] = $subject;
            }
        }
        
        return implode(', ', $formattedSubjects);
    }

    /**
     * Format Arabic text for proper rendering
     */
    private function formatArabicText($text)
    {
        // Remove any existing HTML tags
        $text = strip_tags($text);
        
        // Convert Arabic text to transliterated text for better compatibility
        $arabicToLatin = [
            'ا' => 'a', 'ب' => 'b', 'ت' => 't', 'ث' => 'th', 'ج' => 'j',
            'ح' => 'h', 'خ' => 'kh', 'د' => 'd', 'ذ' => 'dh', 'ر' => 'r',
            'ز' => 'z', 'س' => 's', 'ش' => 'sh', 'ص' => 's', 'ض' => 'd',
            'ط' => 't', 'ظ' => 'z', 'ع' => 'a', 'غ' => 'gh', 'ف' => 'f',
            'ق' => 'q', 'ك' => 'k', 'ل' => 'l', 'م' => 'm', 'ن' => 'n',
            'ه' => 'h', 'و' => 'w', 'ي' => 'y', 'ة' => 'h', 'ى' => 'a',
            'ء' => 'a', 'إ' => 'a', 'أ' => 'a', 'آ' => 'a', 'ئ' => 'a',
            'ؤ' => 'a', 'ء' => 'a'
        ];
        
        $converted = '';
        for ($i = 0; $i < mb_strlen($text, 'UTF-8'); $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            $converted .= isset($arabicToLatin[$char]) ? $arabicToLatin[$char] : $char;
        }
        
        // Convert to title case (first letter uppercase, rest lowercase)
        return ucwords(strtolower($converted));
    }

    public function exportByPackage($packageId)
    {
        try {
            // Get the package with its codes and subjects
            $package = CodePackage::with(['codes', 'codePackageSubjects.subject', 'codePackageSubjects.unit'])->findOrFail($packageId);
            $codes = $package->codes;
            $subjects = app(\App\Services\CodeService::class)->formatPackageSubjectsForDisplay($package);
            $rawSubjects = collect($subjects)->map(function ($group) {
                return $group['subject_name'] . ': ' . collect($group['units'])->pluck('name')->implode(', ');
            })->implode(' | ');
            $subjects = $this->formatSubjects($rawSubjects);
            
            if ($codes->isEmpty()) {
                throw new \Exception('No codes found in this package.');
            }
            
            // Generate QR codes for all codes
            $codesWithQr = [];
            foreach ($codes as $code) {
                try {
                    $qrCode = QrCode::format('svg')
                        ->size(150)
                        ->backgroundColor(255, 255, 255)
                        ->color(0, 0, 0)
                        ->generate($code->code);
                } catch (\Exception $e) {
                    // Fallback to simple SVG if QR generation fails
                    $qrCode = $this->generateSimpleQrCode($code->code, 150);
                }
                
                $codesWithQr[] = [
                    'code' => $code->code,
                    'qr_code' => base64_encode($qrCode),
                    'subjects' => $subjects,
                    'expires_at' => $package->expires_at
                ];
            }
        
            // Organize codes into pages (16 codes per page - 4x4 grid)
            $codesPerPage = 16;
            $pages = array_chunk($codesWithQr, $codesPerPage);
            
            // Generate PDF
            $pdf = PDF::loadView('exports.codes-pdf', [
                'package' => $package,
                'pages' => $pages,
                'codesPerPage' => $codesPerPage
            ]);
            
            $pdf->setPaper('A4', 'portrait');
            $pdf->getDomPDF()->set_option('isHtml5ParserEnabled', true);
            $pdf->getDomPDF()->set_option('isPhpEnabled', true);
            $pdf->getDomPDF()->set_option('defaultFont', 'Arial');
            $pdf->getDomPDF()->set_option('isRemoteEnabled', true);
            $pdf->getDomPDF()->set_option('defaultMediaType', 'screen');
            $pdf->getDomPDF()->set_option('enableFontSubsetting', true);
            $pdf->getDomPDF()->set_option('enableCssFloat', true);
            $pdf->getDomPDF()->set_option('enableInlineCss', true);
            
            $filename = 'codes_package_' . $packageId . '_' . date('Y-m-d_H-i-s') . '.pdf';
            
            return Response::stream(
                function () use ($pdf) {
                    echo $pdf->output();
                },
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Cache-Control' => 'max-age=0',
                ]
            );
        } catch (\Exception $e) {
            // Re-throw the exception to be handled by the controller
            throw new \Exception('Error generating PDF: ' . $e->getMessage());
        }
    }
} 