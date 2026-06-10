@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Users') }}
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
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Users') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Users_list') }}</span>
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
                    @can('User-add')
                        <div class="d-flex justify-content-between">
                            <a class="btn btn-outline-primary btn-block" href="{{ route('users.create') }}">{{ trans('main_trans.Add_user') }}</a>
                        </div>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive hoverable-table">
                        <table class="table table-hover" id="example1" data-page-length='50' style=" text-align: center;">
                            <thead>
                            <tr>
                                <th class="wd-5p-f border-bottom-0">#</th>
                                <th class="wd-5p-f border-bottom-0"></th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Username') }}</th>
                                <th class="wd-15p border-bottom-0">{{ trans('main_trans.Email') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Phone') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.User_type') }}</th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Status') }}</th>
                                <th class="wd-15p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($data as $key => $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        @if($user->photo == null)
                                            <img alt="avatar" class="rounded-circle avatar-md mr-2" src="{{URL::asset('assets/image/sanad.jpg')}}">
                                        @else
                                            <img alt="avatar" class="rounded-circle avatar-md mr-2" src="{{URL::asset('assets/image/Users/' . $user->id . '/' . $user->photo)}}">
                                        @endif
                                    </td>

                                    @php
                                        if(App::getLocale() == 'ar')
                                            $username = $user->name_ar;
                                        else
                                            $username = $user->name_en;
                                    @endphp

                                    <td>
                                        {{$username}}
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        {{ $user->phone }}
                                    </td>

                                    <td>
                                        @if (!empty($user->getRoleNames()))
                                            @foreach ($user->getRoleNames() as $v)
                                                <label class="badge badge-success" style="font-size: 12px !important;">{{ $v }}</label>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->status == 1 )
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
                                        @if ($user->id > 1 || Auth::user()->id==1)
                                            @can('User-edit')
                                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-info"
                                                   title="{{ trans('main_trans.Edit') }}"><i class="fas fa-pen"></i></a>
                                            @endcan

                                            @if($user->id != Auth::user()->id)
                                                @can('User-delete')
                                                    <a class="modal-effect btn btn-danger" data-effect="effect-scale"
                                                       data-user-id="{{ $user->id }}" data-username="{{ $username }}"
                                                       data-toggle="modal" href="#modaldemo8" title="{{ trans('main_trans.Delete') }}"><i
                                                            class="fas fa-trash"></i></a>
                                                @endcan
                                            @endif
                                        @endif
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

        <!-- Modal effects -->
        <div class="modal" id="modaldemo8">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.User_delete') }}</h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="{{ route('users.destroy', 'test') }}" method="post">
                        {{ method_field('delete') }}
                        @csrf
                        <div class="modal-body">
                            <p>{{ trans('main_trans.Are_you_sure_to_delete') }}</p><br>
                            <input type="hidden" name="id" id="id" value="">
                            <input class="form-control" name="username" id="username" type="text" readonly>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('main_trans.Close') }}</button>
                            <button type="submit" class="btn btn-danger">{{ trans('main_trans.Delete') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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

    <script>
        $('#modaldemo8').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var user_id = button.data('user-id')
            var username = button.data('username')
            var modal = $(this)
            modal.find('.modal-body #id').val(user_id);
            modal.find('.modal-body #username').val(username);
        })

    </script>

@endsection
