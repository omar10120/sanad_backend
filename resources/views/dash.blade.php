@extends('layouts.master')
@section('title')
    {{ trans('main_trans.Home') }}
@endsection
@section('css')
    <!--  Owl-carousel css-->
    <link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
    <!-- Maps css -->
    <link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
    <!-- Chart.js css -->
    <link href="{{URL::asset('assets/plugins/chart.js/Chart.bundle.min.js')}}" rel="stylesheet">
    <style>
        .stat-card {
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.2s ease-in-out;
        }
        .stat-card:hover {
            transform: translateY(-2px);
        }
        .bg-gradient-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
        }
        .bg-gradient-success {
            background: linear-gradient(45deg, #28a745, #1e7e34);
        }
        .bg-gradient-info {
            background: linear-gradient(45deg, #17a2b8, #117a8b);
        }
        .bg-gradient-warning {
            background: linear-gradient(45deg, #ffc107, #e0a800);
        }
        .chart-container {
            position: relative;
            height: 300px;
        }
        .recent-activity-item {
            border-left: 3px solid #007bff;
            padding-left: 15px;
            margin-bottom: 15px;
        }
        .activity-time {
            font-size: 0.85rem;
            color: #6c757d;
        }
    </style>
@endsection
@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="left-content">
            <div>
                <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">{{ trans('main_trans.Dashboard') }}</h2>
                <p class="mg-b-0">{{ trans('main_trans.Welcome_back') }}</p>
            </div>
        </div>
        <div class="right-content">
            <div class="btn-group mr-2">
                <button type="button" class="btn btn-sm academic-year-btn {{ ($academicYear ?? '2025-2026') == '2024-2025' ? 'btn-primary' : 'btn-outline-primary' }}"
                        data-year="2024-2025">
                    {{ trans('main_trans.Academic_Year_2024_2025') }}
                </button>
                <button type="button" class="btn btn-sm academic-year-btn {{ ($academicYear ?? '2025-2026') == '2025-2026' ? 'btn-primary' : 'btn-outline-primary' }}"
                        data-year="2025-2026">
                    {{ trans('main_trans.Academic_Year_2025_2026') }}
                </button>
            </div>
            <div class="btn-group">
                <a href="{{ route('dashboard.clear-cache') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-sync-alt"></i> {{ trans('main_trans.Refresh') }}
                </a>
                {{-- <a href="{{ route('dashboard.export-statistics') }}" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-download"></i> {{ trans('main_trans.Export') }}
                </a> --}}
            </div>
        </div>
    </div>
    <!-- /breadcrumb -->
@endsection
@section('content')

    @include('components.flash-messages')

    @can('Graphs')
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card bg-gradient-primary mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $statistics['total_subjects'] ?? 0 }}</h4>
                                <p class="mb-0">{{ trans('main_trans.Total_Subjects') }}</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-book fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card bg-gradient-success mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $statistics['total_questions'] ?? 0 }}</h4>
                                <p class="mb-0">{{ trans('main_trans.Total_Questions') }}</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-question-circle fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card bg-gradient-info mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $statistics['total_students'] ?? 0 }}</h4>
                                <p class="mb-0">{{ trans('main_trans.Total_Students') }}</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-users fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stat-card bg-gradient-warning mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
    {{--                        <div>--}}
    {{--                            <h4 class="mb-0">{{ $statistics['total_enrollments'] ?? 0 }}</h4>--}}
    {{--                            <p class="mb-0">{{ trans('main_trans.Total_Enrollments') }}</p>--}}
    {{--                        </div>--}}
                            <div>
                                <h4 class="mb-0">{{ $statistics['total_lessons'] ?? 0 }}</h4>
                                <p class="mb-0">{{ trans('main_trans.Total_Lessons') }}</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-graduation-cap fa-2x opacity-75"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">{{ trans('main_trans.Student_Distribution_by_Cities') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="cityChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">{{ trans('main_trans.Student_Distribution_by_Types') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="typeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subject Statistics -->
        <div class="row mb-4">
            <div class="col-xl-8">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">{{ trans('main_trans.Subscribers_per_Subject') }}</h6>
                            </div>
                    <div class="card-body" id="subscribers-per-subject">
                        @if(isset($statistics['subscribers_per_subject']) && count($statistics['subscribers_per_subject']) > 0)
                            @foreach(array_slice($statistics['subscribers_per_subject'], 0, 10) as $subject)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>{{ $subject['name'] }}</span>
                                    <span class="badge badge-success">{{ $subject['subscribers_count'] }}</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">{{ trans('main_trans.No_data_available') }}</p>
                        @endif
                    </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">{{ trans('main_trans.Lessons_per_Subject') }}</h6>
                            </div>
                            <div class="card-body">
                                @if(isset($statistics['lessons_per_subject']) && count($statistics['lessons_per_subject']) > 0)
                                    @foreach(array_slice($statistics['lessons_per_subject'], 0, 10) as $subject)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>{{ $subject['name'] }}</span>
                                            <span class="badge badge-info">{{ $subject['lessons_count'] }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted">{{ trans('main_trans.No_data_available') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">{{ trans('main_trans.Recent_Activity') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-2">
                                <h6 class="text-success">{{ trans('main_trans.New_Students_1_day') }}</h6>
                                <h4 class="text-success" id="recent-students-1">{{ $statistics['recent_students_1'] ?? 0 }}</h4>
                            </div>
                            <div class="col-md-2">
                                <h6 class="text-success">{{ trans('main_trans.New_Students_7_days') }}</h6>
                                <h4 class="text-success" id="recent-students-7">{{ $statistics['recent_students_7'] ?? 0 }}</h4>
                            </div>
                            <div class="col-md-2">
                                <h6 class="text-success">{{ trans('main_trans.New_Students_30_days') }}</h6>
                                <h4 class="text-success" id="recent-students-30">{{ $statistics['recent_students_30'] ?? 0 }}</h4>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-warning">{{ trans('main_trans.Pending_Reports') }}</h6>
                                <h4 class="text-warning">{{ $statistics['pending_reports'] ?? 0 }}</h4>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-info">{{ trans('main_trans.Resolved_Reports') }}</h6>
                                <h4 class="text-info">{{ $statistics['resolved_reports'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-xl-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">{{ trans('main_trans.Recent_Reports') }}</h6>
                    </div>
                    <div class="card-body">
                        @if(isset($statistics['recent_reports']) && count($statistics['recent_reports']) > 0)
                            @foreach(array_slice($statistics['recent_reports'], 0, 5) as $report)
                                <div class="recent-activity-item">
                                    <div class="d-flex justify-content-between">
                                        <span class="font-weight-bold">{{ $report['student']['first_name'] ?? 'Unknown' }} {{ $report['student']['last_name'] ?? '' }}</span>
                                        <span class="badge badge-{{ $report['status'] === 'pending' ? 'warning' : 'success' }}">{{ $report['status'] }}</span>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($report['description'] ?? 'No description', 50) }}</p>
                                    <small class="activity-time">{{ \Carbon\Carbon::parse($report['created_at'])->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">{{ trans('main_trans.No_recent_reports') }}</p>
                        @endif
                    </div>
                </div>
            </div>

        </div>

        <!-- Recent Activity -->
        <div class="row">
            <div class="col-xl-8">

            </div>
        </div>
    @endcan

    </div>
    </div>
    <!-- Container closed -->
@endsection
@section('js')
    <!--Internal Chart.bundle js -->
    <script src="{{URL::asset('assets/plugins/chart.js/Chart.bundle.min.js')}}"></script>
    <script>
        let cityChart = null;
        let typeChart = null;
        let currentAcademicYear = '{{ $academicYear ?? "2025-2026" }}';

        // Initialize charts
        function initCharts(statistics) {
            // Destroy existing charts if they exist
            if (cityChart) {
                cityChart.destroy();
            }
            if (typeChart) {
                typeChart.destroy();
            }

            // City Distribution Chart
            const cityData = statistics.students_by_city || [];
            if (cityData.length > 0) {
                cityChart = new Chart(document.getElementById('cityChart'), {
                    type: 'doughnut',
                    data: {
                        labels: cityData.map(item => item.city_name),
                        datasets: [{
                            data: cityData.map(item => item.count),
                            backgroundColor: [
                                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                                '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384',
                                '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'
                            ],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });
            }

            // Type Distribution Chart
            const typeData = statistics.students_by_type || [];
            if (typeData.length > 0) {
                typeChart = new Chart(document.getElementById('typeChart'), {
                    type: 'bar',
                    data: {
                        labels: typeData.map(item => item.name),
                        datasets: [{
                            label: '{{ trans("main_trans.Students") }}',
                            data: typeData.map(item => item.count),
                            backgroundColor: '#36A2EB',
                            borderColor: '#36A2EB',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        }

        // Initialize charts on page load
        initCharts(@json($statistics ?? []));

        // Academic year button click handler
        document.querySelectorAll('.academic-year-btn').forEach(button => {
            button.addEventListener('click', function() {
                const selectedYear = this.getAttribute('data-year');

                // Update button states
                document.querySelectorAll('.academic-year-btn').forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-primary');
                });
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');

                // Show loading state
                const loadingOverlay = document.createElement('div');
                loadingOverlay.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
                loadingOverlay.style.cssText = 'background: rgba(0,0,0,0.5); z-index: 9999;';
                loadingOverlay.innerHTML = '<div class="spinner-border text-light" role="status"><span class="visually-hidden">Loading...</span></div>';
                document.body.appendChild(loadingOverlay);

                // Fetch new statistics
                fetch(`{{ route('dashboard.statistics') }}?academic_year=${selectedYear}`)
                    .then(response => response.json())
                    .then(data => {
                        // Update statistics cards
                        const totalSubjectsEl = document.querySelector('.stat-card.bg-gradient-primary h4');
                        const totalQuestionsEl = document.querySelector('.stat-card.bg-gradient-success h4');
                        const totalStudentsEl = document.querySelector('.stat-card.bg-gradient-info h4');
                        const totalLessonsEl = document.querySelector('.stat-card.bg-gradient-warning h4');

                        if (totalSubjectsEl) totalSubjectsEl.textContent = data.total_subjects || 0;
                        if (totalQuestionsEl) totalQuestionsEl.textContent = data.total_questions || 0;
                        if (totalStudentsEl) totalStudentsEl.textContent = data.total_students || 0;
                        if (totalLessonsEl) totalLessonsEl.textContent = data.total_lessons || 0;

                        // Update recent students
                        const recentStudents1El = document.getElementById('recent-students-1');
                        const recentStudents7El = document.getElementById('recent-students-7');
                        const recentStudents30El = document.getElementById('recent-students-30');

                        if (recentStudents1El) recentStudents1El.textContent = data.recent_students_1 || 0;
                        if (recentStudents7El) recentStudents7El.textContent = data.recent_students_7 || 0;
                        if (recentStudents30El) recentStudents30El.textContent = data.recent_students_30 || 0;

                        // Update subscribers per subject
                        const subscribersContainer = document.getElementById('subscribers-per-subject');
                        if (subscribersContainer && data.subscribers_per_subject) {
                            if (data.subscribers_per_subject.length > 0) {
                                const subscribersHtml = data.subscribers_per_subject.slice(0, 5).map(subject =>
                                    `<div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>${subject.name || ''}</span>
                                        <span class="badge badge-success">${subject.subscribers_count || 0}</span>
                                    </div>`
                                ).join('');
                                subscribersContainer.innerHTML = subscribersHtml;
                            } else {
                                subscribersContainer.innerHTML = '<p class="text-muted">{{ trans("main_trans.No_data_available") }}</p>';
                            }
                        }

                        // Update charts
                        initCharts(data);

                        // Update URL without reload
                        const url = new URL(window.location);
                        url.searchParams.set('academic_year', selectedYear);
                        window.history.pushState({}, '', url);

                        currentAcademicYear = selectedYear;
                    })
                    .catch(error => {
                        console.error('Error fetching statistics:', error);
                        alert('حدث خطأ أثناء تحميل الإحصائيات');
                    })
                    .finally(() => {
                        if (document.body.contains(loadingOverlay)) {
                            document.body.removeChild(loadingOverlay);
                        }
                    });
            });
        });
    </script>
@endsection
