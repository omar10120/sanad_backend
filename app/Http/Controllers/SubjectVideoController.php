<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Http\Requests\SubjectVideo\DeleteSubjectVideoRequest;
use App\Http\Requests\SubjectVideo\ReorderSubjectVideoRequest;
use App\Http\Requests\SubjectVideo\StoreSubjectVideoRequest;
use App\Http\Requests\SubjectVideo\UpdateSubjectVideoRequest;
use App\Models\SubjectVideo;
use App\Services\SubjectVideoService;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\Request;

class SubjectVideoController extends Controller
{
    use HasPermissionChecks;

    protected SubjectVideoService $subjectVideoService;

    public function __construct(SubjectVideoService $subjectVideoService)
    {
        $this->subjectVideoService = $subjectVideoService;
    }

    public function index(Request $request)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_VIDEO_SHOW);

        return redirect()->route('course-type.index');
    }

    public function byType(Request $request, $type_id)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_VIDEO_SHOW);

        $types = $this->subjectVideoService->getAllTypes();
        $type_selected = $this->subjectVideoService->getTypeWithSubjectVideos($type_id);
        $subjectVideos = $type_selected->subjectVideos;
        $archivedSubjectVideosCount = $this->subjectVideoService
            ->getArchivedSubjectVideosByType((int) $type_id)
            ->count();

        return view('setting.subject-video.subjects', compact(
            'subjectVideos',
            'types',
            'type_selected',
            'archivedSubjectVideosCount',
        ));
    }

    public function store(StoreSubjectVideoRequest $request)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_VIDEO_ADD);

        $data = $request->validated();
        $subjectVideo = $this->subjectVideoService->createSubjectVideo($data);

        if ($request->hasFile('icon_photo')) {
            $newPhotoFileName = $this->subjectVideoService->handlePhotoUpload($subjectVideo, $request->file('icon_photo'));
            if ($newPhotoFileName) {
                $this->subjectVideoService->updateSubjectVideo($subjectVideo->id, [], $newPhotoFileName);
            }
        }

        session()->flash('add', trans('main_trans.Subject_video_add_successfully'));
        return back();
    }

    public function update(UpdateSubjectVideoRequest $request)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_VIDEO_EDIT);

        $data = $request->validated();
        $id = $data['id'];
        $newPhotoFileName = null;

        if ($request->hasFile('icon_photo')) {
            $subjectVideo = $this->subjectVideoService->findSubjectVideo($id);
            if ($subjectVideo->icon_photo) {
                $this->subjectVideoService->deleteSubjectVideoPhoto($subjectVideo->id, $subjectVideo->icon_photo);
            }
            $newPhotoFileName = $this->subjectVideoService->handlePhotoUpload($subjectVideo, $request->file('icon_photo'));
        }

        $this->subjectVideoService->updateSubjectVideo($id, $data, $newPhotoFileName);

        session()->flash('edit', trans('main_trans.Subject_video_edit_successfully'));
        return back();
    }

    public function destroy(DeleteSubjectVideoRequest $request)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_VIDEO_DELETE);

        $result = $this->subjectVideoService->deleteSubjectVideo($request->id);

        if ($result['success']) {
            session()->flash('delete', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }

        return back();
    }

    public function toggle(SubjectVideo $subjectVideo)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_VIDEO_EDIT);

        $this->subjectVideoService->toggleSubjectVideoStatus($subjectVideo);
        return back();
    }

    public function reorder(ReorderSubjectVideoRequest $request)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_VIDEO_EDIT);

        $subjectVideo = new SubjectVideo();
        $subjectVideo->updateOrder($request->ordered_ids);

        return response()->json(['success' => true]);
    }
}
