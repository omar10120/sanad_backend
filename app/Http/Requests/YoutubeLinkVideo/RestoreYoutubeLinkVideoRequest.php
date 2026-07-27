<?php

namespace App\Http\Requests\YoutubeLinkVideo;

use Illuminate\Foundation\Http\FormRequest;

class RestoreYoutubeLinkVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer|exists:youtube_links_video,id',
        ];
    }
}
