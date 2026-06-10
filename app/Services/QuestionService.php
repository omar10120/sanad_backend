<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\Lesson;
use App\Models\QuestionType;
use App\Models\Tag;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class QuestionService
{
    /**
     * Get all questions with transformed text
     */
    public function getAllQuestions()
    {
        $questions = Question::all();
        $questions->transform(function ($question) {
            $delta = $question->text_question;
            $question->questionText = $this->deltaToPlainText($delta);
            if(strlen($question->questionText) > 80) {
                $question->questionText = substr($question->questionText, 0, 80);
                $last_space_pos = strrpos($question->questionText,' ');
                $question->questionText = substr($question->questionText, 0, $last_space_pos) . '...';
            }
            return $question;
        });

        return $questions;
    }

    /**
     * Get question by UUID
     */
    public function getQuestionByUuid($uuid)
    {
        return Question::where('uuid', $uuid)->first();
    }

    /**
     * Get question by ID
     */
    public function findQuestion($id)
    {
        return Question::findOrFail($id);
    }

    /**
     * Get lesson data for question creation
     */
    public function getLessonDataForQuestion($lesson_id): array
    {
        $lesson = Lesson::where('id', $lesson_id)->first();
        $tags = Tag::where('subject_id', $lesson->subject->id)->get();
        $question_groups = $lesson->questionGroups;
        $types = QuestionType::all();

        return [
            'lesson' => $lesson,
            'tags' => $tags,
            'question_groups' => $question_groups,
            'types' => $types,
            'group_id' => null
        ];
    }

    /**
     * Get question group data for question creation
     */
    public function getQuestionGroupDataForQuestion($group_id): array
    {
        $question_group_current = QuestionGroup::where('id', $group_id)->first();
        $lesson = $question_group_current->lesson;
        $tags = Tag::where('subject_id', $lesson->subject->id)->get();
        $question_groups = $lesson->questionGroups;
        $types = QuestionType::all();

        return [
            'lesson' => $lesson,
            'tags' => $tags,
            'question_groups' => $question_groups,
            'types' => $types,
            'group_id' => $group_id
        ];
    }

    /**
     * Create a new question
     */
    public function createQuestion($data, $user_id)
    {
        DB::beginTransaction();
        try {
            // Handle question group creation or selection
            if($data['question_group_id'] == -1) {
                $delta = json_decode($data['text_question'], true);
                $delta = $this->deltaToPlainText($delta);
                if(strlen($delta) > 80) {
                    $delta = substr($delta, 0, 80);
                    $last_space_pos = strrpos($delta,' ');
                    $delta = substr($delta, 0, $last_space_pos) . '...';
                }

                $question_group = QuestionGroup::create([
                    'lesson_id' => $data['lesson_id'],
                    'name' => $delta,
                    'order' => Lesson::find($data['lesson_id'])->questionGroups->count() + 1,
                ]);
            } else {
                $question_group = QuestionGroup::where('id', $data['question_group_id'])->first();
            }

            // Create question based on type
            $question = $this->createQuestionByType($data, $question_group, $user_id);

            // Handle file uploads
            $this->handleQuestionFileUpload($question, $data);
            $this->handleHintFileUpload($question, $data);

            // Sync tags
            if(isset($data['tags'])) {
                $question->tags()->sync($data['tags']);
            }

            DB::commit();
            return $question;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update an existing question
     */
    public function updateQuestion($question_id, $data, $user_id)
    {
        DB::beginTransaction();
        try {
            $question = Question::find($question_id);
            $question_group_old = $question->questionGroup;
            $clearHint = $data['clear_hint'] ?? false;
            $clearTags = $data['clear_tags'] ?? false;

            if ($clearHint) {
                $data['hint'] = null;
            }

            // Handle question group creation or selection
            if($data['question_group_id'] == -1) {
                $delta = json_decode($data['text_question'], true);
                $delta = $this->deltaToPlainText($delta);
                if(strlen($delta) > 80) {
                    $delta = substr($delta, 0, 80);
                    $last_space_pos = strrpos($delta,' ');
                    $delta = substr($delta, 0, $last_space_pos) . '...';
                }

                $question_group = QuestionGroup::create([
                    'lesson_id' => $data['lesson_id'],
                    'name' => $delta,
                    'order' => Lesson::find($data['lesson_id'])->questionGroups->count() + 1,
                ]);
            } else {
                $question_group = QuestionGroup::where('id', $data['question_group_id'])->first();
            }

            // Update question based on type
            $this->updateQuestionByType($question, $data, $question_group, $user_id);

            // Handle file uploads
            $this->handleQuestionFileUpload($question, $data);
            $this->handleHintFileUpload($question, $data);

            // Sync tags
            if($clearTags) {
                $question->tags()->sync([]);
            } elseif(array_key_exists('tags', $data)) {
                $question->tags()->sync($data['tags'] ?? []);
            }

            // Clean up old question group if empty
            if ($question_group_old->questions->count() == 0) {
                $question_group_old->delete();
            }
            $this->orderQuestion($question_group_old);

            DB::commit();
            return $question;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Delete a question
     */
    public function deleteQuestion($question_id)
    {
        $question = Question::find($question_id);
        $question_group = $question->questionGroup;
        $question->delete();
        $this->orderQuestion($question_group);
        return true;
    }

    /**
     * Move question to new position
     */
    public function moveQuestion($question_group_id, $question_id, $new_position)
    {
        $questionGroup = QuestionGroup::find($question_group_id);
        $question = $questionGroup->questions()->findOrFail($question_id);
        $currentPosition = $question->order;

        if($new_position > $questionGroup->questions()->count()) {
            $new_position = $questionGroup->questions()->count();
        }
        if($new_position < 1) {
            $new_position = 1;
        }

        DB::transaction(function () use ($questionGroup, $question, $currentPosition, $new_position) {
            if ($new_position < $currentPosition) {
                // Move up
                $questionGroup->questions()
                    ->where('order', '>=', $new_position)
                    ->where('order', '<', $currentPosition)
                    ->increment('order');
            } else {
                // Move down
                $questionGroup->questions()
                    ->where('order', '>', $currentPosition)
                    ->where('order', '<=', $new_position)
                    ->decrement('order');
            }

            $question->update(['order' => $new_position]);
        });

        return true;
    }

    /**
     * Reorder questions in a group
     */
    private function orderQuestion($question_group): void
    {
        $questions = $question_group->questions()
            ->orderBy('order')
            ->get();
        $index = 1;
        foreach ($questions as $question){
            $question->order = $index++;
            $question->save();
        }
    }

    /**
     * Convert delta to plain text
     */
    private function deltaToPlainText($delta): string
    {
        if (is_string($delta)) {
            $delta = json_decode($delta, true);
        }

        if (empty($delta['ops'])) {
            return '';
        }

        $plainText = '';
        foreach ($delta['ops'] as $op) {
            if (isset($op['insert']) && is_string($op['insert'])) {
                $plainText .= $op['insert'];
            }
        }

        return $plainText;
    }

    /**
     * Create question based on type
     */
    private function createQuestionByType($data, $question_group, $user_id)
    {
        $choices_true_false = json_decode('[{"ops":[{"attributes":{"bold":true},"insert":"\u0635\u062d"},{"insert":"\n"}]}, {"ops":[{"attributes":{"bold":true},"insert":"\u063a\u0644\u0637"},{"insert":"\n"}]}]');

        $questionType = QuestionType::find($data['type_id']);
        $type = $questionType ? $questionType->type : null;

        if ($type === QuestionType::TYPE_AUTOMATION) {
            return Question::create([
                'type_id' => $data['type_id'],
                'created_by' => $user_id,
                'text_question' => json_decode($data['text_question'], true),
                'choices' => json_decode($data['choices'], true),
                'right_choice' => $data['correctAnswer'],
                'hint' => isset($data['hint']) ? json_decode($data['hint'], true) : null,
                'question_group_id' => $question_group->id,
                'order' => $question_group->questions->count() + 1,
            ]);
        } elseif ($type === QuestionType::TYPE_TRUE_OR_FALSE) {
            return Question::create([
                'type_id' => $data['type_id'],
                'created_by' => $user_id,
                'text_question' => json_decode($data['text_question'], true),
                'choices' => $choices_true_false,
                'right_choice' => $data['correctAnswer'],
                'hint' => isset($data['hint']) ? json_decode($data['hint'], true) : null,
                'question_group_id' => $question_group->id,
                'order' => $question_group->questions->count() + 1,
            ]);
        } else {
            return Question::create([
                'type_id' => $data['type_id'],
                'created_by' => $user_id,
                'text_question' => json_decode($data['text_question'], true),
                'choices' => json_decode('[' . $data['correctAnswer'] . ']', true),
                'right_choice' => 1,
                'hint' => isset($data['hint']) ? json_decode($data['hint'], true) : null,
                'question_group_id' => $question_group->id,
                'order' => $question_group->questions->count() + 1,
            ]);
        }
    }

    /**
     * Update question based on type
     */
    private function updateQuestionByType($question, $data, $question_group, $user_id)
    {
        $choices_true_false = json_decode('[{"ops":[{"attributes":{"bold":true},"insert":"\u0635\u062d"},{"insert":"\n"}]}, {"ops":[{"attributes":{"bold":true},"insert":"\u063a\u0644\u0637"},{"insert":"\n"}]}]');

        $questionType = QuestionType::find($data['type_id']);
        $type = $questionType ? $questionType->type : null;

        if ($type === QuestionType::TYPE_AUTOMATION) {
            $question->update([
                'type_id' => $data['type_id'],
                'created_by' => $user_id,
                'text_question' => json_decode($data['text_question'], true),
                'choices' => json_decode($data['choices'], true),
                'right_choice' => $data['correctAnswer'],
                'hint' => isset($data['hint']) ? json_decode($data['hint'], true) : null,
                'question_group_id' => $question_group->id,
                'is_edited' => $data['is_edited'] ?? false,
//                'order' => $question_group->questions->count() + 1,
            ]);
        } elseif ($type === QuestionType::TYPE_TRUE_OR_FALSE) {
            $question->update([
                'type_id' => $data['type_id'],
                'created_by' => $user_id,
                'text_question' => json_decode($data['text_question'], true),
                'choices' => $choices_true_false,
                'right_choice' => $data['correctAnswer'],
                'hint' => isset($data['hint']) ? json_decode($data['hint'], true) : null,
                'question_group_id' => $question_group->id,
                'is_edited' => $data['is_edited'] ?? false,
//                'order' => $question_group->questions->count() + 1,
            ]);
        } else {
            $question->update([
                'type_id' => $data['type_id'],
                'created_by' => $user_id,
                'text_question' => json_decode($data['text_question'], true),
                'choices' => json_decode($data['choices'], true),
                'right_choice' => 1,
                'hint' => isset($data['hint']) ? json_decode($data['hint'], true) : null,
                'question_group_id' => $question_group->id,
                'is_edited' => $data['is_edited'] ?? false,
//                'order' => $question_group->questions->count() + 1,
            ]);
        }
    }

    /**
     * Handle question photo upload
     */
    private function handleQuestionFileUpload($question, $data)
    {
        if(isset($data['question_photo']) && $data['question_photo']->isValid()) {
            $extension = $data['question_photo']->getClientOriginalExtension();
            $newFileName = 'question-photo-' . $question->id . '-' . Carbon::now()->format('Ymd_His') . '.' . $extension;

            $question->update([
                'question_photo' => $newFileName,
            ]);

            $data['question_photo']->move(public_path('assets/image/Question/'.$question->id.'/question-photo'), $newFileName);
        }
    }

    /**
     * Handle hint photo upload
     */
    private function handleHintFileUpload($question, $data)
    {
        if(isset($data['hint_photo']) && $data['hint_photo']->isValid()) {
            $extension = $data['hint_photo']->getClientOriginalExtension();
            $newFileName = 'hint-photo-' . $question->id . '-' . Carbon::now()->format('Ymd_His') . '.' . $extension;

            $question->update([
                'hint_photo' => $newFileName,
            ]);

            $data['hint_photo']->move(public_path('assets/image/Question/'.$question->id.'/hint-photo'), $newFileName);
        }
    }
}
