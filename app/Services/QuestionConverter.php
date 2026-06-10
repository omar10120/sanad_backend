<?php

namespace App\Services;

class QuestionConverter
{
//    protected $symbolReplacements = [
//        '→' => '\\to ',
//        '←' => '\\leftarrow ',
//        '⇒' => '\\Rightarrow ',
//        '⇐' => '\\Leftarrow ',
//    ];
//
//    public function toLatex(string $input): string
//    {
//        // 1. أولاً: معالجة الرموز النووية والكيميائية
//        $input = preg_replace_callback('/\(_(-?\d+)\^(-?\d+)\)([A-Za-z]+)/', function ($matches) {
//            $bottom = $matches[1];
//            $top = $matches[2];
//            $element = $matches[3];
//            return '^{'.$top.'}_{'.$bottom.'}\mathrm{'.$element.'}';
//        }, $input);
//
//        // 2. ثانياً: استبدال الأسهم
//        foreach ($this->symbolReplacements as $key => $replacement) {
//            $input = str_replace($key, $replacement, $input);
//        }
//
//        // 3. ثالثاً: لف الكلمات العادية بـ \text{} إذا كانت كلمة مستقلة
//        $input = preg_replace_callback('/(?<![\\\\])\b([A-Z][a-z]*[a-z]*)\b/', function ($matches) {
//            $word = $matches[1];
//            $reservedWords = ['to', 'leftarrow', 'Rightarrow', 'Leftarrow'];
//            if (in_array($word, $reservedWords)) {
//                return '\\'.$word;
//            }
//            // التأكد من أن الباك سلاش يظهر مرة واحدة فقط
//            return '\\mathrm{\\text{'.$word.'}}';
//        }, $input);
//
//        // 4. رابعاً: دعم الكسور باستخدام \frac
//        $input = preg_replace_callback('/\((\d+)\s*\/\s*(\d+)\)/', function ($matches) {
//            return '\\frac{'.$matches[1].'}{'.$matches[2].'}';
//        }, $input);
//
//        // 5. خامساً: دعم الجذور باستخدام \sqrt
//        $input = preg_replace_callback('/\sqrt{([^}]+)}/', function ($matches) {
//            return '\\sqrt{'.$matches[1].'}';
//        }, $input);
//
//        return $input;
//    }

//    public static function convertFormulaToLatex(string $formula): string
//    {
//        // 1. تحويل (_x^y) إلى _{x}^{y}
//        $formula = preg_replace('/\(_(-?\d+)\^(-?\d+)\)/', '_{\1}^{\2}', $formula);
//
//        // 2. تحويل → إلى \to (بدون تهريب مزدوج)
//        $formula = str_replace('→', ' \to ', $formula);
//
//        // 3. تحويل الرموز الكيميائية (Ra, Rn, He) إلى \mathrm{Ra} (تهريب واحد)
//        $formula = preg_replace('/\b([A-Z][a-z])\b/', '\mathrm{\1}', $formula);
//
//        // 4. معالجة β → \beta (تهريب واحد)
//        $formula = str_replace('β', '\beta', $formula);
//
//        // 5. إضافة \text فقط للنصوص الطويلة (مثل Energy) (تهريب واحد)
//        $formula = preg_replace('/([A-Za-z]{3,})/', '\text{\1}', $formula);
//
//        // 6. إصلاح المسافات حول المشغلات (+/-)
//        $formula = preg_replace('/\s*([+\-])\s*/', ' \1 ', $formula);
//
//        // 7. تنظيف المسافات الزائدة
//        $formula = trim(preg_replace('/\s+/', ' ', $formula));
//
//        return $formula;
//    }
//
//    public static function convertDeltaToLatex(array $delta): array
//    {
//        if (isset($delta['ops'])) {
//            foreach ($delta['ops'] as &$op) {
//                if (isset($op['insert']['formula'])) {
//                    $op['insert']['formula'] = self::convertFormulaToLatex($op['insert']['formula']);
//                }
//            }
//        }
//        return $delta;
//    }

    /**
     * تحويل معادلة من صيغة Quill Delta إلى صيغة LaTeX
     *
     * @param string $formula المعادلة بصيغة Quill
     * @return string المعادلة بصيغة LaTeX
     */
//    public function convertToLatex($formula)
//    {
//        // تنظيف النص من الفراغات الزائدة
//        $formula = trim($formula);
//
//        // تحويل معادلات العناصر الكيميائية مثل (_88^226)Ra
//        $formula = preg_replace_callback('/\(_(\d+)\^(\d+)\)([A-Za-z]+)/', function($matches) {
//            return '^{' . $matches[2] . '}_{' . $matches[1] . '}\mathrm{' . $matches[3] . '}';
//        }, $formula);
//
//        // تحويل معادلات العناصر الكيميائية بترتيب مختلف مثل Ra(_88^226)
//        $formula = preg_replace_callback('/([A-Za-z]+)\(_(\d+)\^(\d+)\)/', function($matches) {
//            return '\mathrm{' . $matches[1] . '}^{' . $matches[3] . '}_{' . $matches[2] . '}';
//        }, $formula);
//
//        // تحويل التكاملات مع الحدود مثل ∫_1^2▒〖...〗
//        $formula = preg_replace_callback('/∫_([^▒]+)\^([^▒]+)▒〖([^〗]+)〗/', function($matches) {
//            $innerFormula = $this->processInnerFormula($matches[3]);
//            return '\int_' . $matches[1] . '^' . $matches[2] . ' ' . $innerFormula . ' \, dx';
//        }, $formula);
//
//        // تحويل التكاملات بدون ▒〖...〗
//        $formula = preg_replace_callback('/∫_([^~]+)\^([^~]+)(.+)/', function($matches) {
//            return '\int_' . $matches[1] . '^' . $matches[2] . ' ' . $matches[3] . ' \, dx';
//        }, $formula);
//
//        // تحويل الكسور مثل (n^(2 )+1)/2n
//        $formula = preg_replace_callback('/\(([^)]+)\)\/([^→=\+\s]+)/', function($matches) {
//            return '\frac{' . $this->processInnerFormula($matches[1]) . '}{' . $matches[2] . '}';
//        }, $formula);
//
//        // تحويل الكسور البسيطة مثل 1/x^2
//        $formula = preg_replace_callback('/(\d+)\/([^\s\+\-\*\/]+)/', function($matches) {
//            return '\frac{' . $matches[1] . '}{' . $matches[2] . '}';
//        }, $formula);
//
//        // تحويل الجذور مثل √x
//        $formula = preg_replace('/√([A-Za-z0-9]+)/', '\sqrt{$1}', $formula);
//
//        // تحويل الأسس مثل n^2
//        $formula = preg_replace_callback('/([A-Za-z0-9]+)\^(\(.+?\)|[A-Za-z0-9]+)/', function($matches) {
//            $base = $matches[1];
//            $exponent = $matches[2];
//
//            // إزالة الأقواس من الأس إذا وجدت
//            if (substr($exponent, 0, 1) === '(' && substr($exponent, -1) === ')') {
//                $exponent = substr($exponent, 1, -1);
//            }
//
//            return $base . '^{' . $exponent . '}';
//        }, $formula);
//
//        // تحويل سهم التفاعل
//        $formula = str_replace('→', '\to', $formula);
//
//        // تحويل النص العادي مثل Energy
//        $formula = preg_replace('/\b([A-Za-z]+)\b/', '\text{$1}', $formula);
//
//        // تصحيح معالجة الرموز الرياضية (حتى لا تتحول إلى نص)
//        $mathSymbols = ['mathrm', 'sqrt', 'frac', 'int', 'to', 'text'];
//        foreach ($mathSymbols as $symbol) {
//            $formula = str_replace('\text{' . $symbol . '}', '\\' . $symbol, $formula);
//        }
//
//        // إصلاح الإشارات بعد تحويلها إلى نص
//        $formula = str_replace('\text{+}', '+', $formula);
//        $formula = str_replace('\text{-}', '-', $formula);
//        $formula = str_replace('\text{=}', '=', $formula);
//
//        return $formula;
//    }
//
//    /**
//     * معالجة الصيغ الداخلية
//     *
//     * @param string $formula
//     * @return string
//     */
//    private function processInnerFormula($formula)
//    {
//        // تنظيف وإزالة الرموز غير المطلوبة
//        $formula = str_replace('〖', '', $formula);
//        $formula = str_replace('〗', '', $formula);
//
//        // تحويل الكسور الداخلية
//        $formula = preg_replace_callback('/(\d+)\/([^\s\+\-\*\/]+)/', function($matches) {
//            return '\frac{' . $matches[1] . '}{' . $matches[2] . '}';
//        }, $formula);
//
//        // تحويل الجذور
//        $formula = preg_replace('/√([A-Za-z0-9]+)/', '\sqrt{$1}', $formula);
//
//        return $formula;
//    }
//
//    /**
//     * معالجة ملف دفعة من المعادلات
//     *
//     * @param array $questions مصفوفة تحتوي على الأسئلة مع المعادلات
//     * @return array الأسئلة بعد تحويل المعادلات
//     */
//    public function processQuestions($questions)
//    {
//        $processed = [];
//
//        foreach ($questions as $question) {
//            // افترض أن $question->content يحتوي على محتوى Quill Delta بتنسيق JSON
//            $content = json_decode($question->content, true);
//
//            if (isset($content['ops'])) {
//                foreach ($content['ops'] as &$op) {
//                    // البحث عن المعادلات في النص
//                    if (isset($op['insert']) && is_string($op['insert'])) {
//                        $op['insert'] = $this->convertFormulasInText($op['insert']);
//                    }
//
//                    // البحث عن المعادلات في الصيغ المخصصة
//                    if (isset($op['insert']['formula'])) {
//                        $op['insert']['formula'] = $this->convertToLatex($op['insert']['formula']);
//                    }
//                }
//            }
//
//            $question->content = json_encode($content);
//            $processed[] = $question;
//        }
//
//        return $processed;
//    }
//
//    /**
//     * تحويل المعادلات الموجودة في النص العادي
//     *
//     * @param string $text
//     * @return string
//     */
//    private function convertFormulasInText($text)
//    {
//        // البحث عن أنماط المعادلات في النص واستبدالها
//        $patterns = [
//            '/\(_(\d+)\^(\d+)\)([A-Za-z]+)/',  // مثل (_88^226)Ra
//            '/∫_([^▒]+)\^([^▒]+)▒〖([^〗]+)〗/',  // تكاملات
//            '/([A-Za-z0-9]+)\^(\(.+?\)|[A-Za-z0-9]+)/',  // أسس
//            '/√([A-Za-z0-9]+)/',  // جذور
//        ];
//
//        // إذا وجد نمط معادلة، قم بتحويل النص بالكامل
//        foreach ($patterns as $pattern) {
//            if (preg_match($pattern, $text)) {
//                return $this->convertToLatex($text);
//            }
//        }
//
//        return $text;
//    }

    /**
     * تحويل معادلة من صيغة Quill Delta إلى صيغة LaTeX
     *
     * @param string $formula المعادلة بصيغة Quill
     * @return string المعادلة بصيغة LaTeX
     */
    public function convertToLatex($formula)
    {
        // تنظيف النص من الفراغات الزائدة
        $formula=trim($formula);

        // الخطوة 1: تحويل معادلات العناصر الكيميائية والجسيمات قبل أي شيء آخر
        $formula = preg_replace_callback('/\(_(\d+)\^(\d+)\)([A-Za-z]+)/', function($matches) {
            return '^{' . $matches[2] . '}_{' . $matches[1] . '}\\mathrm{' . $matches[3] . '}';
        }, $formula);

        // معالجة خاصة للعناصر مع أعداد سالبة مثل (_-1^0)β
        $formula = preg_replace_callback('/\(_(-\d+)\^(\d+)\)([A-Za-z]+|β)/', function($matches) {
            if ($matches[3] == 'β') {
                return '_{' . $matches[1] . '}^{' . $matches[2] . '}\\beta';
            }
            return '^{' . $matches[2] . '}_{' . $matches[1] . '}\\mathrm{' . $matches[3] . '}';
        }, $formula);

        // الخطوة 2: تحويل الرموز اليونانية بدون إحاطتها بـ \text
        $greekSymbols = [
            'α' => '\\alpha',
            'β' => '\\beta',
            'γ' => '\\gamma',
            'Γ' => '\\Gamma',
            'δ' => '\\delta',
            'Δ' => '\\Delta',
            'ε' => '\\epsilon',
            'ζ' => '\\zeta',
            'η' => '\\eta',
            'θ' => '\\theta',
            'Θ' => '\\Theta',
            'ι' => '\\iota',
            'κ' => '\\kappa',
            'λ' => '\\lambda',
            'Λ' => '\\Lambda',
            'μ' => '\\mu',
            'ν' => '\\nu',
            'ξ' => '\\xi',
            'Ξ' => '\\Xi',
            'π' => '\\pi',
            'Π' => '\\Pi',
            'ρ' => '\\rho',
            'σ' => '\\sigma',
            'Σ' => '\\Sigma',
            'τ' => '\\tau',
            'υ' => '\\upsilon',
            'Υ' => '\\Upsilon',
            'φ' => '\\phi',
            'Φ' => '\\Phi',
            'χ' => '\\chi',
            'ψ' => '\\psi',
            'Ψ' => '\\Psi',
            'ω' => '\\omega',
            'Ω' => '\\Omega'
        ];

        // الخطوة 3: تحويل الرموز الرياضية بدون إحاطتها بـ \text
        $mathSymbols = [
            '→' => '\\to',
            '≤' => '\\leq',
            '≥' => '\\geq',
            '≠' => '\\neq',
            '±' => '\\pm',
            '∓' => '\\mp',
            '×' => '\\times',
            '÷' => '\\div',
            '∞' => '\\infty',
//            '∫' => '\\int',
            '∬' => '\\iint',
            '∭' => '\\iiint',
            '∮' => '\\oint',
            '∂' => '\\partial',
            '∇' => '\\nabla',
            '∑' => '\\sum',
            '∏' => '\\prod',
            '√' => '\\sqrt',
            '∈' => '\\in',
            '∉' => '\\notin',
            '⊂' => '\\subset',
            '⊆' => '\\subseteq',
            '⊃' => '\\supset',
            '⊇' => '\\supseteq',
            '∪' => '\\cup',
            '∩' => '\\cap',
            '∅' => '\\emptyset',
            '∀' => '\\forall',
            '∃' => '\\exists',
            '∄' => '\\nexists',
            '∴' => '\\therefore',
            '∵' => '\\because',
            '≈' => '\\approx',
            '≡' => '\\equiv',
            '≅' => '\\cong',
            '⊥' => '\\perp',
            '∥' => '\\parallel',
            '∠' => '\\angle',
        ];

        // تنفيذ التحويلات
        foreach ($greekSymbols as $symbol => $latex) {
            $formula = str_replace($symbol, $latex, $formula);
        }

        foreach ($mathSymbols as $symbol => $latex) {
            $formula = str_replace($symbol, $latex, $formula);
        }

        // الخطوة 4: تحويل الكسور بشكل صحيح
        $formula = preg_replace_callback('/\(([^)]+)\)\/([^→=\+\s]+)/', function($matches) {
            $numerator = $this->processInnerFormula($matches[1]);
            $denominator = trim($matches[2]);
            return '\\frac{' . $numerator . '}{' . $denominator . '}';
        }, $formula);

        // الكسور البسيطة
        $formula = preg_replace_callback('/(\d+)\/([^\s\+\-\*\/]+)/', function($matches) {
            return '\\frac{' . $matches[1] . '}{' . $matches[2] . '}';
        }, $formula);

        // الخطوة 5: تحويل التكاملات
        $formula = preg_replace_callback('/∫_([^▒]+)\^([^▒]+)▒〖([^〗]+)〗/', function($matches) {
            $innerFormula = $this->processInnerFormula($matches[3]);
            return '\\int_{' . $matches[1] . '}^{' . $matches[2] . '} ' . $innerFormula . ' \\, dx';
        }, $formula);

        // الخطوة 6: تحويل الجذور
        $formula = preg_replace('/√\(([^)]+)\)/', '\\sqrt{$1}', $formula);
        $formula = preg_replace('/√([A-Za-z0-9]+)/', '\\sqrt{$1}', $formula);

        // جذر تكعيبي
        $formula = preg_replace('/∛\(([^)]+)\)/', '\\sqrt[3]{$1}', $formula);
        $formula = preg_replace('/∛([A-Za-z0-9]+)/', '\\sqrt[3]{$1}', $formula);

        // جذر رابع
        $formula = preg_replace('/∜\(([^)]+)\)/', '\\sqrt[4]{$1}', $formula);
        $formula = preg_replace('/∜([A-Za-z0-9]+)/', '\\sqrt[4]{$1}', $formula);

        // الخطوة 7: تحويل الأسس
        $formula = preg_replace_callback('/([A-Za-z0-9]+)\^(\(([^)]+)\)|([A-Za-z0-9]+))/', function($matches) {
            $base = $matches[1];
            $exponent = !empty($matches[3]) ? $matches[3] : $matches[4];
            $exponent = trim($exponent);
            return $base . '^{' . $exponent . '}';
        }, $formula);

        // الخطوة 8: تحويل subscript _n إلى _{n}
        $formula = preg_replace('/([A-Za-z])_([A-Za-z0-9])/', '$1_{$2}', $formula);

        // إزالة أي تكرار في الوسوم
        $formula = str_replace('\\\\', '\\', $formula);

        return $formula;
    }

    /**
     * معالجة الصيغ الداخلية
     *
     * @param string $formula
     * @return string
     */
    private function processInnerFormula($formula)
    {
        // تنظيف وإزالة الرموز غير المطلوبة والفراغات الزائدة
        $formula = str_replace(['〖', '〗'], '', $formula);
        $formula = trim($formula);

        // تحويل الكسور الداخلية
        $formula = preg_replace_callback('/(\d+)\/([^\s\+\-\*\/]+)/', function($matches) {
            return '\\frac{' . $matches[1] . '}{' . $matches[2] . '}';
        }, $formula);

        // تحويل الجذور
        $formula = preg_replace('/√\(([^)]+)\)/', '\\sqrt{$1}', $formula);
        $formula = preg_replace('/√([A-Za-z0-9]+)/', '\\sqrt{$1}', $formula);

        return $formula;
    }

    /**
     * اختبار التحويل للتحقق من النتائج
     */
    public function testConversion()
    {
        $tests = [
            "(_88^226)Ra→(_86^222)Rn+(_2^4)He  +Energy",
            "(_88^226)Ra→(_86^222)Rn+(_-1^0)β  +Energy",
            "U_n=(n^(2 )+1   )/2n",
            "I=∫_1^2▒〖(-1/x^2 -√x+1)dx〗",
            "α+β=γ",
            "x≤y≥z≠w",
            "a±b∓c×d÷e",
            "∫∬∭∮∂∇∑∏",
            "√(x+y)+∛x+∜y",
            "A∈B∉C⊂D⊆E⊃F⊇G",
            "X∪Y∩Z∅∀∃∄",
            "∴a=b∵c≈d≡e≅f",
            "P⊥Q∥R∠S"
        ];

        $results = [];
        foreach ($tests as $test) {
            $results[] = [
                'original' => $test,
                'latex' => $this->convertToLatex($test)
            ];
        }

        return $results;
    }
}
