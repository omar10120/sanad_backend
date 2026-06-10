@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Roles') }}
@endsection
@section('css')
    <!--Internal Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Users') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0"> / {{ trans('main_trans.Roles') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')

@include('components.flash-messages')

    <!-- row -->
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between">
                        <div class="col-lg-12 margin-tb">
                            <div class="pull-right">
                                @can('Role-add')
                                    <a class="btn btn-secondary btn-sm" href="{{ route('roles.create') }}">{{ trans('main_trans.Add_role') }}</a>
                                @endcan
                            </div>
                        </div>
                        <br>
                    </div>

                </div>
                @php
                    $i=0;
                @endphp
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mg-b-0 text-md-nowrap table-hover ">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('main_trans.Role_name') }}</th>
                                <th>{{ trans('main_trans.Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($roles as $key => $role)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        @can('Role-show')
                                            <a class="btn btn-success btn-sm"
                                               href="{{ route('roles.show', $role->id) }}">{{ trans('main_trans.Show_role') }}</a>
                                        @endcan

                                        @if ($role->name !== 'Owner' && $role->name !== 'Student')
                                            @can('Role-edit')
                                                <a class="btn btn-primary btn-sm"
                                                   href="{{ route('roles.edit', $role->id) }}">{{ trans('main_trans.Edit_role') }}</a>
                                            @endcan

                                            @can('Role-delete')
                                                <form method="POST" action="{{ route('roles.destroy', $role->id) }}" style="display:inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">{{ trans('main_trans.Delete_Role') }}</button>
                                                </form>
                                            @endcan
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
    </div>
    <!-- row closed -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <!--Internal  Notify js -->
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
@endsection
