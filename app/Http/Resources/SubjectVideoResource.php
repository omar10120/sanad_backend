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

    private function hexToFlutterColor($hex): string
    {
        $hex = ltrim($hex, '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return 'FF' . strtoupper($hex);
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

        if($this->icon_photo == null)
            $icon_photo = null;
        else
            $icon_photo = asset('assets/image/SubjectVideos/' . $this->id . '/' . $this->icon_photo);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_order' => $this->order,
            'icon' => $this->icon,
            'icon_photo'=> $icon_photo,
            'light_color_code' => $this->light_color_code ? $this->hexToFlutterColor($this->light_color_code) : null,
            'dark_color_code' => $this->dark_color_code ? $this->hexToFlutterColor($this->dark_color_code) : null,
            'link' => $this->link,
            'description' => $this->description,
            'number_of_teachers' => $subjectVideo->teachers_count ?? $subjectVideo->teachers()->count(),
            'number_of_units' => $unitsCount,
            'is_locked' => $isLocked,
            'expires_at' => $student ? $apiService->getStudentValidToDate($subjectVideo, $student->id) : null,
        ];
    }
}
