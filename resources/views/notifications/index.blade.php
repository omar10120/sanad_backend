@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Notifications') }}
@endsection
@section('css')
    <!-- Internal Data table css -->
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <!--Internal   Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Notifications') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.All_notifications') }}</span>
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
                    @can('Notification-add')
                        <div class="d-flex justify-content-between">
                            <a class="btn btn-outline-primary btn-block" href="{{ route('notifications.create') }}">{{ trans('main_trans.Send_notification') }}</a>
                        </div>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive hoverable-table">
                        <table class="table table-hover" id="example1" data-page-length='12' style=" text-align: center;">
                            <thead>
                            <tr>
                                <th class="wd-5p-f border-bottom-0">#</th>
                                <th class="wd-15p border-bottom-0">{{ trans('main_trans.Notification_title') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Notification_type') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Target_type') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Status') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Success_rate') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Total_recipients') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Created_at') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($notifications as $notification)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $notification->title }}</td>
                                    <td>
                                        <span class="badge badge-{{ $notification->type === 'general' ? 'primary' : ($notification->type === 'system' ? 'danger' : ($notification->type === 'announcement' ? 'success' : 'warning')) }}">
                                            {{ trans('main_trans.' . ucfirst(str_replace('_', ' ', $notification->type))) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ trans('main_trans.' . ucfirst(str_replace('_', ' ', $notification->target_type))) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $notification->status === 'sent' ? 'success' : ($notification->status === 'draft' ? 'warning' : 'info') }}">
                                            {{ trans('main_trans.' . ucfirst($notification->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(config('features.advanced_notifications'))
                                            @if($notification->status === 'sent')
                                                <span class="badge badge-{{ $notification->success_rate >= 80 ? 'success' : ($notification->success_rate >= 50 ? 'warning' : 'danger') }}">
                                                    {{ number_format($notification->success_rate, 1) }}%
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        @else
                                            <span class="badge badge-outline-warning"
                                                  style="cursor: pointer; border: 1px solid #f0ad4e; color: #f0ad4e;"
                                                  onclick="showProModal(event)">
                                                <i class="fas fa-crown mr-1"></i> Pro
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $notification->total_recipients }}</td>
                                    <td>{{ $notification->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('Notification-show')
                                                <a href="{{ route('notifications.show', $notification->id) }}" class="btn btn-sm btn-info" title="{{ trans('main_trans.Show') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endcan

                                            @if($notification->status === 'draft')
                                                @can('Notification-edit')
                                                    <a href="{{ route('notifications.edit', $notification->id) }}" class="btn btn-sm btn-warning" title="{{ trans('main_trans.Edit') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan

                                                @can('Notification-send')
                                                    <form method="POST" action="{{ route('notifications.send', $notification->id) }}" style="display: inline;">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success" title="{{ trans('main_trans.Send_now') }}" onclick="return confirm('{{ trans('main_trans.Are_you_sure_to_send') }}?')">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    </form>
                                                @endcan
                                            @endif

                                            @can('Notification-delete')
                                                <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ trans('main_trans.Delete') }}" onclick="return confirm('{{ trans('main_trans.Are_you_sure_to_delete') }}?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
    <script>
        $('#example1').DataTable({
            responsive: true,
            pageLength: 12,
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
                lengthMenu: '_MENU_',
            }
        });
    </script>
@endsection
