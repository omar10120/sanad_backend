<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Http\Requests\QuestionGroup\DeleteQuestionGroupRequest;
use App\Http\Requests\QuestionGroup\MoveGroupRequest;
use App\Http\Requests\QuestionGroup\ReorderQuestionGroupRequest;
use App\Http\Requests\QuestionGroup\StoreQuestionGroupRequest;
use App\Http\Requests\QuestionGroup\UpdateOrderRequest;
use App\Http\Requests\QuestionGroup\UpdateQuestionGroupRequest;
use App\Services\QuestionGroupService;
use App\Traits\HasPermissionChecks;
use App\Models\Lesson;
use App\Models\QuestionGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionGroupController extends Controller
{
    use HasPermissionChecks;

    protected QuestionGroupService $questionGroupService;

    public function __construct(QuestionGroupService $questionGroupService)
    {
        $this->questionGroupService = $questionGroupService;
    }

    /**
     * عرض المجموعات مرتبة
     */
    public function index(Lesson $lesson): JsonResponse
    {
        $this->checkPermission(PermissionEnum::QUESTION_SHOW);

        $groups = $this->questionGroupService->getQuestionGroupsByLesson($lesson);

        return response()->json([
            'success' => true,
            'data' => $groups
        ]);
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
    public function store(StoreQuestionGroupRequest $request)
    {
        // $questionGroup = $this->questionGroupService->createQuestionGroup($request->validated());

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Question group created successfully',
        //     'data' => $questionGroup
        // ]);
    }

    /**
     * Display the specified resource.
     * @throws PermissionException
     */
    public function show(Request $request, $group_id)
    {
        $this->checkPermission(PermissionEnum::QUESTION_SHOW);

        $question_group = $this->questionGroupService->getQuestionGroupForDisplay($group_id);

        if (!$question_group) {
            abort(404, 'Question group not found');
        }

        $lesson_selected = $question_group->lesson;

        return view(
            'question.questions',compact(
            'question_group',
            'lesson_selected'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QuestionGroup $questionGroup)
    {
        // return response()->json([
        //     'success' => true,
        //     'data' => $questionGroup
        // ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuestionGroupRequest $request, QuestionGroup $questionGroup)
    {
        // $updated = $this->questionGroupService->updateQuestionGroup($questionGroup->id, $request->validated());

        // if ($updated) {
        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Question group updated successfully',
        //         'data' => $questionGroup->fresh()
        //     ]);
        // }

        // return response()->json([
        //     'success' => false,
        //     'message' => 'Failed to update question group'
        // ], 500);
    }

    /**
     * Remove the specified resource from storage.
     * @throws PermissionException
     */
    public function destroy(DeleteQuestionGroupRequest $request)
    {
        $this->checkPermission(PermissionEnum::QUESTION_DELETE);

        $result = $this->questionGroupService->deleteQuestionGroup($request->id);

        if ($result['success']) {
            session()->flash('delete', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }

        return back();
    }


    /**
     * حفظ الترتيب الجديد
     */
    public function updateOrder(UpdateOrderRequest $request, Lesson $lesson)
    {
        $updated = $this->questionGroupService->updateGroupsOrder($lesson, $request->ordered_groups);

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث ترتيب المجموعات بنجاح',
                'new_order' => $lesson->questionGroups()->pluck('id')
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update groups order'
        ], 500);
    }

    /**
     * نقل مجموعة إلى موضع معين
     */
    public function moveGroup(MoveGroupRequest $request, Lesson $lesson)
    {
        $moved = $this->questionGroupService->moveGroupToPosition(
            $lesson,
            $request->group_id,
            $request->new_position
        );

        if ($moved) {
            return back();
        }

        return back()->withErrors(['error' => 'Failed to move question group']);
    }

    /**
     * Reorder question groups within a lesson
     * @throws PermissionException
     */
    public function reorder(ReorderQuestionGroupRequest $request, Lesson $lesson)
    {
        $this->checkPermission(PermissionEnum::QUESTION_EDIT);

        $questionGroup = new QuestionGroup();
        $questionGroup->lesson_id = $lesson->id;
        $questionGroup->updateOrder($request->ordered_ids);

        return response()->json(['success' => true]);
    }
}
