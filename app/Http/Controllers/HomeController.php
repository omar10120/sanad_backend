<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\Request;
//use Spatie\Analytics\Facades\Analytics;
//use Spatie\Analytics\Period;

class HomeController extends Controller
{
    use HasPermissionChecks;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // Redirect to the new dashboard controller
        return app(DashboardController::class)->dashboard();
    }

    public function test($index)
    {
        return view($index);
    }
}
