<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Traits\HasPermissionChecks;
use App\Models\Type;
use App\Services\TypeService;
use App\Http\Requests\Type\StoreTypeRequest;
use App\Http\Requests\Type\UpdateTypeRequest;
use App\Http\Requests\Type\DeleteTypeRequest;
use App\Http\Requests\Type\ReorderTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TypeController extends Controller
{
    use HasPermissionChecks;

    protected TypeService $typeService;

    public function __construct(TypeService $typeService)
    {
        $this->typeService = $typeService;
    }

    /**
     * Display a listing of the resource.
     * @throws PermissionException
     */
    public function index(Request $request)
    {
        $this->checkPermission(PermissionEnum::TYPE_SHOW);

        $types = $this->typeService->getTypesForUser();
        return view('setting.type.types', compact('types'));
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
    public function store(StoreTypeRequest $request)
    {
        $this->checkPermission(PermissionEnum::TYPE_ADD);

        $this->typeService->createType($request->validated());

        session()->flash('add', trans('main_trans.Type_add_successfully'));

        if ($request->input('return_to') === 'course-type') {
            return redirect()->route('course-type.index');
        }

        return redirect('type');
    }

    /**
     * Display the specified resource.
     */
    public function show(type $type)
    {
        //
    }

    /**
     * @throws PermissionException
     */
    public function subjects(Request $request, $type_id)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_SHOW);

        $types = $this->typeService->getTypesForUser();
        $type_selected = $this->typeService->getTypeWithSubjectsForUser($type_id);

        if (!$type_selected) {
            abort(404);
        }

        // Filter subjects based on user access
        $user = Auth::user();
        if ($user->hasRole('Owner')) {
            $subjects = $type_selected->subjects;
        } else {
            $userSubjectIds = $user->getAllowedSubjectIds();
            $subjects = $type_selected->subjects->whereIn('id', $userSubjectIds);
        }

        return view('setting.subject.subjects', compact(
            'subjects',
            'types',
            'type_selected',
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(type $type)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @throws PermissionException
     */
    public function update(UpdateTypeRequest $request)
    {
        $this->checkPermission(PermissionEnum::TYPE_EDIT);

        $validatedData = $request->validated();
        $id = $validatedData['id'];

        $this->typeService->updateType($id, [
            'name' => $validatedData['name'],
        ]);

        session()->flash('edit', trans('main_trans.Type_edit_successfully'));
        return redirect('type');
    }

    /**
     * Remove the specified resource from storage.
     * @throws PermissionException
     */
    public function destroy(DeleteTypeRequest $request)
    {
        $this->checkPermission(PermissionEnum::TYPE_DELETE);

        $result = $this->typeService->deleteType($request->id);

        if ($result['success']) {
            session()->flash('delete', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }

        return back();
    }

    /**
     * @throws PermissionException
     */
    public function toggle(Type $type)
    {
        $this->checkPermission(PermissionEnum::TYPE_EDIT);

        $this->typeService->toggleTypeStatus($type);
        return back();
    }

    /**
     * Reorder types
     * @throws PermissionException
     */
    public function reorder(ReorderTypeRequest $request)
    {
        $this->checkPermission(PermissionEnum::TYPE_EDIT);

        $type = new Type();
        $type->updateOrder($request->ordered_ids);

        return response()->json(['success' => true]);
    }
}
