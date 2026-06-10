<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Traits\HasPermissionChecks;
use App\Models\QuestionType;
use App\Services\QuestionTypeService;
use App\Http\Requests\QuestionType\StoreQuestionTypeRequest;
use App\Http\Requests\QuestionType\UpdateQuestionTypeRequest;
use App\Http\Requests\QuestionType\DeleteQuestionTypeRequest;
use Illuminate\Http\Request;

class QuestionTypeController extends Controller
{
    use HasPermissionChecks;

    protected QuestionTypeService $questionTypeService;

    public function __construct(QuestionTypeService $questionTypeService)
    {
        $this->questionTypeService = $questionTypeService;
    }

    /**
     * Display a listing of the resource.
     * @throws PermissionException
     */
    public function index(Request $request)
    {
        $this->checkPermission(PermissionEnum::QUESTION_SHOW);

        $types = $this->questionTypeService->getAllQuestionTypes();
        return view('question.question_types', compact('types'));
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
     * @throws PermissionException
     */
    public function store(StoreQuestionTypeRequest $request)
    {
        $this->checkPermission(PermissionEnum::QUESTION_ADD);

        $this->questionTypeService->createQuestionType($request->validated());

        session()->flash('add', trans('main_trans.Question_type_add_successfully'));
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(QuestionType $type)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QuestionType $type)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @throws PermissionException
     */
    public function update(UpdateQuestionTypeRequest $request)
    {
        $this->checkPermission(PermissionEnum::QUESTION_EDIT);

        $validatedData = $request->validated();
        $this->questionTypeService->updateQuestionType($validatedData['id'], [
            'name' => $validatedData['name'],
            'type' => $validatedData['type'],
        ]);

        session()->flash('edit', trans('main_trans.Question_type_edit_successfully'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     * @throws PermissionException
     */
    public function destroy(DeleteQuestionTypeRequest $request)
    {
        $this->checkPermission(PermissionEnum::QUESTION_DELETE);

        $result = $this->questionTypeService->deleteQuestionType($request->id);

        if ($result['success']) {
            session()->flash('delete', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }

        return back();
    }
}
