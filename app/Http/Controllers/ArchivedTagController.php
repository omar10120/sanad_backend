<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Traits\HasPermissionChecks;
use App\Services\TagService;
use App\Http\Requests\Tag\RestoreTagRequest;
use App\Http\Requests\Tag\ForceDeleteTagRequest;
use App\Models\Tag;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ArchivedTagController extends Controller
{
    use HasPermissionChecks;

    protected TagService $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * Show archived tags
     * @throws PermissionException
     */
    public function index(Request $request): Factory|Application|View
    {
        $this->checkPermission(PermissionEnum::TAG_SHOW_DELETED);

        $archivedTags = $this->tagService->getArchivedTags();
        
        // If there are archived tags, get the subject of the first one for back button
        $subject_selected = null;
        if ($archivedTags->count() > 0) {
            $subject_selected = $archivedTags->first()->subject;
        }
        
        return view('setting.tag.tag-deleted', compact('archivedTags', 'subject_selected'));
    }

    /**
     * Show archived tags for a specific subject
     * @throws PermissionException
     */
    public function showBySubject(Request $request, $subject_id): Factory|Application|View
    {
        $this->checkPermission(PermissionEnum::TAG_SHOW_DELETED);

        $archivedTags = $this->tagService->getArchivedTagsBySubject($subject_id);
        $subject_selected = $this->tagService->getSubjectById($subject_id);
        
        return view('setting.tag.tag-deleted', compact('archivedTags', 'subject_selected'));
    }

    /**
     * Show archived tags (for resource route)
     * @throws PermissionException
     */
    public function show(Request $request, $id): Factory|Application|View
    {
        $this->checkPermission(PermissionEnum::TAG_SHOW_DELETED);

        $archivedTags = $this->tagService->getArchivedTags();
        return view('setting.tag.tag-deleted', compact('archivedTags'));
    }

    /**
     * Restore a deleted tag
     * @throws PermissionException
     */
    public function update(RestoreTagRequest $request): RedirectResponse
    {
        $this->checkPermission(PermissionEnum::TAG_RESTORE_DELETED);

        try {
            $success = $this->tagService->restoreTag($request->id);
            
            if (!$success) {
                session()->flash('error', trans('main_trans.Tag_not_found'));
                return back();
            }

            session()->flash('restore', trans('main_trans.Tag_restore_successfully'));
            return back();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }
    }

    /**
     * Permanently delete a tag
     * @throws PermissionException
     */
    public function destroy(ForceDeleteTagRequest $request): RedirectResponse
    {
        $this->checkPermission(PermissionEnum::TAG_DELETE);

        try {
            $success = $this->tagService->forceDeleteTag($request->id);
            
            if (!$success) {
                session()->flash('error', trans('main_trans.Tag_not_found'));
                return back();
            }

            session()->flash('delete', trans('main_trans.Tag_delete_successfully'));
            return back();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }
    }
} 