<?php

namespace App\Http\Resources;

use App\Services\ApiSubjectVideoService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UnitResource extends JsonResource
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
        $apiService = app(ApiSubjectVideoService::class);

        $locked = $this->isLocked;
        if ($locked === null && $student) {
            $locked = !$apiService->studentHasUnitAccess($student->id, $this->id);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_order' => $this->order,
            'teacher_id' => $this->teacher_id,
            'number_of_lessons' => $this->lesson_videos_count ?? $this->lessonVideos()->count(),
            'is_locked' => (bool) $locked,
        ];
    }
}
