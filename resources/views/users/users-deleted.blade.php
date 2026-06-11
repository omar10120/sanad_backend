@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Deleted_users') }}
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
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Users') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Deleted_users') }}</span>
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
                    <div class="main-content-label mg-b-5">
                        <div class="pull-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('users.index') }}"><i class="fas fa-arrow-right"></i>{{ ' ' . trans('main_trans.Back') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive hoverable-table">
                        <table class="table table-hover" id="example-delete" data-page-length='50' style=" text-align: center;">
                            <thead>
                            <tr>
                                <th class="wd-5p-f border-bottom-0">#</th>
                                <th class="wd-5p-f border-bottom-0"></th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Username') }}</th>
                                <th class="wd-15p border-bottom-0">{{ trans('main_trans.Email') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Phone') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.User_type') }}</th>
                                <th class="wd-5p border-bottom-0">{{ trans('main_trans.Status') }}</th>
                                <th class="wd-10p border-bottom-0">{{ trans('main_trans.Deleted_at') }}</th>
                                <th class="wd-15p border-bottom-0">{{ trans('main_trans.Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        @if($user->photo == null)
                                            <img alt="avatar" class="rounded-circle avatar-md mr-2" src="{{ URL::asset('assets/image/sanad.jpg') }}">
                                        @else
                                            <img alt="avatar" class="rounded-circle avatar-md mr-2" src="{{ URL::asset('assets/image/Users/' . $user->id . '/' . $user->photo) }}">
                                        @endif
                                    </td>

                                    @php
                                        if(App::getLocale() == 'ar')
                                            $username = $user->name_ar;
                                        else
                                            $username = $user->name_en;
                                    @endphp

                                    <td>{{ $username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>
                                        @if (!empty($user->getRoleNames()))
                                            @foreach ($user->getRoleNames() as $v)
                                                <label class="badge badge-success" style="font-size: 12px !important;">{{ $v }}</label>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        @if ($user->status == 1)
                                            <span class="label text-success d-flex">
                                                <div class="dot-label bg-success" style="margin: -5px 20px 0px 0px"></div>{{ trans('main_trans.Enable') }}
                                            </span>
                                        @else
                                            <span class="label text-danger d-flex">
                                                <div class="dot-label bg-danger" style="margin: -5px 20px 0px 0px"></div>{{ trans('main_trans.Disable') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $user->deleted_at }}</td>
                                    <td>
                                        @can('User-restore-deleted')
                                            <a class="modal-effect btn btn-info" data-effect="effect-scale"
                                               data-user-id="{{ $user->id }}" data-username="{{ $username }}"
                                               data-toggle="modal" href="#modal-restore" title="{{ trans('main_trans.Restore') }}"><i
                                                    class="fas fa-trash-restore"></i></a>
                                        @endcan

                                        @can('User-delete')
                                            <a class="modal-effect btn btn-danger" data-effect="effect-scale"
                                               data-user-id="{{ $user->id }}" data-username="{{ $username }}"
                                               data-toggle="modal" href="#modal-force-delete" title="{{ trans('main_trans.Delete') }}"><i
                                                    class="fas fa-trash"></i></a>
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

        <div class="modal" id="modal-restore">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.Restore_user') }}</h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="{{ route('archived-user.update', 'archived-user') }}" method="post">
                        {{ method_field('patch') }}
                        @csrf
                        <div class="modal-body">
                            <p>{{ trans('main_trans.Are_you_sure_to_restore') }}</p><br>
                            <input type="hidden" name="id" id="restore-id" value="">
                            <input class="form-control" name="username" id="restore-username" type="text" readonly>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('main_trans.Close') }}</button>
                            <button type="submit" class="btn btn-info">{{ trans('main_trans.Restore') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal" id="modal-force-delete">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content modal-content-demo">
                    <div class="modal-header">
                        <h6 class="modal-title">{{ trans('main_trans.User_delete') }}</h6><button aria-label="Close" class="close"
                            data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <form action="{{ route('archived-user.destroy', 'archived-user') }}" method="post">
                        {{ method_field('delete') }}
                        @csrf
                        <div class="modal-body">
                            <p>{{ trans('main_trans.Are_you_sure_to_delete_permanently') }}</p><br>
                            <input type="hidden" name="id" id="force-delete-id" value="">
                            <input class="form-control" name="username" id="force-delete-username" type="text" readonly>
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
    <script src="{{ URL::asset('assets/js/modal.js') }}"></script>

    <script>
        $('#modal-restore').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var user_id = button.data('user-id')
            var username = button.data('username')
            var modal = $(this)
            modal.find('.modal-body #restore-id').val(user_id);
            modal.find('.modal-body #restore-username').val(username);
        })

        $('#modal-force-delete').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget)
            var user_id = button.data('user-id')
            var username = button.data('username')
            var modal = $(this)
            modal.find('.modal-body #force-delete-id').val(user_id);
            modal.find('.modal-body #force-delete-username').val(username);
        })
    </script>
@endsection
