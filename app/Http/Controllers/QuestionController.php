<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Traits\HasPermissionChecks;
use App\Services\QuestionService;
use App\Services\SubjectService;
use App\Http\Requests\Question\StoreQuestionRequest;
use App\Http\Requests\Question\UpdateQuestionRequest;
use App\Http\Requests\Question\DeleteQuestionRequest;
use App\Http\Requests\Question\MoveQuestionRequest;
use App\Http\Requests\Question\ReorderQuestionRequest;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\QuestionType;
use App\Models\Subject;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Routing\Redirector;

class QuestionController extends Controller
{
    use HasPermissionChecks;

    protected QuestionService $questionService;
    protected SubjectService $subjectService;

    public function __construct(QuestionService $questionService, SubjectService $subjectService)
    {
        $this->questionService = $questionService;
        $this->subjectService = $subjectService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission(PermissionEnum::QUESTION_SHOW);

        $lesson_selected = new Lesson();
        $questions = $this->questionService->getAllQuestions();

        return view('question.questions', compact('questions', 'lesson_selected'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, $lesson_id)
    {
        $this->checkPermission(PermissionEnum::QUESTION_ADD);

        $data = (object)$this->questionService->getLessonDataForQuestion($lesson_id);

        return view('question.add_question', compact(
            'data'
        ));
    }

    /**
     * Show the form for creating a new resource in a specific group.
     */
    public function createInGroup(Request $request, $group_id)
    {
        $this->checkPermission(PermissionEnum::QUESTION_ADD);

        $data = (object)$this->questionService->getQuestionGroupDataForQuestion($group_id);

        return view('question.add_question', compact(
            'data'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @throws PermissionException
     */
    public function store(StoreQuestionRequest $request)
    {
        $this->checkPermission(PermissionEnum::QUESTION_ADD);

        try {
            $this->questionService->createQuestion($request->validated(), $request->user()->id);
            session()->flash('add', trans('main_trans.Question_add_successfully'));
            return back();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     * @throws PermissionException
     */
    public function show(Request $request, $question_uuid): View|Application|Factory
    {
        $this->checkPermission(PermissionEnum::QUESTION_EDIT);

        $question = $this->questionService->getQuestionByUuid($question_uuid);
        if($question == null) {
            return abort(404);
        }

        $current_question_group = $question->questionGroup;
        $lesson = Lesson::where('id', $current_question_group->lesson->id)->first();
        $question_groups = $lesson->questionGroups;
        $tags = Tag::where('subject_id', $lesson->subject->id)->get();
        $subjects = $this->subjectService->getSubjectsForUser();
        $lessons = $this->subjectService->getSubjectLessons($lesson->subject->id);
        $types = QuestionType::all();

        return view('question.edit_question', compact(
            'question',
            'current_question_group',
            'question_groups',
            'lesson',
            'lessons',
            'subjects',
            'tags',
            'types',
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @throws PermissionException
     */
    public function edit(Request $request, $question_id): View|Application|Factory
    {
        $this->checkPermission(PermissionEnum::QUESTION_EDIT);

        $question = $this->questionService->findQuestion($question_id);
        $current_question_group = $question->questionGroup;
        $lesson = Lesson::where('id', $current_question_group->lesson->id)->first();
        $question_groups = $lesson->questionGroups;
        $tags = Tag::where('subject_id', $lesson->subject->id)->get();
        $subjects = $this->subjectService->getSubjectsForUser();
        $lessons = $this->subjectService->getSubjectLessons($lesson->subject->id);
        $types = QuestionType::all();

        $nextQuestion = $this->getNextQuestion($question);

        return view('question.edit_question', compact(
            'question',
            'current_question_group',
            'question_groups',
            'lesson',
            'lessons',
            'subjects',
            'tags',
            'types',
            'nextQuestion',
        ));
    }

    /**
     * Update the specified resource in storage.
     * @throws PermissionException
     */
    public function update(UpdateQuestionRequest $request, Question $question): Application|Redirector|RedirectResponse
    {
        $this->checkPermission(PermissionEnum::QUESTION_EDIT);

        try {
            $this->questionService->updateQuestion($request->id, $request->validated(), $request->user()->id);
            session()->flash('edit', trans('main_trans.Question_edit_successfully'));

            // Refresh question to get updated data
            $question->refresh();

            // Handle redirect based on action
            if ($request->has('action')) {
                if ($request->action === 'save_and_next') {
                    // Get next question
                    $nextQuestion = $this->getNextQuestion($question);
                    if ($nextQuestion) {
                        return redirect()->route('question.edit', $nextQuestion->id);
                    }
                }
                // elseif ($request->action === 'save_and_back') {
                //     // Return to previous page
                //     return redirect()->back();
                // }
            }

            return redirect(url('lesson/' . $request->lesson_id . '/question-group'));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Get the next question in the same lesson and question group.
     * If it's the last question in the group, return the first question in the next group within the same lesson.
     * If it's the last question in the last group of the lesson, return null.
     */
    private function getNextQuestion(Question $currentQuestion)
    {
        $nextQuestion = Question::where('question_group_id', $currentQuestion->question_group_id)
            ->where('order', '>', $currentQuestion->order)
            ->orderBy('order', 'asc')
            ->first();

        if ($nextQuestion) {
            return $nextQuestion;
        }

        $currentGroup = $currentQuestion->questionGroup ?? $currentQuestion->group ?? null;
        if (!$currentGroup) {
            return null;
        }

        $lessonId = $currentGroup->lesson_id ?? $currentQuestion->lesson_id ?? null;
        if (!$lessonId) {
            return null;
        }

        $questionGroups = QuestionGroup::where('lesson_id', $lessonId)
            ->orderBy('order', 'asc')
            ->get();

        $groupIndex = $questionGroups->search(function($group) use ($currentGroup) {
            return $group->id == $currentGroup->id;
        });

        if ($groupIndex === false || $groupIndex === null) {
            return null;
        }

        $nextGroup = $questionGroups->get($groupIndex + 1);

        if (!$nextGroup) {
            return null;
        }

        $firstInNextGroup = $nextGroup->questions()->orderBy('order', 'asc')->first();
        return $firstInNextGroup ?: null;
    }

    /**
     * Get lessons by subject for dynamic selection
     * @throws PermissionException
     */
    public function getLessonsBySubject(Subject $subject): JsonResponse
    {
        $this->checkPermission(PermissionEnum::QUESTION_SHOW);

        $lessons = $this->subjectService->getSubjectLessons($subject->id)
            ->map(fn($lesson) => [
                'id' => $lesson->id,
                'title' => $lesson->title,
            ]);

        return response()->json([
            'success' => true,
            'data' => $lessons,
        ]);
    }

    /**
     * Get tags by subject for dynamic selection
     * @throws PermissionException
     */
    public function getTagsBySubject(Subject $subject): JsonResponse
    {
        $this->checkPermission(PermissionEnum::QUESTION_SHOW);

        $tags = $this->subjectService->getSubjectTags($subject->id)
            ->map(fn($tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ]);

        return response()->json([
            'success' => true,
            'data' => $tags,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @throws PermissionException
     */
    public function destroy(DeleteQuestionRequest $request): RedirectResponse
    {
        $this->checkPermission(PermissionEnum::QUESTION_DELETE);

        try {
            $this->questionService->deleteQuestion($request->id);
            session()->flash('delete', trans('main_trans.Question_delete_successfully'));
            return back();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }
    }

    /**
     * Move question to new position
     */
    public function moveQuestion(MoveQuestionRequest $request, QuestionGroup $questionGroup): RedirectResponse
    {
        try {
            $this->questionService->moveQuestion(
                $questionGroup->id,
                $request->question_id,
                $request->new_position
            );
            return back();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }
    }

    /**
     * Reorder questions within a question group
     * @throws PermissionException
     */
    public function reorder(ReorderQuestionRequest $request, QuestionGroup $questionGroup): JsonResponse
    {
        $this->checkPermission(PermissionEnum::QUESTION_EDIT);

        $question = new Question();
        $question->question_group_id = $questionGroup->id;
        $question->updateOrder($request->ordered_ids);

        return response()->json(['success' => true]);
    }

    /**
     * Delete question photo
     */
    public function deleteQuestionPhoto(Request $request, $id): JsonResponse
    {
        try {
            $question = Question::find($id);

            if ($question->question_photo) {
                // Delete file from storage
                $photoPath = public_path('assets/image/Question/' . $id . '/question-photo/' . $question->question_photo);
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }

                // Update database
                $question->update(['question_photo' => null]);

                return response()->json([
                    'success' => true,
                    'message' => trans('main_trans.Question photo deleted successfully')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => trans('main_trans.No photo to delete')
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('main_trans.Error deleting photo')
            ]);
        }
    }

    /**
     * Delete hint photo
     */
    public function deleteHintPhoto(Request $request, $id): JsonResponse
    {
        try {
            $question = Question::find($id);

            if ($question->hint_photo) {
                // Delete file from storage
                $photoPath = public_path('assets/image/Question/' . $id . '/hint-photo/' . $question->hint_photo);
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }

                // Update database
                $question->update(['hint_photo' => null]);

                return response()->json([
                    'success' => true,
                    'message' => trans('main_trans.Hint photo deleted successfully')
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => trans('main_trans.No photo to delete')
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('main_trans.Error deleting photo')
            ]);
        }
    }
}
