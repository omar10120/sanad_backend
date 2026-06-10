<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Traits\HasPermissionChecks;
use App\Models\Lesson;
use App\Models\Subject;
use App\Models\Tag;
use App\Models\Type;
use App\Services\SubjectService;
use App\Http\Requests\Subject\StoreSubjectRequest;
use App\Http\Requests\Subject\UpdateSubjectRequest;
use App\Http\Requests\Subject\DeleteSubjectRequest;
use App\Http\Requests\Subject\ReorderSubjectRequest;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    use HasPermissionChecks;

    protected SubjectService $subjectService;

    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService = $subjectService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_SHOW);

        // Redirect to types page since subjects are organized by type
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
     */
    public function store(StoreSubjectRequest $request)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_ADD);

        $data = $request->validated();
        $subject = $this->subjectService->createSubject($data);

        if ($request->hasFile('icon_photo')) {
            $newPhotoFileName = $this->subjectService->handlePhotoUpload($subject, $request->file('icon_photo'));
            if ($newPhotoFileName) {
                $this->subjectService->updateSubject($subject->id, [], $newPhotoFileName);
            }
        }

        session()->flash('add', trans('main_trans.Subject_add_successfully'));
        return back();
    }

    /**
     * Display the specified resource.
     * @throws PermissionException
     */
    public function show(Request $request, $id)
    {
        $this->checkPermission(PermissionEnum::LESSON_SHOW);

        $lessons = $this->subjectService->getSubjectLessons($id);
        $subjects = $this->subjectService->getAllSubjects();
        return view('setting.lesson.lessons', compact('lessons','subjects'));
    }

    /**
     * @throws PermissionException
     */
    public function lessons(Request $request, $subject_id)
    {
        $this->checkPermission(PermissionEnum::LESSON_SHOW);

        $lessons = $this->subjectService->getSubjectLessons($subject_id);
        $subject_selected = $this->subjectService->findSubject($subject_id);
        $subjects = $this->subjectService->getAllSubjects();
        return view('setting.lesson.lessons', compact(
            'lessons',
            'subjects',
            'subject_selected',
        ));
    }

    /**
     * @throws PermissionException
     */
    public function tags(Request $request, $subject_id)
    {
        $this->checkPermission(PermissionEnum::TAG_SHOW);

        $tags = $this->subjectService->getSubjectTags($subject_id);
        $subject_selected = $this->subjectService->findSubject($subject_id);
        $subjects = $this->subjectService->getAllSubjects();
        return view('setting.tag.tags', compact(
            'tags',
            'subjects',
            'subject_selected',
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @throws PermissionException
     */
    public function edit(Request $request, $id)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_EDIT);

        $subject = $this->subjectService->findSubject($id);
        $types = $this->subjectService->getAllTypes();
        $types_subject = $subject->types;
        return view('setting.subject.edit-subject', compact(
            'subject',
            'types',
            'types_subject'
        ));
    }

    /**
     * Update the specified resource in storage.
     * @throws PermissionException
     */
    public function update(UpdateSubjectRequest $request, $id)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_EDIT);

        $data = $request->validated();
        $subject = $this->subjectService->findSubject($data['id']);
        $newPhotoFileName = null;

        if ($request->hasFile('icon_photo')) {
            // Delete old photo if exists
            if ($subject->icon_photo) {
                $this->subjectService->deleteSubjectPhoto($subject->id, $subject->icon_photo);
            }

            $newPhotoFileName = $this->subjectService->handlePhotoUpload($subject, $request->file('icon_photo'));
            if (!$newPhotoFileName) {
                session()->flash('error', trans('main_trans.Photo_upload_failed'));
                return back();
            }
        }

        $this->subjectService->updateSubject($data['id'], $data, $newPhotoFileName);

        session()->flash('edit', trans('main_trans.Subject_edit_successfully'));
        $url = route('type.subject', ['type' => $subject->types->first()]);
        return redirect($url);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteSubjectRequest $request)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_DELETE);

        $data = $request->validated();
        $subject = $this->subjectService->findSubject($data['id']);

        if ($subject->icon_photo) {
            $this->subjectService->deleteSubjectPhoto($subject->id, $subject->icon_photo);
        }

        $result = $this->subjectService->deleteSubject($data['id']);

        if ($result['success']) {
            session()->flash('delete', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }

        return back();
    }

    public function toggle(Subject $subject)
    {
        $this->subjectService->toggleSubjectStatus($subject);
        return back();
    }

    /**
     * Reorder subjects
     * @throws PermissionException
     */
    public function reorder(ReorderSubjectRequest $request)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_EDIT);

        $subject = new Subject();
        $subject->updateOrder($request->ordered_ids);

        return response()->json(['success' => true]);
    }
}
