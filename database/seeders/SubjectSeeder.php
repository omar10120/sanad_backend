<?php

namespace Database\Seeders;

use App\Models\QuestionType;
use App\Models\Subject;
use App\Models\Type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //------start------ TypeTableSeeder -------------//

        $textTypes = [
            'بكلوريا علمي',
            'بكلوريا أدبي',
        ];

        foreach ($textTypes as $textType) {
            Type::create(['name' => $textType]);
        }

        //-------end------- TypeTableSeeder -------------//

        //------start----- SubjectTableSeeder -----------//

        $subject1 = Subject::create([
            'name' => 'الرياضيات',
            'icon' => 'search',
            'link' => 'telegram.com/math',
            'teacher' => 'sanad',
            'description' => 'it is good',
        ]);

        $subject2 = Subject::create([
            'name' => 'English',
            'icon' => 'abc',
            'link' => 'telegram.com/english',
            'teacher' => 'sanad',
            'description' => 'it is good',
        ]);

        $types = Type::all();

        $subject1->types()->sync([$types[0]->id]);
        $subject2->types()->sync([$types[0]->id,$types[1]->id]);

        //-------end------ SubjectTableSeeder -----------//

        //----start------ QuestionTypeTableSeeder -------//

        $QuestionTypes = [
            'اختر الإجابة',
            'صح أو غلط',
            'عرف',
            'عدد',
            'فسر',
        ];

        foreach ($QuestionTypes as $QuestionType) {
            QuestionType::create(['name' => $QuestionType]);
        }

        //-------end---- QuestionTypeTableSeeder --------//
    }
}
