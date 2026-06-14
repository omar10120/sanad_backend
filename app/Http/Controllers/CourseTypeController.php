<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Services\TypeService;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\Request;

class CourseTypeController extends Controller
{
    use HasPermissionChecks;

    protected TypeService $typeService;

    public function __construct(TypeService $typeService)
    {
        $this->typeService = $typeService;
    }

    public function index(Request $request)
    {
        $this->checkPermission(PermissionEnum::SUBJECT_VIDEO_SHOW);

        $types = $this->typeService->getAllTypes();

        return view('setting.course.types', compact('types'));
    }
}
