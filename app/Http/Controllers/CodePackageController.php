<?php
namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Exceptions\PermissionException;
use App\Http\Requests\CodePackage\DeleteCodePackageRequest;
use App\Http\Requests\CodePackage\DeleteCodeRequest;
use App\Http\Requests\CodePackage\StoreCodePackageRequest;
use App\Services\CodeService;
use App\Traits\HasPermissionChecks;
use App\Exports\CodesExport;
use App\Exports\CodesPdfExport;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CodePackageController extends Controller
{
    use HasPermissionChecks;

    protected CodeService $codeService;

    public function __construct(CodeService $codeService)
    {
        $this->codeService = $codeService;
    }

    // عرض كل الحزم
    public function index(): Factory|Application|View
    {
        $packages = $this->codeService->getAllPackages();
        $subjects = $this->codeService->getAllSubjects();

        return view('packages.index', compact('packages', 'subjects'));
    }

    // صفحة إنشاء حزمة جديدة
    public function create(): Factory|Application|View
    {
        $subjects = $this->codeService->getAllSubjects();
        return view('packages.create', compact('subjects'));
    }

    // حفظ حزمة جديدة مع توليد الأكواد
    public function store(StoreCodePackageRequest $request): RedirectResponse
    {
        try {
            $this->checkPermission(PermissionEnum::CODE_ADD);

            $packageData = [
                'name' => $request->name,
                'expires_at' => $request->expires_at,
            ];

            $this->codeService->createPackage(
                $packageData,
                $request->subject_ids,
                $request->codes_count
            );

            session()->flash('add', trans('main_trans.Code_package_add_successfully'));
            return back();
        } catch (\Exception $e) {
            session()->flash('error', trans('main_trans.Error_creating_package') . ': ' . $e->getMessage());
            return back();
        }
    }

    // عرض حزمة

    /**
     * @throws PermissionException
     */
    public function show(Request $request, $id): Factory|Application|View
    {
        $this->checkPermission(PermissionEnum::CODE_SHOW);

        $package = $this->codeService->findPackage($id);

        if (!$package) {
            return view('404');
        }

        $subjects = $this->codeService->getAllSubjects();

        return view('packages.show', compact('package', 'subjects'));
    }

    // حذف حزمة
    public function destroy(DeleteCodePackageRequest $request): RedirectResponse
    {
        try {
            $this->checkPermission(PermissionEnum::CODE_DELETE);

            $deleted = $this->codeService->deletePackage($request->id);

            if ($deleted) {
                session()->flash('delete', trans('main_trans.Code_package_delete_successfully'));
            } else {
                session()->flash('error', trans('main_trans.Error_deleting_package'));
            }

            return back();
        } catch (\Exception $e) {
            session()->flash('error', trans('main_trans.Error_deleting_package') . ': ' . $e->getMessage());
            return back();
        }
    }

    // حذف كود
    public function destroyCode(DeleteCodeRequest $request): RedirectResponse
    {
        try {
            $this->checkPermission(PermissionEnum::CODE_DELETE);

            $deleted = $this->codeService->deleteCode($request->id);

            if ($deleted) {
                session()->flash('delete', trans('main_trans.Code_delete_successfully'));
            } else {
                session()->flash('error', trans('main_trans.Error_deleting_code'));
            }

            return back();
        } catch (\Exception $e) {
            session()->flash('error', trans('main_trans.Error_deleting_code') . ': ' . $e->getMessage());
            return back();
        }
    }

    // تصدير الأكواد الخاصة بحزمة معينة
    public function exportPackage($packageId): StreamedResponse|RedirectResponse
    {
        if (!config('features.code_export_excel')) {
            session()->flash('error', trans('main_trans.Pro_Feature_Message'));
            return back();
        }
        return (new CodesExport)->exportByPackage($packageId);
    }

    // تحديث حزمة الأكواد
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $this->checkPermission(PermissionEnum::CODE_EDIT);

            $request->validate([
                'name' => 'required|string|max:255',
                'expires_at' => 'required|date',
                'subject_ids' => 'required|array|min:1',
                'subject_ids.*' => 'exists:subjects,id'
            ]);

            $packageData = [
                'name' => $request->name,
                'expires_at' => $request->expires_at,
            ];

            $this->codeService->updatePackage($id, $packageData, $request->subject_ids);

            session()->flash('edit', trans('main_trans.Code_package_edit_successfully'));
            return back();
        } catch (\Exception $e) {
            session()->flash('error', trans('main_trans.Error_updating_package') . ': ' . $e->getMessage());
            return back();
        }
    }

    // تصدير الأكواد كملف PDF
    public function exportPackagePdf($packageId)
    {
        try {
            if (!config('features.code_export_pdf')) {
                session()->flash('error', trans('main_trans.Pro_Feature_Message'));
                return back();
            }
            $this->checkPermission(PermissionEnum::CODE_SHOW);
            return (new CodesPdfExport)->exportByPackage($packageId);
        } catch (\Exception $e) {
            session()->flash('error', trans('main_trans.Error_generating_PDF') . ': ' . $e->getMessage());
            return back();
        }
    }
}
