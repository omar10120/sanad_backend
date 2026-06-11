@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Students_2024_2025') }}
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
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Students_2024_2025') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Students_list_2024_2025') }}</span>
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
                        <div class="alert alert-info">
                            
                            <i class="fas fa-info-circle"></i> {{ trans('main_trans.Read_only_view_2024_2025') }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive hoverable-table">
                        
                        <table class="table table-hover" id="example1" data-page-length='100' style=" text-align: center;">
                            <thead>
                            <tr>
                                
                                <th class="wd-5p-f border-bottom-0">#</th>
                                <th class="wd-5p-f border-bottom-0"></th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.First_name') }}</th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Father_name') }}</th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Last_name') }}</th>
                                <th class="wd-15p border-bottom-0">{{ trans('main_trans.Email') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Phone') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Certificate_type') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.School') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.City') }}</th>
                                <th class="wd-10p border-bottom-0">
                                    {{trans('main_trans.Devices')}}
                                    @if(!config('features.student_devices'))
                                    <i class="fas fa-crown text-warning" title="{{ trans('main_trans.Pro_Only') }}"></i>
                                    @endif
                                </th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Status') }}</th>
                                <th class="wd-15p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $key => $student)
                                <tr>
                                    <td>{{ $student->id }}</td>
                                    <td>
                                        @if($student->photo == null)
                                            <img alt="avatar" class="rounded-circle avatar-md mr-2" src="{{URL::asset('assets/image/sanad.jpg')}}">
                                        @else
                                            <img alt="avatar" class="rounded-circle avatar-md mr-2" src="{{URL::asset('assets/image/Students/' . $student->id . '/' . $student->photo)}}">
                                        @endif
                                    </td>
                                    <td>{{ $student->first_name }}</td>
                                    <td>{{ $student->father_name }}</td>
                                    <td>{{ $student->last_name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->phone }}</td>
                                    <td>{{ $student->type->name }}</td>
                                    <td>{{ $student->school }}</td>
                                    <td>{{ trans('main_trans.' . $student->city ) }}</td>

                                    <td>
                                        @if(config('features.student_devices'))
                                            <span class="badge badge-info text-white">
                                                {{$student->getActiveDevicesCount()}}/{{$student->max_devices}}
                                            </span>
                                            @if($student->getActiveDevicesCount() > 0)
                                                <button class="btn btn-sm btn-outline-info ml-1"
                                                        data-toggle="modal"
                                                        data-target="#devicesModal"
                                                        data-student-id="{{$student->id}}"
                                                        data-student-name="{{$student->first_name}} {{$student->last_name}}">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            @endif
                                        @else
                                            <span class="badge badge-outline-warning" 
                                                  style="cursor: pointer; border: 1px solid #f0ad4e; color: #f0ad4e;"
                                                  onclick="showProModal(event)">
                                                <i class="fas fa-crown mr-1"></i> Pro
                                            </span>
                                        @endif
                                    </td>
{{--                                    <td>--}}
{{--                                        <div class="d-flex align-items-center">--}}
{{--                                            <span class="mr-2">{{$student->max_devices}}</span>--}}
{{--                                        </div>--}}
{{--                                    </td>--}}

                                    <td>
                                        @if ($student->status == 1 )
                                            <span class="label text-success d-flex">
                                                    <div class="dot-label bg-success" style="margin: -5px 20px 0px 0px"></div>{{ trans('main_trans.Enable') }}
                                                </span>
                                        @else
                                            <span class="label text-danger d-flex">
                                                    <div class="dot-label bg-danger" style="margin: -5px 20px 0px 0px"></div>{{ trans('main_trans.Disable') }}
                                                </span>
                                        @endif
                                    </td>

                                    <td>
                                        @can('Student-show')
                                            <a href="{{ route('student.show', $student->id) }}" class="btn btn-success"
                                               title="{{ trans('main_trans.Show') }}"><i class="fas fa-eye"></i></a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!--/div-->

    </div>
    <!-- /row -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
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
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/responsive.bootstrap4.min.js') }}"></script>
    <!--Internal  Datatable js -->
    <script src="{{ URL::asset('assets/js/table-data.js') }}"></script>
    <!--Internal  Notify js -->
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
    <!-- Internal Modal js-->
    <script src="{{ URL::asset('assets/js/modal.js') }}"></script>
@endsection
