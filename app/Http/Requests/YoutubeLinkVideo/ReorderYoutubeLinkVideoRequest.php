<?php

namespace App\Http\Requests\YoutubeLinkVideo;

use Illuminate\Foundation\Http\FormRequest;

class ReorderYoutubeLinkVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'exists:youtube_links_video,id',
        ];
    }
}
