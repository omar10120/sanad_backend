<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $photo = null;
        if ($this->photo) {
            $photo = asset('assets/image/Teachers/' . $this->id . '/' . $this->photo);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'estimation_time' => $this->estimation_time,
            'whatsapp_link' => $this->whatsapp_link,
            'instagram_link' => $this->instagram_link,
            'phone' => $this->phone,
            'photo' => $photo,
            'price' => $this->price,
            'description' => $this->description,
            'display_order' => $this->pivot?->order,
            'number_of_units' => $this->units_count ?? $this->units()->count(),
        ];
    }
}
