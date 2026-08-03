@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Create_notification') }}
@endsection
@section('css')
    <!--Internal   Notify -->
    <link href="{{ URL::asset('assets/plugins/notify/css/notifIt.css') }}" rel="stylesheet" />
    <!-- Select2 css -->
    <link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet">
    <!--Internal  Datetimepicker-slider css -->
    <link href="{{ URL::asset('assets/plugins/amazeui-datetimepicker/css/amazeui.datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/jquery-simple-datetimepicker/jquery.datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ URL::asset('assets/plugins/pickerjs/css/picker.min.css') }}" rel="stylesheet">
    <!-- Internal Spectrum-colorpicker css -->
    <link href="{{ URL::asset('assets/plugins/spectrum-colorpicker/css/spectrum.css') }}" rel="stylesheet">
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Notifications') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main_trans.Create_notification') }}</span>
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
                        <h4 class="card-title mg-b-0">{{ trans('main_trans.Create_notification') }}</h4>
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> {{ trans('main_trans.Back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('notifications.store') }}" method="post" autocomplete="off">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">{{ trans('main_trans.Notification_title') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">{{ trans('main_trans.Notification_type') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="">{{ trans('main_trans.Select') }}</option>
                                        <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>{{ trans('main_trans.General') }}</option>
                                        <option value="question_update" {{ old('type') == 'question_update' ? 'selected' : '' }}>{{ trans('main_trans.Question_update') }}</option>
                                        <option value="system" {{ old('type') == 'system' ? 'selected' : '' }}>{{ trans('main_trans.System') }}</option>
                                        <option value="announcement" {{ old('type') == 'announcement' ? 'selected' : '' }}>{{ trans('main_trans.Announcement') }}</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="body">{{ trans('main_trans.Notification_body') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="4" required>{{ old('body') }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="target_type">{{ trans('main_trans.Target_type') }} <span class="text-danger">*</span></label>
                                    <select class="form-control @error('target_type') is-invalid @enderror" id="target_type" name="target_type" required>
                                        <option value="">{{ trans('main_trans.Select') }}</option>
                                        <option value="all" {{ old('target_type') == 'all' ? 'selected' : '' }}>{{ trans('main_trans.All_students') }}</option>
                                        @if(config('features.advanced_notifications'))
                                        <option value="type" {{ old('target_type') == 'type' ? 'selected' : '' }}>{{ trans('main_trans.By_type') }}</option>
                                        <option value="student" {{ old('target_type') == 'student' ? 'selected' : '' }}>{{ trans('main_trans.By_student') }}</option>
                                        @else
                                        <option value="type" disabled>{{ trans('main_trans.By_type') }} ({{ trans('main_trans.Pro_Only') }})</option>
                                        <option value="student" disabled>{{ trans('main_trans.By_student') }} ({{ trans('main_trans.Pro_Only') }})</option>
                                        @endif
                                    </select>
                                    @error('target_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="scheduled_at">
                                        {{ trans('main_trans.Scheduled_at') }}
                                        @if(!config('features.advanced_notifications'))
                                        <span class="badge badge-warning" style="font-size: 10px;">
                                            <i class="fas fa-crown"></i> {{ trans('main_trans.Pro_Only') }}
                                        </span>
                                        @endif
                                    </label>
                                    <input type="datetime-local" 
                                           class="form-control @error('scheduled_at') is-invalid @enderror" 
                                           id="scheduled_at" 
                                           name="scheduled_at" 
                                           value="{{ old('scheduled_at') }}"
                                           @if(!config('features.advanced_notifications')) disabled @endif>
                                    @error('scheduled_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if(config('features.advanced_notifications'))
                        <!-- Target Types Selection -->
                        <div class="form-group" id="target_types_div" style="display: none;">
                            <label for="target_types">{{ trans('main_trans.Select_types') }}</label>
                            <select class="form-control select2 @error('target_ids') is-invalid @enderror" id="target_types" name="target_ids[]" multiple>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}" {{ in_array($type->id, old('target_ids', [])) ? 'selected' : '' }}>{{ $type->name }}</option>
                                @endforeach
                            </select>
                            @error('target_ids')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Target Students Selection -->
                        <div class="form-group" id="target_students_div" style="display: none;">
                            <label for="target_students">{{ trans('main_trans.Select_students') }}</label>
                            <select class="form-control select2 @error('target_ids') is-invalid @enderror" id="target_students" name="target_ids[]" multiple>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ in_array($student->id, old('target_ids', [])) ? 'selected' : '' }}>{{ $student->first_name }} {{ $student->last_name }}</option>
                                @endforeach
                            </select>
                            @error('target_ids')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <div class="form-group">
                            <button type="submit" id="submit-notification-btn" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> {{ trans('main_trans.Send_notification') }}
                            </button>
                            <a href="{{ route('notifications.index') }}" class="btn btn-secondary" id="cancel-notification-btn">{{ trans('main_trans.Cancel') }}</a>
                            <small class="d-block text-muted mt-2" id="sending-hint" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i> {{ trans('main_trans.Notification_sending_please_wait') }}
                            </small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- row closed -->
    </div>
    </div>

@endsection
@section('js')
    <!--Internal  Datepicker js -->
    <script src="{{ URL::asset('assets/plugins/jquery-ui/ui/widgets/datepicker.js') }}"></script>
    <!--Internal  Form-elements js-->
    <script src="{{ URL::asset('assets/js/form-elements.js') }}"></script>
    <!--Internal  Notify js -->
    <script src="{{ URL::asset('assets/plugins/notify/js/notifIt.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/notify/js/notifit-custom.js') }}"></script>
    <!-- Select2 js -->
    <script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2({
                placeholder: '{{ trans('main_trans.Select') }}',
                allowClear: true
            });

            @if(config('features.advanced_notifications'))
            // Handle target type change
            $('#target_type').change(function() {
                var targetType = $(this).val();

                // Hide all target selection divs
                $('#target_types_div, #target_students_div').hide();

                // Show relevant div based on selection
                if (targetType === 'type') {
                    $('#target_types_div').show();
                } else if (targetType === 'student') {
                    $('#target_students_div').show();
                }
            });

            // Trigger change event on page load if there's a value
            $('#target_type').trigger('change');
            @endif

            $('form').on('submit', function() {
                var $btn = $('#submit-notification-btn');
                if ($btn.prop('disabled')) {
                    return false;
                }
                $btn.prop('disabled', true)
                    .html('<i class="fas fa-spinner fa-spin"></i> {{ trans('main_trans.Notification_sending_please_wait') }}');
                $('#cancel-notification-btn').addClass('disabled').css('pointer-events', 'none');
                $('#sending-hint').show();
            });
        });
    </script>
@endsection
