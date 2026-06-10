@extends('layouts.master')

@section('title')
    {{ trans('main_trans.Report_details') }} #{{ $report->id }}
@endsection

@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Question_reports') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Report_details') }}</span>
            </div>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.question-reports.index') }}" class="btn btn-secondary">{{ trans('main_trans.Back') }}</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="row row-sm">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ trans('main_trans.Report') }} #{{ $report->id }}</h5>
                    <span class="badge badge-{{ $report->status === 'resolved' ? 'success' : ($report->status === 'rejected' ? 'danger' : ($report->status === 'reviewed' ? 'info' : 'warning')) }}">{{ $report->status }}</span>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">{{ trans('main_trans.Question') }}</dt>
                        <dd class="col-sm-9">
                            @if($report->question)
                                <a href="{{ route('question.show', $report->question->uuid) }}" target="_blank" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i> عرض السؤال #{{ $report->question->id }}
                                </a>
                            @else
                                {{ trans('main_trans.Not_available') }}
                            @endif
                        </dd>

                        <dt class="col-sm-3">{{ trans('main_trans.Student') }}</dt>
                        <dd class="col-sm-9">{{ optional($report->student)->first_name }} {{ optional($report->student)->last_name }}</dd>

                        <dt class="col-sm-3">{{ trans('main_trans.Report_type') }}</dt>
                        <dd class="col-sm-9">
                            @switch($report->report_type)
                                @case('spelling_error') {{ trans('main_trans.Spelling_error') }} @break
                                @case('scientific_error') {{ trans('main_trans.Scientific_error') }} @break
                                @default {{ trans('main_trans.Not_available') }}
                            @endswitch
                        </dd>

                        <dt class="col-sm-3">{{ trans('main_trans.Description') }}</dt>
                        <dd class="col-sm-9">{{ $report->description }}</dd>

                        <dt class="col-sm-3">{{ trans('main_trans.Admin_notes') }}</dt>
                        <dd class="col-sm-9">{{ $report->admin_notes ?: trans('main_trans.Not_available') }}</dd>

                        <dt class="col-sm-3">{{ trans('main_trans.Reviewed_by') }}</dt>
                        <dd class="col-sm-9">{{ optional($report->reviewer)->name_ar ?: optional($report->reviewer)->name_en ?: trans('main_trans.Not_available') }}</dd>

                        <dt class="col-sm-3">{{ trans('main_trans.Created_at') }}</dt>
                        <dd class="col-sm-9">{{ $report->created_at }}</dd>

                        <dt class="col-sm-3">{{ trans('main_trans.Reviewed_at') }}</dt>
                        <dd class="col-sm-9">{{ $report->reviewed_at ?: trans('main_trans.Not_available') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header pb-0">
                    <h6 class="mb-0">{{ trans('main_trans.Update_status') }}</h6>
                </div>
                <div class="card-body">
                    <form id="updateStatusForm" action="{{ route('admin.question-reports.update-status', $report) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>{{ trans('main_trans.Status') }}</label>
                            <select name="status" class="form-control">
                                @foreach(['pending' => trans('main_trans.Pending'), 'reviewed' => trans('main_trans.Reviewed'), 'resolved' => trans('main_trans.Resolved'), 'rejected' => trans('main_trans.Rejected')] as $value => $label)
                                    <option value="{{ $value }}" {{ $report->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>{{ trans('main_trans.Admin_notes') }}</label>
                            <textarea name="admin_notes" class="form-control" rows="3" placeholder="{{ trans('main_trans.Admin_notes') }}..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">{{ trans('main_trans.Save') }}</button>
                    </form>
                    <hr>
                    <form action="{{ route('admin.question-reports.destroy', $report) }}" method="POST" onsubmit="return confirm('{{ trans('main_trans.Are_you_sure_to_delete') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">{{ trans('main_trans.Delete') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
</div>
@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
    <script>
        document.getElementById('updateStatusForm').addEventListener('submit', async function(e){
            e.preventDefault();
            const form = e.currentTarget;
            const url = form.getAttribute('action');
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
                    setTimeout(function(){ window.location.reload(); }, 700);
                } else {
                    notif({ msg: data.message || '{{ trans('main_trans.Error_saving') }}', type: 'error' });
                }
            } catch (err) {
                notif({ msg: '{{ trans('main_trans.Server_connection_failed') }}', type: 'error' });
            }
        });
    </script>
@endsection


