<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Http\Requests\Unit\DeleteUnitRequest;
use App\Http\Requests\Unit\ReorderUnitRequest;
use App\Http\Requests\Unit\StoreUnitRequest;
use App\Http\Requests\Unit\UpdateUnitRequest;
use App\Models\Teacher;
use App\Models\Unit;
use App\Services\UnitService;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    use HasPermissionChecks;

    protected UnitService $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function index(Request $request)
    {
        $this->checkPermission(PermissionEnum::UNIT_SHOW);

        return redirect()->route('course-type.index');
    }

    public function byTeacher(Request $request, $teacher_id)
    {
        $this->checkPermission(PermissionEnum::UNIT_SHOW);

        $subject_video_id = $request->query('subject_video');
        if (!$subject_video_id) {
            abort(404);
        }

        if (!$this->unitService->teacherBelongsToSubjectVideo($teacher_id, $subject_video_id)) {
            abort(404);
        }

        $teacher_selected = $this->unitService->getTeacherById($teacher_id);
        $subject_video_selected = $this->unitService->getSubjectVideoById($subject_video_id);
        $units = $this->unitService->getUnitsByTeacher($teacher_id);
        $teachers = $this->unitService->getTeachersBySubjectVideo($subject_video_id);

        return view('setting.unit.units', compact(
            'units',
            'teachers',
            'teacher_selected',
            'subject_video_selected',
        ));
    }

    public function store(StoreUnitRequest $request)
    {
        $this->checkPermission(PermissionEnum::UNIT_ADD);

        $this->unitService->createUnit($request->validated());

        session()->flash('add', trans('main_trans.Unit_add_successfully'));
        return back();
    }

    public function update(UpdateUnitRequest $request)
    {
        $this->checkPermission(PermissionEnum::UNIT_EDIT);

        $data = $request->validated();
        $this->unitService->updateUnit($data['id'], [
            'name' => $data['name'],
            'teacher_id' => $data['teacher_id'],
        ]);

        session()->flash('edit', trans('main_trans.Unit_edit_successfully'));
        return back();
    }

    public function destroy(DeleteUnitRequest $request)
    {
        $this->checkPermission(PermissionEnum::UNIT_DELETE);

        $result = $this->unitService->deleteUnit($request->id);

        if ($result['success']) {
            session()->flash('delete', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }

        return back();
    }

    public function reorder(ReorderUnitRequest $request, Teacher $teacher)
    {
        $this->checkPermission(PermissionEnum::UNIT_EDIT);

        $unit = new Unit();
        $unit->teacher_id = $teacher->id;
        $unit->updateOrder($request->ordered_ids);

        return response()->json(['success' => true]);
    }
}
