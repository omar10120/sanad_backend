@extends('layouts.master')

@section('title')
    {{ trans('main_trans.phone_verification_codes') }}
@endsection

@section('css')
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
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.phone_verification_codes') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.recent_phone_verification_codes') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive hoverable-table">
                        <table class="table table-hover" id="phoneCodesTable" data-page-length="50" style="text-align: center;">
                            <thead>
                            <tr>
                                <th class="wd-5p-f border-bottom-0">#</th>
                                <th class="wd-15p border-bottom-0">{{ trans('main_trans.Phone') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Code') }}</th>
                                <th class="wd-20p border-bottom-0">{{ trans('main_trans.activation_type') }}</th>
                                <th class="wd-20p border-bottom-0">{{ trans('main_trans.generated_at') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $typeLabels = [
                                    \App\Models\PhoneVerificationCode::TYPE_REGISTRATION => trans('main_trans.registration_activation'),
                                    \App\Models\PhoneVerificationCode::TYPE_PASSWORD_RESET => trans('main_trans.password_reset_activation'),
                                    \App\Models\PhoneVerificationCode::TYPE_PHONE_CHANGE => trans('main_trans.phone_change_activation'),
                                ];
                            @endphp
                            @forelse($codes as $code)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $code->phone }}</td>
                                    <td>{{ $code->code }}</td>
                                    <td>{{ $typeLabels[$code->type] ?? $code->type }}</td>
                                    <td>{{ optional($code->created_at)->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">{{ trans('main_trans.no_phone_verification_codes') }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@section('js')
    <!-- Internal Data tables -->
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.dataTables.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
    <!--Internal  Datatable js -->
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>

    <script>
        $(function () {
            $('#phoneCodesTable').DataTable({
                pageLength: 50,
                order: [[4, 'desc']]
            });
        });
    </script>
@endsection

