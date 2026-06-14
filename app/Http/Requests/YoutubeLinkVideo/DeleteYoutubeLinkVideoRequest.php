<?php

namespace App\Http\Requests\YoutubeLinkVideo;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DeleteYoutubeLinkVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:youtube_links_video,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.required' => trans('main_trans.Youtube_link_video_id_required'),
            'id.integer' => trans('main_trans.Youtube_link_video_id_must_be_integer'),
            'id.exists' => trans('main_trans.Youtube_link_video_not_exists'),
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => trans('main_trans.Youtube_link_video_id'),
        ];
    }
}
