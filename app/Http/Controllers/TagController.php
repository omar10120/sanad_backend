<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Http\Requests\Tag\StoreTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Http\Requests\Tag\DeleteTagRequest;
use App\Http\Requests\Tag\ReorderTagRequest;
use App\Models\Tag;
use App\Services\TagService;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\Request;

class TagController extends Controller
{
    use HasPermissionChecks;

    protected TagService $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * Display a listing of the resource.
     * @throws PermissionException
     */
    public function index(Request $request)
    {
        $this->checkPermission(PermissionEnum::TAG_SHOW);

        // Redirect to types page since tags are organized by subject, and subjects are organized by type
        return redirect()->route('type.index');
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
    public function store(StoreTagRequest $request)
    {
        $this->checkPermission(PermissionEnum::TAG_ADD);

        $this->tagService->createTag($request->validated());

        session()->flash('add', trans('main_trans.Tag_add_successfully'));
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(tag $tag)
    {
        //
    }

    /**
     * Show tags for a specific subject
     * @throws PermissionException
     */
    public function showBySubject(Request $request, $subject_id)
    {
        $this->checkPermission(PermissionEnum::TAG_SHOW);

        $tags = $this->tagService->getTagsBySubject($subject_id);
        $subject_selected = $this->tagService->getSubjectById($subject_id);
        $subjects = $this->tagService->getAllSubjects();
        return view('setting.tag.tags', compact(
            'tags',
            'subjects',
            'subject_selected',
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(tag $tag)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @throws PermissionException
     */
    public function update(UpdateTagRequest $request)
    {
        $this->checkPermission(PermissionEnum::TAG_EDIT);

        $data = $request->validated();
        $id = $data['id'];

        $tag = $this->tagService->updateTag($id, [
            'name' => $data['name'],
            'subject_id' => $data['subject_id'],
            'is_exam' => $data['is_exam'] ?? false,
        ]);

        if (!$tag) {
            session()->flash('error', trans('main_trans.Tag_not_found'));
            return back();
        }

        session()->flash('edit', trans('main_trans.Tag_edit_successfully'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     * @throws PermissionException
     */
    public function destroy(DeleteTagRequest $request)
    {
        $this->checkPermission(PermissionEnum::TAG_DELETE);

        $data = $request->validated();
        $success = $this->tagService->deleteTag($data['id']);

        if (!$success) {
            session()->flash('error', trans('main_trans.Tag_not_found'));
            return back();
        }

        session()->flash('delete', trans('main_trans.Tag_delete_successfully'));
        return back();
    }

    /**
     * Reorder tags within a subject
     * @throws PermissionException
     */
    public function reorder(ReorderTagRequest $request, $subjectId)
    {
        $this->checkPermission(PermissionEnum::TAG_EDIT);

        $tag = new Tag();
        $tag->subject_id = $subjectId;
        $tag->updateOrder($request->ordered_ids);

        return response()->json(['success' => true]);
    }
}
