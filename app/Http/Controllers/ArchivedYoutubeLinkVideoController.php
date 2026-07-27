<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Http\Requests\YoutubeLinkVideo\ForceDeleteYoutubeLinkVideoRequest;
use App\Http\Requests\YoutubeLinkVideo\RestoreYoutubeLinkVideoRequest;
use App\Services\YoutubeLinkVideoService;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\Request;

class ArchivedYoutubeLinkVideoController extends Controller
{
    use HasPermissionChecks;

    protected YoutubeLinkVideoService $youtubeLinkVideoService;

    public function __construct(YoutubeLinkVideoService $youtubeLinkVideoService)
    {
        $this->youtubeLinkVideoService = $youtubeLinkVideoService;
    }

    public function show(Request $request, $lesson_video_id)
    {
        $this->checkPermission(PermissionEnum::YOUTUBE_LINK_VIDEO_EDIT);

        $subject_video_id = $request->query('subject_video');
        $teacher_id = $request->query('teacher');
        $unit_id = $request->query('unit');

        if (! $subject_video_id || ! $teacher_id || ! $unit_id) {
            abort(404);
        }

        if (! $this->youtubeLinkVideoService->lessonVideoBelongsToContext(
            (int) $lesson_video_id,
            (int) $unit_id,
            (int) $teacher_id,
            (int) $subject_video_id
        )) {
            abort(404);
        }

        $lesson_video_selected = $this->youtubeLinkVideoService->getLessonVideoById((int) $lesson_video_id);
        $unit_selected = $this->youtubeLinkVideoService->getUnitById((int) $unit_id);
        $teacher_selected = $this->youtubeLinkVideoService->getTeacherById((int) $teacher_id);
        $subject_video_selected = $this->youtubeLinkVideoService->getSubjectVideoById((int) $subject_video_id);
        $youtubeLinks = $this->youtubeLinkVideoService->getArchivedYoutubeLinksByLessonVideo((int) $lesson_video_id);

        return view('setting.youtube-link-video.youtube-links-video-deleted', compact(
            'youtubeLinks',
            'lesson_video_selected',
            'unit_selected',
            'teacher_selected',
            'subject_video_selected',
        ));
    }

    public function update(RestoreYoutubeLinkVideoRequest $request)
    {
        $this->checkPermission(PermissionEnum::YOUTUBE_LINK_VIDEO_EDIT);

        $this->youtubeLinkVideoService->restoreYoutubeLinkVideo((int) $request->id);
        session()->flash('restore', trans('main_trans.Youtube_link_video_restore_successfully'));

        return back();
    }

    public function destroy(ForceDeleteYoutubeLinkVideoRequest $request)
    {
        $this->checkPermission(PermissionEnum::YOUTUBE_LINK_VIDEO_DELETE);

        $this->youtubeLinkVideoService->forceDeleteYoutubeLinkVideo((int) $request->id);
        session()->flash('delete', trans('main_trans.Youtube_link_video_delete_successfully'));

        return back();
    }
}
