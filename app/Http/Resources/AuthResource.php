<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->photo == null)
            $photo = null;
        else
            $photo = asset('assets/image/Students/' . $this->id . '/' . $this->photo);

        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'father_name' => $this->father_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'city' => $this->city,
            'school' => $this->school,
            'certificate' => TypeResource::make($this->type),
            'photo' => $photo,
        ];
    }
}
