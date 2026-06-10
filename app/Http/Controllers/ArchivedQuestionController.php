<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Traits\HasPermissionChecks;
use App\Services\ArchivedQuestionService;
use App\Http\Requests\Question\RestoreQuestionRequest;
use App\Http\Requests\Question\ForceDeleteQuestionRequest;
use Illuminate\Http\Request;

class ArchivedQuestionController extends Controller
{
    use HasPermissionChecks;

    protected ArchivedQuestionService $archivedQuestionService;

    public function __construct(ArchivedQuestionService $archivedQuestionService)
    {
        $this->archivedQuestionService = $archivedQuestionService;
    }

    /**
     * Display a listing of archived questions
     */
    public function index(Request $request, $question_group_id = null)
    {
        $this->checkPermission(PermissionEnum::QUESTION_SHOW_DELETED);

        try {
            $archivedQuestions = $this->archivedQuestionService->getArchivedQuestionsByQuestionGroup($question_group_id);
            $question_group = null;
            $lesson_selected = null;

            if ($question_group_id) {
                $question_group = $this->archivedQuestionService->getQuestionGroup($question_group_id);
                $lesson_selected = $this->archivedQuestionService->getLesson($question_group->lesson_id);
            }

            return view('question.questions-deleted', compact('archivedQuestions', 'question_group', 'lesson_selected'));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show($question_group_id)
    {
        $this->checkPermission(PermissionEnum::QUESTION_SHOW_DELETED);

        try {
            $archivedQuestions = $this->archivedQuestionService->getArchivedQuestionsByQuestionGroup($question_group_id);
            $question_group = $this->archivedQuestionService->getQuestionGroup($question_group_id);
            $lesson_selected = $this->archivedQuestionService->getLesson($question_group->lesson_id);

            return view('question.questions-deleted', compact('archivedQuestions', 'question_group', 'lesson_selected'));
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort(404);
    }

    /**
     * Restore a deleted question
     */
    public function update(RestoreQuestionRequest $request)
    {
        $this->checkPermission(PermissionEnum::QUESTION_RESTORE_DELETED);

        try {
            $this->archivedQuestionService->restoreQuestion($request->id);
            session()->flash('restore', trans('main_trans.Question_restore_successfully'));
            return back();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }
    }

    /**
     * Permanently delete a question
     */
    public function destroy(ForceDeleteQuestionRequest $request)
    {
        $this->checkPermission(PermissionEnum::QUESTION_DELETE);

        try {
            $this->archivedQuestionService->forceDeleteQuestion($request->id);
            session()->flash('delete', trans('main_trans.Question_delete_successfully'));
            return back();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }
    }
}
