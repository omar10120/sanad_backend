<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Traits\HasPermissionChecks;
use App\Services\TypeService;
use App\Http\Requests\Type\RestoreTypeRequest;
use App\Http\Requests\Type\ForceDeleteTypeRequest;
use App\Models\Type;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ArchivedTypeController extends Controller
{
    use HasPermissionChecks;

    protected TypeService $typeService;

    public function __construct(TypeService $typeService)
    {
        $this->typeService = $typeService;
    }

    /**
     * Show archived types
     * @throws PermissionException
     */
    public function index(Request $request): Factory|Application|View
    {
        $this->checkPermission(PermissionEnum::TYPE_SHOW_DELETED);

        $archivedTypes = Type::onlyTrashed()->get();
        return view('setting.type.types-deleted', compact('archivedTypes'));
    }

    /**
     * Show archived types (for resource route)
     * @throws PermissionException
     */
    public function show(Request $request, $id): Factory|Application|View
    {
        $this->checkPermission(PermissionEnum::TYPE_SHOW_DELETED);

        $archivedTypes = Type::onlyTrashed()->get();
        return view('setting.type.types-deleted', compact('archivedTypes'));
    }

    /**
     * Restore a deleted type
     * @throws PermissionException
     */
    public function update(RestoreTypeRequest $request): RedirectResponse
    {
        $this->checkPermission(PermissionEnum::TYPE_RESTORE_DELETED);

        try {
            $type = Type::onlyTrashed()->findOrFail($request->id);
            $type->restore();
            session()->flash('restore', trans('main_trans.Type_restore_successfully'));
            return back();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }
    }

    /**
     * Permanently delete a type
     * @throws PermissionException
     */
    public function destroy(ForceDeleteTypeRequest $request): RedirectResponse
    {
        $this->checkPermission(PermissionEnum::TYPE_DELETE);

        try {
            $type = Type::onlyTrashed()->findOrFail($request->id);
            $type->forceDelete();
            session()->flash('delete', trans('main_trans.Type_delete_successfully'));
            return back();
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }
    }
}
