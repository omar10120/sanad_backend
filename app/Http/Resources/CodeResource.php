<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
//            'subjects' => SubjectOnlyNameResource::collection($this->package->subjects->select('name')),
            'subjects' => $this->package->subjects->pluck('name')->toArray(),
            'expires_at' => $this->package->expires_at,
        ];

    }
}
