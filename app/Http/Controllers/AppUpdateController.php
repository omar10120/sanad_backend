<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Traits\HasPermissionChecks;
use App\Models\AppUpdate;
use App\Services\AppUpdateService;
use App\Http\Requests\AppUpdate\StoreAppUpdateRequest;
use App\Http\Requests\AppUpdate\UpdateAppUpdateRequest;
use App\Http\Requests\AppUpdate\DeleteAppUpdateRequest;
use Illuminate\Http\Request;

class AppUpdateController extends Controller
{
    use HasPermissionChecks;

    protected AppUpdateService $appUpdateService;

    public function __construct(AppUpdateService $appUpdateService)
    {
        $this->appUpdateService = $appUpdateService;
    }

    /**
     * Display a listing of the resource.
     * @throws PermissionException
     */
    public function index(Request $request)
    {
        if (!config('features.app_updates')) {
            session()->flash('error', trans('main_trans.Pro_Feature_Message'));
            return redirect()->route('home');
        }
        $this->checkPermission(PermissionEnum::ROLE_SHOW);

        $app_updates = $this->appUpdateService->getAllAppUpdates();
        return view('setting.app-update.app-updates', compact('app_updates'));
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
    public function store(StoreAppUpdateRequest $request)
    {
        $this->checkPermission(PermissionEnum::ROLE_ADD);

        try {
            $this->appUpdateService->createAppUpdate($request->validated());
            session()->flash('add', trans('main_trans.App_update_add_successfully'));
            return redirect()->route('app-update.index');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => trans('main_trans.Error_creating_app_update')])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AppUpdate $app_update)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AppUpdate $app_update)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * @throws PermissionException
     */
    public function update(UpdateAppUpdateRequest $request, AppUpdate $app_update)
    {
        $this->checkPermission(PermissionEnum::ROLE_EDIT);

        try {
            $this->appUpdateService->updateAppUpdate($app_update, $request->validated());
            session()->flash('edit', trans('main_trans.App_update_updated_successfully'));
            return redirect()->route('app-update.index');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => trans('main_trans.Error_updating_app_update')])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     * @throws PermissionException
     */
    public function destroy(AppUpdate $app_update)
    {
        $this->checkPermission(PermissionEnum::ROLE_DELETE);

        try {
            $deleted = $this->appUpdateService->deleteAppUpdate($app_update->id);
            if ($deleted) {
                session()->flash('delete', trans('main_trans.App_update_delete_successfully'));
            } else {
                session()->flash('error', trans('main_trans.App_update_not_found'));
            }
            return redirect()->route('app-update.index');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => trans('main_trans.Error_deleting_app_update')])->withInput();
        }
    }
}
