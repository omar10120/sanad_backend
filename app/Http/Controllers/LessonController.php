<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Traits\HasPermissionChecks;
use App\Models\Lesson;
use App\Services\LessonService;
use App\Http\Requests\Lesson\StoreLessonRequest;
use App\Http\Requests\Lesson\UpdateLessonRequest;
use App\Http\Requests\Lesson\DeleteLessonRequest;
use App\Http\Requests\Lesson\ReorderLessonRequest;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    use HasPermissionChecks;

    protected $lessonService;

    public function __construct(LessonService $lessonService)
    {
        $this->lessonService = $lessonService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission(PermissionEnum::LESSON_SHOW);

        // Redirect to types page since lessons are organized by subject, and subjects are organized by type
        return redirect()->route('type.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLessonRequest $request)
    {
        $this->checkPermission(PermissionEnum::LESSON_ADD);

        $this->lessonService->createLesson($request->validated());

        session()->flash('add', trans('main_trans.Lesson_add_successfully') );
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson)
    {
        //
    }

    public function questionGroups(Request $request, $lesson_id)
    {
        $this->checkPermission(PermissionEnum::QUESTION_SHOW);

        $lesson_selected = $this->lessonService->getLessonWithQuestionGroups($lesson_id);
        $question_groups = $lesson_selected->questionGroups;

        return view(
            'question.question_groups',compact(
            'question_groups',
                'lesson_selected'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLessonRequest $request)
    {
        $this->checkPermission(PermissionEnum::LESSON_EDIT);

        $this->lessonService->updateLesson($request->id, [
            'title' => $request->title,
            'subject_id' => $request->subject_id,
        ]);

        session()->flash('edit', trans('main_trans.Lesson_edit_successfully') );
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteLessonRequest $request)
    {
        $this->checkPermission(PermissionEnum::LESSON_DELETE);

        $result = $this->lessonService->deleteLesson($request->id);

        if ($result['success']) {
            session()->flash('delete', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }

        return back();
    }

    public function toggle(Lesson $lesson)
    {
        $this->lessonService->toggleLesson($lesson);
        return back();
    }

    /**
     * Reorder lessons within a subject
     * @throws PermissionException
     */
    public function reorder(ReorderLessonRequest $request, $subjectId)
    {
        $this->checkPermission(PermissionEnum::LESSON_EDIT);

        $lesson = new Lesson();
        $lesson->subject_id = $subjectId;
        $lesson->updateOrder($request->ordered_ids);

        return response()->json(['success' => true]);
    }
}
