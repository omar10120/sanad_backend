<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Traits\HasPermissionChecks;
use App\Services\ArchivedQuestionGroupService;
use App\Http\Requests\QuestionGroup\RestoreQuestionGroupRequest;
use App\Http\Requests\QuestionGroup\ForceDeleteQuestionGroupRequest;
use Illuminate\Http\Request;

class ArchivedQuestionGroupController extends Controller
{
    use HasPermissionChecks;

    protected ArchivedQuestionGroupService $archivedQuestionGroupService;

    public function __construct(ArchivedQuestionGroupService $archivedQuestionGroupService)
    {
        $this->archivedQuestionGroupService = $archivedQuestionGroupService;
    }

    /**
     * Display a listing of archived question groups
     */
    public function index(Request $request, $lesson_id = null)
    {
        $this->checkPermission(PermissionEnum::QUESTION_SHOW_DELETED);

        $archivedQuestionGroups = $this->archivedQuestionGroupService->getArchivedQuestionGroups($lesson_id);
        $lesson_selected = null;

        if ($lesson_id) {
            $lesson_selected = $this->archivedQuestionGroupService->getLesson($lesson_id);
        }

        return view('question.question-group-deleted', compact('archivedQuestionGroups', 'lesson_selected'));
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
    public function show($lesson_id)
    {
        $this->checkPermission(PermissionEnum::QUESTION_SHOW_DELETED);

        $archivedQuestionGroups = $this->archivedQuestionGroupService->getArchivedQuestionGroups($lesson_id);
        $lesson_selected = null;

        if ($lesson_id) {
            $lesson_selected = $this->archivedQuestionGroupService->getLesson($lesson_id);
        }

        return view('question.question-group-deleted', compact('archivedQuestionGroups', 'lesson_selected'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        abort(404);
    }

    /**
     * Restore a deleted question group
     */
    public function update(RestoreQuestionGroupRequest $request)
    {
        $this->checkPermission(PermissionEnum::QUESTION_RESTORE_DELETED);

        try {
            $this->archivedQuestionGroupService->restoreQuestionGroup($request->id);
            session()->flash('restore', trans('main_trans.Question_group_restore_successfully'));
            return back();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }
    }

    /**
     * Permanently delete a question group
     */
    public function destroy(ForceDeleteQuestionGroupRequest $request)
    {
        $this->checkPermission(PermissionEnum::QUESTION_DELETE);

        try {
            $this->archivedQuestionGroupService->forceDeleteQuestionGroup($request->id);
            session()->flash('delete', trans('main_trans.Question_group_delete_successfully'));
            return back();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }
    }
} 