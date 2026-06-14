@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Certificate_types') }}
@endsection
@section('css')
    <link href="{{URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/buttons.bootstrap4.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.bootstrap4.min.css')}}" rel="stylesheet" />
    <link href="{{URL::asset('assets/plugins/datatable/css/jquery.dataTables.min.css')}}" rel="stylesheet">
    <link href="{{URL::asset('assets/plugins/datatable/css/responsive.dataTables.min.css')}}" rel="stylesheet">
@endsection
@section('page-header')
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Coures') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Certificate_types') }}</span>
            </div>
        </div>
    </div>
@endsection
@section('content')

    @include('components.flash-messages')

    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    @if($types->count() > 0)
                        <div class="table-responsive hoverable-table">
                            <table class="table table-hover" id="example1" data-page-length='50' style="text-align: center;">
                                <thead>
                                <tr>
                                    <th class="wd-5p-f border-bottom-0">#</th>
                                    <th class="wd-10p border-bottom-0">{{ trans('main_trans.Name') }}</th>
                                    <th class="wd-5p border-bottom-0">{{ trans('main_trans.Number_of_course_subjects') }}</th>
                                    <th class="wd-10p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($types->sortBy('order') as $type)
                                    <tr>
                                        <td>{{ $type->id }}</td>
                                        <td>{{ $type->name }}</td>
                                        <td>{{ \App\Models\Type::withCount('subjectVideos')->find($type->id)->subject_videos_count }}</td>
                                        <td>
                                            @can('SubjectVideo-show')
                                                <a class="btn btn-success" href="{{ route('type.subject-video', $type->id) }}"
                                                   title="{{ trans('main_trans.Course_subjects') }}">
                                                    <i class="fas fa-book"></i> {{ trans('main_trans.Course_subjects') }}
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <h5>{{ trans('main_trans.No_Types_Available') }}</h5>
                            <p>{{ trans('main_trans.No_Types_Available_Description') }}</p>
                        </div>
                    @endif
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
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script>
        $('#example1').DataTable({
            responsive: true,
            language: {
                searchPlaceholder: 'Search...',
                sSearch: '',
                lengthMenu: '_MENU_',
            }
        });
    </script>
@endsection
