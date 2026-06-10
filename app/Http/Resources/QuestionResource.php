<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\QuestionConverter;

class QuestionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->question_photo == null)
            $question_photo = null;
        else
            $question_photo = asset('assets/image/Question/' . $this->id . '/question-photo/' . $this->question_photo);

        if($this->hint_photo == null)
            $hint_photo = null;
        else
            $hint_photo = asset('assets/image/Question/' . $this->id . '/hint-photo/' . $this->hint_photo);

        $text_question = $this->cleanDelta($this->text_question);
        $hint = $this->cleanDelta($this->hint);
        $choices = $this->cleanChoices($this->choices);

        return [
            'id' => $this->id,
            'uuid'=> $this->uuid,
            'question_group_id'=> $this->question_group_id,
            'display_order'=> $this->order,
            'type_id'=> $this->type_id,
            'type_type'=> $this->typeQuestion->type,
            'question_text'=> $text_question,
            'question_photo'=> $question_photo,
            'choices'=> $choices,
            'right_choice'=> $this->right_choice,
            'is_edited'=> $this->is_edited,
            'hint'=> $hint,
            'hint_photo'=> $hint_photo,
        ];
    }

    private function cleanDelta($delta)
    {
        if (is_null($delta)) return null;

        if (is_string($delta)) {
            $data = json_decode($delta, true);
        }
        elseif (is_array($delta)) {
            $data = $delta;
        }
        else {
            return $delta;
        }

        if (!isset($data['ops']) || !is_array($data['ops'])) {
            return json_encode($data);
        }

        foreach ($data['ops'] as &$op) {
            if (isset($op['attributes'])) {
                unset($op['attributes']['color']);
                unset($op['attributes']['align']);
                unset($op['attributes']['direction']);
                unset($op['attributes']['background']);

                if (empty($op['attributes'])) {
                    unset($op['attributes']);
                }
            }
            if (isset($op['insert']['formula'])) {
                if (!isset($op['attributes'])) {
                    $op['attributes'] = [];
                }
                $op['attributes']['direction'] = 'ltr';
                $op['attributes']['unicode-bidi'] = 'isolate';
            }
        }

        return $data;
    }

    private function cleanChoices($choices): ?array
    {
        if (is_null($choices)) return null;

        if (is_string($choices)) {
            $choicesArray = json_decode($choices, true);
        } elseif (is_array($choices)) {
            $choicesArray = $choices;
        } else {
            return null;
        }

        if (!is_array($choicesArray)) {
            return null;
        }

        $cleanedChoices = [];
        foreach ($choicesArray as $choice) {
            $choiceJson = is_array($choice) ? json_encode($choice) : $choice;
            $cleanedChoices[] = $this->cleanDelta($choiceJson);
        }
        return $cleanedChoices;
    }
}
