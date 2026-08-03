@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Notification_details') }}
@endsection
@section('css')
    <!--Internal   Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Notifications') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Notification_details') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')

    @include('components.flash-messages')

    <!-- row opened -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mg-b-0">{{ trans('main_trans.Notification_details') }}</h4>
                        <div>
                            @if(in_array($notification->status, ['draft', 'failed', 'processing'], true))
                                @can('Notification-edit')
                                    @if($notification->status === 'draft')
                                    <a href="{{ route('notifications.edit', $notification->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> {{ trans('main_trans.Edit') }}
                                    </a>
                                    @endif
                                @endcan

                                @can('Notification-send')
                                    <form method="POST" action="{{ route('notifications.send', $notification->id) }}" id="send-notification-form" style="display: inline;">
                                        @csrf
                                        <button type="submit" id="send-notification-btn" class="btn btn-success" onclick="return confirm('{{ trans('main_trans.Are_you_sure_to_send') }}?')">
                                            <i class="fas fa-paper-plane"></i> {{ trans('main_trans.Send_now') }}
                                        </button>
                                    </form>
                                @endcan
                            @endif

                            <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left"></i> {{ trans('main_trans.Back') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ trans('main_trans.Notification_title') }}</label>
                                <p class="form-control-plaintext">{{ $notification->title }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ trans('main_trans.Notification_type') }}</label>
                                <p>
                                    <span class="badge badge-{{ $notification->type === 'general' ? 'primary' : ($notification->type === 'system' ? 'danger' : ($notification->type === 'announcement' ? 'success' : 'warning')) }}">
                                        {{ trans('main_trans.' . ucfirst(str_replace('_', ' ', $notification->type))) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{ trans('main_trans.Notification_body') }}</label>
                        <p class="form-control-plaintext">{{ $notification->body }}</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ trans('main_trans.Target_type') }}</label>
                                <p>
                                    <span class="badge badge-info">
                                        {{ trans('main_trans.' . ucfirst(str_replace('_', ' ', $notification->target_type))) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ trans('main_trans.Status') }}</label>
                                <p>
                                    <span class="badge badge-{{ match($notification->status) {
                                        'sent' => 'success',
                                        'draft' => 'warning',
                                        'processing' => 'primary',
                                        'failed' => 'danger',
                                        default => 'info',
                                    } }}">
                                        {{ trans('main_trans.' . ucfirst($notification->status)) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($notification->scheduled_at)
                        <div class="form-group">
                            <label>{{ trans('main_trans.Scheduled_at') }}</label>
                            <p class="form-control-plaintext">{{ $notification->scheduled_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    @endif

                    @if($notification->sent_at)
                        <div class="form-group">
                            <label>{{ trans('main_trans.Sent_at') }}</label>
                            <p class="form-control-plaintext">{{ $notification->sent_at->format('Y-m-d H:i:s') }}</p>
                        </div>
                    @endif

                    <div class="form-group">
                        <label>{{ trans('main_trans.Created_by') }}</label>
                        <p class="form-control-plaintext">{{ $notification->creator->name ?? 'N/A' }}</p>
                    </div>

                    @if($notification->status === 'sent')
                        @if(config('features.advanced_notifications'))
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('main_trans.Total_recipients') }}</label>
                                    <p class="form-control-plaintext">{{ $notification->total_recipients }}</p>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('main_trans.Successful_sends') }}</label>
                                    <p class="form-control-plaintext text-success">{{ $notification->successful_sends }}</p>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('main_trans.Failed_sends') }}</label>
                                    <p class="form-control-plaintext text-danger">{{ $notification->failed_sends }}</p>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ trans('main_trans.Success_rate') }}</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge badge-{{ $notification->success_rate >= 80 ? 'success' : ($notification->success_rate >= 50 ? 'warning' : 'danger') }}">
                                            {{ number_format($notification->success_rate, 1) }}%
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @else
                        <label>{{ trans('main_trans.Send_results') }}:</label>
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center py-4" style="background-color: #f8f9fa; border-radius: 5px; border: 1px dashed #ddd;">
                                        <i class="fas fa-crown fa-3x text-warning mb-3"></i>
                                        <h5>{{ trans('main_trans.Pro_Feature_Title') }}</h5>
                                        <p class="text-muted mb-0">{{ trans('main_trans.Pro_Feature_Message') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(in_array($notification->status, ['sent', 'failed'], true) && $notification->logs()->exists() && config('features.advanced_notifications'))
        <div class="row row-sm">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h4 class="card-title mg-b-0">{{ trans('main_trans.Send_results') }}</h4>
                        <small class="text-muted">{{ trans('main_trans.Failed_sends') }}: {{ $notification->failed_sends }}</small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="sendResultsTable" data-page-length='12' style=" text-align: center;">
                                <thead>
                                <tr>
                                    <th class="wd-10p-f border-bottom-0">{{ trans('main_trans.Student_id') }}</th>
                                    <th class="wd-15p-f border-bottom-0">{{ trans('main_trans.Status') }}</th>
                                    <th class="wd-20p-f border-bottom-0">{{ trans('main_trans.Sent_at') }}</th>
                                    <th class="wd-20p border-bottom-0">{{ trans('main_trans.Error') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($notification->logs()->latest()->limit(500)->get() as $log)
                                    <tr>
                                        <td>{{ $log->student_id }}</td>
                                        <td>
                                            <span class="badge badge-{{ $log->status === 'success' ? 'success' : 'danger' }}">
                                                {{ ucfirst($log->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $log->created_at }}</td>
                                        <td>{{ $log->error_message ?? '-' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
        <!-- row closed -->
    </div>
    </div>

@endsection
@section('js')
    <!-- Internal Data tables -->
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/vfs_fonts.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
    @if(in_array($notification->status, ['sent', 'failed'], true) && $notification->logs()->exists() && config('features.advanced_notifications'))
    <script>
        $('#sendResultsTable').DataTable({
            responsive: true,
            pageLength: 12,
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
                lengthMenu: '_MENU_',
            }
        });
    </script>
    @endif
    <script>
        $('#send-notification-form').on('submit', function() {
            var $btn = $('#send-notification-btn');
            $btn.prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin"></i> {{ trans('main_trans.Notification_sending_please_wait') }}');
        });
    </script>
@endsection
