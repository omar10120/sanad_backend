<?php

namespace App\Http\Resources;

use App\Models\SubjectVideo;
use App\Models\Unit;
use App\Services\ApiSubjectVideoService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class SubjectVideoResource extends JsonResource
{
    protected ?bool $isLocked = null;

    public function __construct($resource, ?bool $isLocked = null)
    {
        parent::__construct($resource);
        $this->isLocked = $isLocked;
    }

    public function toArray(Request $request): array
    {
        $student = Auth::user();
        $subjectVideo = SubjectVideo::withCount([
            'teachers',
        ])->find($this->id);

        $apiService = app(ApiSubjectVideoService::class);
        $isLocked = $this->isLocked ?? ($student ? !$subjectVideo->checkStudentAccess($student->id) : true);
        

        $unitsCount = Unit::whereHas('teacher.subjectVideos', function ($query) use ($subjectVideo) {
            $query->where('subjects_video.id', $subjectVideo->id);
        })->count();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_order' => $this->order,
            'icon' => $this->icon,
            'link' => $this->link,
            'description' => $this->description,
            'number_of_teachers' => $subjectVideo->teachers_count ?? $subjectVideo->teachers()->count(),
            'number_of_units' => $unitsCount,
            'is_locked' => $isLocked,
            'expires_at' => $student ? $apiService->getStudentValidToDate($subjectVideo, $student->id) : null,
        ];
    }
}
