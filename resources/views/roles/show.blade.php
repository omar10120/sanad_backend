@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Show_role') }}
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
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Roles') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0"> / {{ trans('main_trans.Show_role') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->


    <!-- row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mg-b-20">
                <div class="card-body">
                    <div class="main-content-label mg-b-5">
                        <div class="pull-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('roles.index') }}">{{ trans('main_trans.Back') }}</a>
                        </div>
                    </div>
                    <div class="row">
                        <!-- col -->
                        <div class="col-lg-12">
                            <ul id="treeview1">
                                <li style="font-size: 20px; margin: 10px; color: midnightblue">{{ $role->name }}
                                </li>
                                <ul>
                                    <div class="row">
                                        @if(!empty($rolePermissions))
                                            @foreach($rolePermissions as $v)
                                                <div class="col-lg-2 mg-t-20 mg-lg-t-0">
                                                    <li>{{ trans('main_trans.' . $v->name) }}</li>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </ul>
                            </ul>
                        </div>
                        <!-- /col -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
    </div>
    <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection
@section('js')
    <script src="{{URL::asset('assets/plugins/treeview/treeview.js')}}"></script>

@endsection
