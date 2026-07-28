<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Http\Requests\SubjectVideo\ForceDeleteSubjectVideoRequest;
use App\Http\Requests\SubjectVideo\RestoreSubjectVideoRequest;
use App\Services\SubjectVideoService;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\Request;

class ArchivedSubjectVideoController extends Controller
{
    use HasPermissionChecks;

    protected SubjectVideoService $subjectVideoService;

    public function __construct(SubjectVideoService $subjectVideoService)
    {
        $this->subjectVideoService = $subjectVideoService;
    }

    public function show(Request $request, $type_id)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_VIDEO_SHOW_DELETED);

        $type_selected = $this->subjectVideoService->getTypeById((int) $type_id);
        $subjectVideos = $this->subjectVideoService->getArchivedSubjectVideosByType((int) $type_id);

        return view('setting.subject-video.subjects-deleted', compact(
            'subjectVideos',
            'type_selected',
        ));
    }

    public function update(RestoreSubjectVideoRequest $request)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_VIDEO_RESTORE_DELETED);

        $this->subjectVideoService->restoreSubjectVideo(
            (int) $request->id,
            $request->filled('type_id') ? (int) $request->type_id : null
        );
        session()->flash('restore', trans('main_trans.Subject_video_restore_successfully'));

        return back();
    }

    public function destroy(ForceDeleteSubjectVideoRequest $request)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_VIDEO_DELETE);

        $result = $this->subjectVideoService->forceDeleteSubjectVideo((int) $request->id);

        if ($result['success']) {
            session()->flash('delete', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }

        return back();
    }
}
