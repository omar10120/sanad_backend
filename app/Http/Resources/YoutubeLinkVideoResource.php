<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\GetVideoTimePerSecond;
class YoutubeLinkVideoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_order' => $this->order,
            'youtube_link' => $this->youtube_link,
            'video_time' => $this->video_time,
            'video_time_persecond' => GetVideoTimePerSecond::getVideoTimePerSecond($this->video_time),
            'is_active' => $this->is_active,
            'lesson_video_id' => $this->lesson_video_id,
        ];
    }
}
