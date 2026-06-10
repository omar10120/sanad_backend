<?php

namespace App\Services;

use App\Http\Resources\TypeHasSubjectResource;
use App\Models\Subject;
use App\Models\Type;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\QuestionType;
use App\Models\Tag;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class SubjectService
{
    /**
     * Get all subjects
     */
    public function getAllSubjects(): Collection
    {
        return Subject::orderBy('order')->get();
    }

    /**
     * Get active subjects only
     */
    public function getActiveSubjects(): Collection
    {
        return Subject::where('is_active', 1)->orderBy('order')->get();
    }

    /**
     * Get subjects for authenticated user
     */
    public function getSubjectsForUser(): Collection
    {
        $user = Auth::user();

        // Check if user has owner role - they have access to all subjects
        if ($user->hasRole('Owner')) {
            return Subject::orderBy('order')->get();
        }

        // For other users, get only subjects linked to them via user_has_subject table
        return $user->subjects()->orderBy('order')->get();
    }

    /**
     * Create a new subject
     */
    public function createSubject(array $data): Subject
    {
        $data['icon_photo'] = null;
        $subject = Subject::create($data);

        if (isset($data['types'])) {
            $subject->types()->sync($data['types']);
        }

        return $subject;
    }

    /**
     * Find subject by ID
     */
    public function findSubject(int $id): ?Subject
    {
        return Subject::findOrFail($id);
    }

    /**
     * Update subject
     */
    public function updateSubject(int $id, array $data, ?string $newPhotoFileName = null): bool
    {
        $subject = Subject::findOrFail($id);
        if (!$subject) {
            return false;
        }

        // If a new photo filename is provided, add it to the data
        if ($newPhotoFileName) {
            $data['icon_photo'] = $newPhotoFileName;
        }

        $subject->update($data);

        if (isset($data['types'])) {
            $subject->types()->sync($data['types']);
        }

        return true;
    }

    /**
     * Delete subject with validation
     */
    public function deleteSubject(int $id): array
    {
        $subject = Subject::findOrFail($id);

        if (!$subject->canBeDeleted()) {
            return [
                'success' => false,
                'message' => trans('main_trans.Subject_has_related_data'),
            ];
        }

        try {
            $subject->delete();
            return [
                'success' => true,
                'message' => trans('main_trans.Subject_delete_successfully')
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Toggle subject active status
     */
    public function toggleSubjectStatus(Subject $subject): bool
    {
        return $subject->update(['is_active' => !$subject->is_active]);
    }

    /**
     * Handle subject photo upload
     */
    public function handlePhotoUpload(Subject $subject, $photoFile): ?string
    {
        if (!$photoFile || !$photoFile->isValid()) {
            return null;
        }

        $extension = $photoFile->getClientOriginalExtension();
        $newFileName = 'subject-' . $subject->id . '-' . Carbon::now()->format('Ymd_His') . '.' . $extension;

        // Create directory if it doesn't exist
        $uploadPath = public_path('assets/image/Subjects/' . $subject->id);
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        try {
            $photoFile->move($uploadPath, $newFileName);
            return $newFileName;
        } catch (\Exception $e) {
            Log::error('Subject photo upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete subject photo
     */
    public function deleteSubjectPhoto(int $subjectId, string $photoName): bool
    {
        $path = public_path('assets/image/Subjects/' . $subjectId . '/' . $photoName);
        if (file_exists($path)) {
            return unlink($path);
        }
        return false;
    }

    /**
     * Get subject lessons
     */
    public function getSubjectLessons(int $subjectId): Collection
    {
        return Lesson::where('subject_id', $subjectId)->orderBy('order')->get();
    }

    /**
     * Get subject tags
     */
    public function getSubjectTags(int $subjectId): Collection
    {
        return Tag::where('subject_id', $subjectId)
            ->orderBy('order')
            ->get();
    }

    /**
     * Get edited questions for subject
     */
    public function getEditedQuestions(int $subjectId): Collection
    {
        return Question::with(['lesson.subject'])
            ->where('is_edited', 1)
            ->whereHas('lesson.subject', fn($q) => $q->where('id', $subjectId))
            ->get();
    }

    /**
     * Get all subject data for API
     */
    public function getAllSubjectData(int $subjectId, bool $isLocked = false): array
    {
        $subject = Subject::findOrFail($subjectId);
        $lessons = $subject->lessons()->where('is_active', 1)->get();

        if ($isLocked) {
            return $this->getLockedSubjectData($subject, $lessons);
        }

        return $this->getUnlockedSubjectData($subject, $lessons);
    }

    /**
     * Get locked subject data (first lesson only)
     */
    private function getLockedSubjectData(Subject $subject, Collection $lessons): array
    {
        $firstLesson = $subject->lessons()->first();

        if (!$firstLesson) {
            return [
                'subjects' => $subject,
                'lessons' => collect(),
                'question_groups' => collect(),
                'questions' => collect(),
                'tags' => collect(),
                'question_tags' => collect(),
                'is_locked' => true,
            ];
        }

        $questionGroups = QuestionGroup::where('lesson_id', $firstLesson->id)
            ->orderBy('order')
            ->get();

        $questionIds = $questionGroups->pluck('id');

        $questions = Question::whereIn('question_group_id', $questionIds)
            ->orderBy('order')
            ->get();

        $tags = $subject->tags()->get();

        $questionTags = DB::table('tag_has_question')
            ->whereIn('question_id', $questions->pluck('id'))
            ->get();

        return [
            'subjects' => $subject,
            'lessons' => $lessons,
            'question_groups' => $questionGroups,
            'questions' => $questions,
            'tags' => $tags,
            'question_tags' => $questionTags,
            'is_locked' => true,
        ];
    }

    /**
     * Get unlocked subject data (all lessons)
     */
    private function getUnlockedSubjectData(Subject $subject, Collection $lessons): array
    {
        $questionGroups = QuestionGroup::whereHas('lesson', fn($q) => $q->where('subject_id', $subject->id)->where('is_active', 1))
            ->orderBy('order')
            ->get();

        $questions = Question::whereHas('questionGroup.lesson', fn($q) => $q->where('subject_id', $subject->id)->where('is_active', 1))
            ->orderBy('order')
            ->get();

        $tags = $subject->tags()->get();

        $questionTags = DB::table('tag_has_question')
            ->whereIn('question_id',
                Question::whereHas('questionGroup.lesson', fn($q) => $q->where('subject_id', $subject->id)->where('is_active', 1))
                    ->pluck('id')
            )->get();

        return [
            'subjects' => $subject,
            'lessons' => $lessons,
            'question_groups' => $questionGroups,
            'questions' => $questions,
            'tags' => $tags,
            'question_tags' => $questionTags,
            'is_locked' => false,
        ];
    }

    /**
     * Get all types
     */
    public function getAllTypes(): Collection
    {
        return Type::all();
    }

    /**
     * Get all question types
     */
    public function getAllQuestionTypes(): Collection
    {
        return QuestionType::all();
    }

    /**
     * Get type_has_subject relationships for a student.
     */

    public function getTypeHasSubjectRelationshipsByType(int $typeId): AnonymousResourceCollection
    {
        $relationships = DB::table('type_has_subject')
            ->join('types', 'type_has_subject.type_id', '=', 'types.id')
            ->where('type_id', $typeId)
            ->join('subjects', 'type_has_subject.subject_id', '=', 'subjects.id')
            ->where('subjects.is_active', 1)
            ->select(
                'type_has_subject.type_id',
                'type_has_subject.subject_id',
                'types.id as type_id',
                'types.name as type_name',
                'subjects.id as subject_id',
                'subjects.name as subject_name',
            )
            ->get();

            return TypeHasSubjectResource::collection($relationships);
    }

    /**
     * Get all type_has_subject relationships for admin
     */
    public function getTypeHasSubjectRelationships(): AnonymousResourceCollection
    {
        $relationships = DB::table('type_has_subject')
            ->select('type_id', 'subject_id')
            ->get();

        return TypeHasSubjectResource::collection($relationships);
    }


}
