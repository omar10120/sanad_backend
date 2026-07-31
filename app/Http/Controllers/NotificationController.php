<?php

namespace App\Http\Controllers;

use App\Enums\PermissionEnum;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Requests\UpdateNotificationRequest;
use App\Models\Notification;
use App\Models\Student;
use App\Models\Type;
use App\Services\NotificationService;
use App\Traits\HasPermissionChecks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    use HasPermissionChecks;

    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
//        if (!config('features.advanced_notifications')) {
//            session()->flash('error', trans('main_trans.Pro_Feature_Message'));
//            return redirect()->route('home');
//        }
        $this->checkPermission(PermissionEnum::NOTIFICATION_SHOW);
        $notifications = $this->notificationService->getAllNotifications();
        return view('notifications.index', compact('notifications'));
    }

    public function create()
    {
       if (!config('features.advanced_notifications')) {
           session()->flash('error', trans('main_trans.Pro_Feature_Message'));
           return redirect()->route('home');
       }
        $this->checkPermission(PermissionEnum::NOTIFICATION_ADD);
        $types = Type::where('is_active', true)->get();
        $students = Student::where('status', 1)->get();
        return view('notifications.create', compact('types', 'students'));
    }

    public function store(StoreNotificationRequest $request)
    {
        $this->checkPermission(PermissionEnum::NOTIFICATION_ADD);

        $data = $request->validated();
        $data['created_by'] = Auth::user()->id;

        $notification = $this->notificationService->createNotification($data);

        session()->flash('add', trans('main_trans.Notification_add_successfully'));
        return redirect()->route('notifications.index');
    }

    public function show(Notification $notification)
    {
        $this->checkPermission(PermissionEnum::NOTIFICATION_SHOW);
        return view('notifications.show', compact('notification'));
    }

    public function edit(Notification $notification)
    {
        $this->checkPermission(PermissionEnum::NOTIFICATION_EDIT);

        if ($notification->status !== 'draft') {
            return redirect()->route('notifications.index')
                ->with('error', trans('main_trans.Cannot_edit_sent_notification'));
        }

        $types = Type::where('is_active', true)->get();
        $students = Student::where('status', 1)->get();
        return view('notifications.edit', compact('notification', 'types', 'students'));
    }

    public function update(UpdateNotificationRequest $request, Notification $notification)
    {
        $this->checkPermission(PermissionEnum::NOTIFICATION_EDIT);

        if ($notification->status !== 'draft') {
            return redirect()->route('notifications.index')
                ->with('error', trans('main_trans.Cannot_edit_sent_notification'));
        }

        $this->notificationService->updateNotification($notification, $request->validated());

        session()->flash('edit', trans('main_trans.Notification_edit_successfully'));
        return redirect()->route('notifications.index');
    }

    public function destroy(Notification $notification)
    {
        $this->checkPermission(PermissionEnum::NOTIFICATION_DELETE);

        $this->notificationService->deleteNotification($notification);

        session()->flash('delete', trans('main_trans.Notification_delete_successfully'));
        return redirect()->route('notifications.index');
    }

    public function send(Notification $notification)
    {
        $this->checkPermission(PermissionEnum::NOTIFICATION_SEND);

        $result = $this->notificationService->sendNotification($notification);

        if ($result['success']) {
            session()->flash('add', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }

        return redirect()->route('notifications.index');
    }
}
