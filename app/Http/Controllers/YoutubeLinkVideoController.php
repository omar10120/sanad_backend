<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Http\Requests\YoutubeLinkVideo\DeleteYoutubeLinkVideoRequest;
use App\Http\Requests\YoutubeLinkVideo\ReorderYoutubeLinkVideoRequest;
use App\Http\Requests\YoutubeLinkVideo\StoreYoutubeLinkVideoRequest;
use App\Http\Requests\YoutubeLinkVideo\UpdateYoutubeLinkVideoRequest;
use App\Models\LessonVideo;
use App\Models\YoutubeLinkVideo;
use App\Services\YoutubeLinkVideoService;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\Request;

class YoutubeLinkVideoController extends Controller
{
    use HasPermissionChecks;

    protected YoutubeLinkVideoService $youtubeLinkVideoService;

    public function __construct(YoutubeLinkVideoService $youtubeLinkVideoService)
    {
        $this->youtubeLinkVideoService = $youtubeLinkVideoService;
    }

    public function index(Request $request)
    {
        $this->checkPermission(PermissionEnum::YOUTUBE_LINK_VIDEO_SHOW);

        return redirect()->route('course-type.index');
    }

    public function byLessonVideo(Request $request, $lesson_video_id)
    {
        $this->checkPermission(PermissionEnum::YOUTUBE_LINK_VIDEO_SHOW);

        $subject_video_id = $request->query('subject_video');
        $teacher_id = $request->query('teacher');
        $unit_id = $request->query('unit');

        if (!$subject_video_id || !$teacher_id || !$unit_id) {
            abort(404);
        }

        if (!$this->youtubeLinkVideoService->lessonVideoBelongsToContext(
            $lesson_video_id,
            $unit_id,
            $teacher_id,
            $subject_video_id
        )) {
            abort(404);
        }

        $lesson_video_selected = $this->youtubeLinkVideoService->getLessonVideoById($lesson_video_id);
        $unit_selected = $this->youtubeLinkVideoService->getUnitById($unit_id);
        $teacher_selected = $this->youtubeLinkVideoService->getTeacherById($teacher_id);
        $subject_video_selected = $this->youtubeLinkVideoService->getSubjectVideoById($subject_video_id);
        $youtubeLinks = $this->youtubeLinkVideoService->getYoutubeLinksByLessonVideo($lesson_video_id);
        $archivedYoutubeLinksCount = $this->youtubeLinkVideoService
            ->getArchivedYoutubeLinksByLessonVideo((int) $lesson_video_id)
            ->count();
        $lessonVideos = $this->youtubeLinkVideoService->getLessonVideosByUnit($unit_id);

        return view('setting.youtube-link-video.youtube-links-video', compact(
            'youtubeLinks',
            'lessonVideos',
            'lesson_video_selected',
            'unit_selected',
            'teacher_selected',
            'subject_video_selected',
            'archivedYoutubeLinksCount',
        ));
    }

    public function store(StoreYoutubeLinkVideoRequest $request)
    {
        $this->checkPermission(PermissionEnum::YOUTUBE_LINK_VIDEO_ADD);

        $this->youtubeLinkVideoService->createYoutubeLinkVideo($request->validated());

        session()->flash('add', trans('main_trans.Youtube_link_video_add_successfully'));
        return back();
    }

    public function update(UpdateYoutubeLinkVideoRequest $request)
    {
        $this->checkPermission(PermissionEnum::YOUTUBE_LINK_VIDEO_EDIT);

        $data = $request->validated();
        $this->youtubeLinkVideoService->updateYoutubeLinkVideo($data['id'], [
            'name' => $data['name'],
            'youtube_link' => $data['youtube_link'],
            'video_time' => $data['video_time'] ?? null,
            'lesson_video_id' => $data['lesson_video_id'],
            'is_active' => $data['is_active'] ?? true,
        ]);

        session()->flash('edit', trans('main_trans.Youtube_link_video_edit_successfully'));
        return back();
    }

    public function destroy(DeleteYoutubeLinkVideoRequest $request)
    {
        $this->checkPermission(PermissionEnum::YOUTUBE_LINK_VIDEO_DELETE);

        $this->youtubeLinkVideoService->deleteYoutubeLinkVideo($request->id);
        session()->flash('delete', trans('main_trans.Youtube_link_video_delete_successfully'));

        return back();
    }

    public function toggle($youtube_link_video_id)
    {
        $this->checkPermission(PermissionEnum::YOUTUBE_LINK_VIDEO_EDIT);

        $this->youtubeLinkVideoService->toggleYoutubeLinkVideo((int) $youtube_link_video_id);

        return back();
    }

    public function reorder(ReorderYoutubeLinkVideoRequest $request, LessonVideo $lessonVideo)
    {
        $this->checkPermission(PermissionEnum::YOUTUBE_LINK_VIDEO_EDIT);

        $youtubeLinkVideo = new YoutubeLinkVideo();
        $youtubeLinkVideo->lesson_video_id = $lessonVideo->id;
        $youtubeLinkVideo->updateOrder($request->ordered_ids);

        return response()->json(['success' => true]);
    }
}
