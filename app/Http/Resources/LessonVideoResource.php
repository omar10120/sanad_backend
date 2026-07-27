<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonVideoResource extends JsonResource
{
    protected ?bool $isLocked = null;

    public function __construct($resource, ?bool $isLocked = null)
    {
        parent::__construct($resource);
        $this->isLocked = $isLocked;
    }

    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'display_order' => $this->order,
            'unit_id' => $this->unit_id,
            'is_active' => $this->is_active,
            'number_of_videos' => $this->youtube_links_count ?? $this->youtubeLinks->where('is_active',true)->count(),
            'youtube_links' => YoutubeLinkVideoResource::collection($this->youtubeLinks->where('is_active', true)),
        ];

        if ($this->relationLoaded('youtubeLinks')) {
            $youtubeLinks = $this->youtubeLinks;

            // if ($this->isLocked) {
            //     $youtubeLinks = $youtubeLinks->take(1);
            // }

            $data['youtube_links'] = YoutubeLinkVideoResource::collection($youtubeLinks->where('is_active', true));
        }

        return $data;
    }
}
