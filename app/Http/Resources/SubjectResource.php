<?php

namespace App\Http\Resources;

use App\Models\Subject;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubjectResource extends JsonResource
{
    protected bool $is_locked;

    public function __construct($resource, $is_locked = null)
    {
        parent::__construct($resource);
        $this->is_locked = $is_locked;
    }

    private function hexToFlutterColor($hex): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return 'FF' . strtoupper($hex);
    }

    private function getStudentValidToDate(Subject $subject): ?string
    {
        $student = Auth::user();

        if (!$student) {
            return null;
        }

        if($student->type_id == 7 || $student->type_id == 11)
            $validToDate = "1-10-2026";
        else
            $validToDate = $this->codePackages()
                ->whereHas('codes', function($query) use ($student) {
                    $query->where('student_id', $student->id);
                })
                ->where('expires_at', '>', now())
                ->where('subject_id', $subject->id)
                ->max('expires_at');

        return $validToDate ? Carbon::parse($validToDate)->format('Y-m-d') : null;
    }

    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray(Request $request, $is_locked = null): array
    {
        $subject = Subject::find($this->id);
        $subject1 = Subject::withCount('lessons')->with(['lessons' => function ($query) {$query->withCount('questionGroups');}])->find($subject->id);

        if($this->icon_photo == null)
            $icon_photo = null;
        else
            $icon_photo = asset('assets/image/Subjects/' . $this->id . '/' . $this->icon_photo);

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'display_order' => $this->order,
            'icon'=> $this->icon,
            'icon_photo'=> $icon_photo,
            'light_color_code' => $this->light_color_code ? $this->hexToFlutterColor($this->light_color_code) : null,
            'dark_color_code' => $this->dark_color_code ? $this->hexToFlutterColor($this->dark_color_code) : null,
            'link'=> $this->link,
            'number_of_lessons'=> $subject1->lessons()->where('is_active',1)->count(),
            'number_of_tags'=> $subject->tags()->where('is_exam',0)->count(),
            'number_of_exams'=> $subject->tags()->where('is_exam',1)->count(),
            'number_of_questions'=> $subject->questionsCount(),
            'teacher'=> $this->teacher,
            'description'=> $this->description,
            'is_locked'=> !($subject->checkStudentAccess(Auth::user()->id)),
            'expires_at' => $this->getStudentValidToDate($subject),
        ];

        return $data;
    }

}
