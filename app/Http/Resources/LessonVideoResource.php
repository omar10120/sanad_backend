<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonVideoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'display_order' => $this->order,
            'unit_id' => $this->unit_id,
            'number_of_videos' => $this->youtube_links_count ?? $this->youtubeLinks()->count(),
        ];
    }
}
