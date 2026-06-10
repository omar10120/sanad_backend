<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Models\PhoneVerificationCode;
use App\Traits\HasPermissionChecks;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class PhoneVerificationCodeController extends Controller
{
    use HasPermissionChecks;

    public function index(): Factory|Application|View|RedirectResponse
    {
        if (!config('features.phone_verification_codes')) {
            session()->flash('error', trans('main_trans.Pro_Feature_Message'));
            return redirect()->route('home');
        }
        $this->checkPermission(PermissionEnum::PHONE_VERIFICATION_CODES);

        $codes = PhoneVerificationCode::where('created_at', '>=', now()->subMinutes(30))
            ->orderByDesc('created_at')
            ->get();

        return view('phone-verification-codes.index', compact('codes'));
    }
}

