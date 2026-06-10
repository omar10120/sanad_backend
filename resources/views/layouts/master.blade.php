<!DOCTYPE html>
<html lang={{App::getLocale()}}>
	<head>
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="Description" content="Bootstrap Responsive Admin Web Dashboard For Sanad Team">
		<meta name="Author" content="Time of Code Company">
		<meta name="Keywords" content="Sanad,Sanad al-Taleb,Sanadaltaleb,Time of Code"/>
		@include('layouts.head')
	</head>

	<body class="main-body app sidebar-mini">
		<!-- Loader -->
		<div id="global-loader">
			<img src="{{URL::asset('assets/img/loader.svg')}}" class="loader-img" alt="Loader">
		</div>
		<!-- /Loader -->
		@include('layouts.main-sidebar')
		<!-- main-content -->
		<div class="main-content app-content">
			@include('layouts.main-header')
			<!-- container -->
			<div class="container-fluid">
				@yield('page-header')
				@yield('content')
				{{--
                include('layouts.sidebar')
				include('layouts.models') --}}
            	@include('layouts.footer')
				@include('components.pro-modal')
				@include('layouts.footer-scripts')
	</body>
</html>
