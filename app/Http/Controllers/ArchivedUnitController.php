<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Http\Requests\Unit\ForceDeleteUnitRequest;
use App\Http\Requests\Unit\RestoreUnitRequest;
use App\Services\UnitService;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\Request;

class ArchivedUnitController extends Controller
{
    use HasPermissionChecks;

    protected UnitService $unitService;

    public function __construct(UnitService $unitService)
    {
        $this->unitService = $unitService;
    }

    public function show(Request $request, $teacher_id)
    {
        $this->checkPermission(PermissionEnum::UNIT_SHOW_DELETED);

        $subject_video_id = $request->query('subject_video');
        if (! $subject_video_id) {
            abort(404);
        }

        if (! $this->unitService->teacherBelongsToSubjectVideo($teacher_id, $subject_video_id)) {
            abort(404);
        }

        $teacher_selected = $this->unitService->getTeacherById($teacher_id);
        $subject_video_selected = $this->unitService->getSubjectVideoById($subject_video_id);
        $units = $this->unitService->getArchivedUnitsByTeacher($teacher_id);

        return view('setting.unit.units-deleted', compact(
            'units',
            'teacher_selected',
            'subject_video_selected',
        ));
    }

    public function update(RestoreUnitRequest $request)
    {
        $this->checkPermission(PermissionEnum::UNIT_RESTORE_DELETED);

        $this->unitService->restoreUnit((int) $request->id);
        session()->flash('restore', trans('main_trans.Unit_restore_successfully'));

        return back();
    }

    public function destroy(ForceDeleteUnitRequest $request)
    {
        $this->checkPermission(PermissionEnum::UNIT_DELETE);

        $this->unitService->forceDeleteUnit((int) $request->id);
        session()->flash('delete', trans('main_trans.Unit_delete_successfully'));

        return back();
    }
}
