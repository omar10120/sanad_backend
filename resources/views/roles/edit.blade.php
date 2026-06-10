@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Edit_role') }}
@stop
@section('css')
    <!--Internal  Font Awesome -->
    <link href="{{URL::asset('assets/plugins/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
    <!--Internal  treeview -->
    <link href="{{URL::asset('assets/plugins/treeview/treeview-rtl.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Roles') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0"> / {{ trans('main_trans.Edit_role') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection
@section('content')
@php
use Spatie\Html\Elements\Form
 @endphp
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>خطا</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

<form method="POST" action="{{ route('roles.update', $role->id) }}">
    @csrf
    @method('PATCH')
{{--    {!! Form::model($role, ['method' => 'PATCH','route' => ['roles.update', $role->id]]) !!}--}}
    <!-- row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mg-b-20">
                <div class="card-body">
                    <div class="main-content-label mg-b-5">
                        <div class="form-group">
                            <p> {{ trans('main_trans.Role_name') }}:</p>
                            <input type="text" name="name" placeholder="Name" class="form-control" value="{{$role->name}}">
{{--                            {!! Form::text('name', null, array('placeholder' => 'Name','class' => 'form-control')) !!}--}}
                        </div>
                    </div>
                    <div class="row">
                        <!-- col -->
                        <div class="col-lg-12">
                            <ul id="treeview1">
                                <li>{{ trans('main_trans.Permissions') }}
                                </li>
                                <ul>
                                    <div class="row">
                                            @foreach($permission as $value)
                                                <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                                                    <label>
                                                        <input type="checkbox" name="permission[]" value="{{ $value->id }}" class="name" {{ in_array($value->id, $rolePermissions) ? 'checked' : '' }}>
                                                        {{ trans('main_trans.' . $value->name) }}
                                                    </label>
                                                </div>


                                            @endforeach
                                    </div>
                                </ul>
                            </ul>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            <button type="submit" class="btn btn-main-primary">{{ trans('main_trans.Edit_role') }}</button>
                        </div>
                        <!-- /col -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
</form>
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
{{--    {!! Form::close() !!}--}}
@endsection
@section('js')
    <!-- Internal Treeview js -->
    <script src="{{URL::asset('assets/plugins/treeview/treeview.js')}}"></script>
@endsection
