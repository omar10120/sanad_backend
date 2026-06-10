@extends('layouts.master')

@section('title')
    {{ trans('main_trans.Question_reports') }}
@endsection

@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Question_reports') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Question_reports_list') }}</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @include('components.flash-messages')

    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <form method="GET" action="{{ route('admin.question-reports.index') }}" class="w-100">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <label class="form-label">{{ trans('main_trans.Status') }}</label>
                                <select class="form-control" name="status">
                                    <option value="">الكل</option>
                                    @php $currentStatus = request('status'); @endphp
                                    @foreach(['pending' => trans('main_trans.Pending'), 'reviewed' => trans('main_trans.Reviewed'), 'resolved' => trans('main_trans.Resolved'), 'rejected' => trans('main_trans.Rejected')] as $value => $label)
                                        <option value="{{ $value }}" {{ $currentStatus === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">{{ trans('main_trans.Report_type') }}</label>
                                <select class="form-control" name="report_type">
                                    <option value="">الكل</option>
                                    @php $currentType = request('report_type'); @endphp
                                    @foreach(['spelling_error' => trans('main_trans.Spelling_error'), 'scientific_error' => trans('main_trans.Scientific_error')] as $value => $label)
                                        <option value="{{ $value }}" {{ $currentType === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">{{ trans('main_trans.Question_ID') }}</label>
                                <input type="number" class="form-control" name="question_id" value="{{ request('question_id') }}" placeholder="{{ trans('main_trans.Question_ID') }}">
                            </div>
                            <div class="col-md-3 mb-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2 ml-2">{{ trans('main_trans.Filter') }}</button>
                                <a href="{{ route('admin.question-reports.index') }}" class="btn btn-secondary">{{ trans('main_trans.Reset') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive hoverable-table">
                        <table class="table table-hover" id="example1" data-page-length='25' style=" text-align: center;">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('main_trans.Question') }}</th>
                                <th>{{ trans('main_trans.Student') }}</th>
                                <th>{{ trans('main_trans.Report_type') }}</th>
                                <th>{{ trans('main_trans.Status') }}</th>
                                <th>{{ trans('main_trans.Created_at') }}</th>
                                <th>{{ trans('main_trans.Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($reports as $report)
                                <tr>
                                    <td>{{ $report->id }}</td>
                                    <td>
                                        @if($report->question)
                                            <a href="{{ route('question.show', $report->question->uuid) }}" target="_blank">سؤال #{{ $report->question->id }}</a>
                                        @else
                                            {{ trans('main_trans.Not_available') }}
                                        @endif
                                    </td>
                                    <td>{{ optional($report->student)->first_name }} {{ optional($report->student)->last_name }}</td>
                                    <td>
                                        @switch($report->report_type)
                                            @case('spelling_error') {{ trans('main_trans.Spelling_error') }} @break
                                            @case('scientific_error') {{ trans('main_trans.Scientific_error') }} @break
                                            @default {{ trans('main_trans.Not_available') }}
                                        @endswitch
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $report->status === 'resolved' ? 'success' : ($report->status === 'rejected' ? 'danger' : ($report->status === 'reviewed' ? 'info' : 'warning')) }}">
                                            {{ [
                                                'pending' => trans('main_trans.Pending'),
                                                'reviewed' => trans('main_trans.Reviewed'),
                                                'resolved' => trans('main_trans.Resolved'),
                                                'rejected' => trans('main_trans.Rejected'),
                                            ][$report->status] ?? $report->status }}
                                        </span>
                                    </td>
                                    <td>{{ $report->created_at }}</td>
                                    <td class="text-nowrap">
                                        <a class="btn btn-sm btn-success" href="{{ route('admin.question-reports.show', $report) }}">{{ trans('main_trans.Show') }}</a>

                                        @if($report->question)
                                            <a class="btn btn-sm btn-info" href="{{ route('question.show', $report->question->uuid) }}" target="_blank" title="عرض السؤال">
                                                <i class="fas fa-eye"></i> عرض السؤال
                                            </a>
                                        @endif

                                        <button class="btn btn-sm btn-outline-secondary" data-toggle="collapse" data-target="#upd-{{ $report->id }}">{{ trans('main_trans.Update_status') }}</button>

                                        <form method="POST" action="{{ route('admin.question-reports.destroy', $report) }}" class="d-inline-block delete-report-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">{{ trans('main_trans.Delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr id="upd-{{ $report->id }}" class="collapse">
                                    <td colspan="7" class="text-left">
                                        <form class="form-inline update-status-form" data-action="{{ route('admin.question-reports.update-status', $report) }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="form-group m-2">
                                                <label class="m-2">{{ trans('main_trans.Status') }}</label>
                                                <select name="status" class="form-control">
                                                    @foreach(['pending' => trans('main_trans.Pending'), 'reviewed' => trans('main_trans.Reviewed'), 'resolved' => trans('main_trans.Resolved'), 'rejected' => trans('main_trans.Rejected')] as $value => $label)
                                                        <option value="{{ $value }}" {{ $report->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group m-2 d-flex align-items-center row">
                                                <label class="m-2 mb-0" for="admin_notes_{{ $report->id }}">{{ trans('main_trans.Admin_notes') }}</label>
                                                <input type="text" class="form-control ml-2" style="min-width: 250px;" id="admin_notes_{{ $report->id }}" name="admin_notes" value="{{ $report->admin_notes }}" placeholder="{{ trans('main_trans.Admin_notes') }}">
                                            </div>
                                            <button type="submit" class="btn btn-success">{{ trans('main_trans.Save') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <div class="mt-3">
                            {{ $reports->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>

    <script>
        document.querySelectorAll('.update-status-form').forEach(function(form){
            form.addEventListener('submit', async function(e){
                e.preventDefault();
                const url = form.getAttribute('data-action');
                const formData = new FormData(form);
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    });
                    const data = await response.json();
                    if (data.success) {
                        notif({ msg: data.message || '{{ trans('main_trans.Saved_successfully') }}', type: 'success' });
                        setTimeout(function(){ window.location.reload(); }, 600);
                    } else {
                        notif({ msg: data.message || '{{ trans('main_trans.Error_saving') }}', type: 'error' });
                    }
                } catch (err) {
                    notif({ msg: '{{ trans('main_trans.Server_connection_failed') }}', type: 'error' });
                }
            });
        });

        document.querySelectorAll('.delete-report-form').forEach(function(form){
            form.addEventListener('submit', function(e){
                if (!confirm('{{ trans('main_trans.Are_you_sure_to_delete') }}')) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endsection


