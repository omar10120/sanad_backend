<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Traits\HasPermissionChecks;
use App\Services\SubjectService;
use App\Http\Requests\Subject\RestoreSubjectRequest;
use App\Http\Requests\Subject\ForceDeleteSubjectRequest;
use App\Models\Subject;
use App\Models\Type;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ArchivedSubjectController extends Controller
{
    use HasPermissionChecks;

    protected $subjectService;

    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService = $subjectService;
    }

    /**
     * Show archived subjects
     * @throws PermissionException
     */
    public function index(Request $request, $type_id = null): Factory|Application|View
    {
        $this->checkPermission(PermissionEnum::SUBJECT_SHOW_DELETED);

        if ($type_id) {
            // Get archived subjects for a specific type
            $type = Type::findOrFail($type_id);
            $archivedSubjects = $type->subjects()->onlyTrashed()->get();
            $type_selected = $type;
        } else {
            // Get all archived subjects
            $archivedSubjects = Subject::onlyTrashed()->get();
            $type_selected = null;
        }

        return view('setting.subject.subjects-deleted', compact('archivedSubjects', 'type_selected'));
    }

    /**
     * Show archived subjects (for resource route)
     * @throws PermissionException
     */
    public function show(Request $request, $id): Factory|Application|View
    {
        $this->checkPermission(PermissionEnum::SUBJECT_SHOW_DELETED);

        // Get all archived subjects for resource route
        $archivedSubjects = Subject::onlyTrashed()->get();
        $type_selected = null;

        return view('setting.subject.subjects-deleted', compact('archivedSubjects', 'type_selected'));
    }

    /**
     * Restore a deleted subject
     * @throws PermissionException
     */
    public function update(RestoreSubjectRequest $request): RedirectResponse
    {
        $this->checkPermission(PermissionEnum::SUBJECT_RESTORE_DELETED);

        try {
            $subject = Subject::onlyTrashed()->findOrFail($request->id);
            $subject->restore();
            session()->flash('restore', trans('main_trans.Subject_restore_successfully'));
            return back();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }
    }

    /**
     * Permanently delete a subject
     * @throws PermissionException
     */
    public function destroy(ForceDeleteSubjectRequest $request): RedirectResponse
    {
        $this->checkPermission(PermissionEnum::SUBJECT_DELETE);

        try {
            $subject = Subject::onlyTrashed()->findOrFail($request->id);
            $subject->forceDelete();
            session()->flash('delete', trans('main_trans.Subject_delete_successfully'));
            return back();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }
    }
}
