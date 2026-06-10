<!-- Title -->
<title> @yield('title') </title>
<!-- Favicon -->
<link rel="icon" href="{{URL::asset('assets/img/brand/logo.png')}}" type="image/x-icon"/>
<!-- Icons css -->
@if (App::getLocale() == 'ar')
<link href="{{URL::asset('assets/css-rtl/icons.css')}}" rel="stylesheet">
@else
<link href="{{URL::asset('assets/css/icons.css')}}" rel="stylesheet">
@endif

<!--  Custom Scroll bar-->
<link href="{{URL::asset('assets/plugins/mscrollbar/jquery.mCustomScrollbar.css')}}" rel="stylesheet"/>
<!-- Modern Scrollbar Styles -->
<link href="{{URL::asset('assets/css/modern-scrollbar.css')}}" rel="stylesheet"/>
<!--  Sidebar css -->
<link href="{{URL::asset('assets/plugins/sidebar/sidebar.css')}}" rel="stylesheet">
<!-- Sidemenu css -->
@if (App::getLocale() == 'ar')
    <link rel="stylesheet" href="{{URL::asset('assets/css-rtl/sidemenu.css')}}">
@else
    <link rel="stylesheet" href="{{URL::asset('assets/css/sidemenu.css')}}">
@endif
@yield('css')
@if (App::getLocale() == 'ar')
<!--- Style css -->
<link href="{{URL::asset('assets/css-rtl/style.css')}}" rel="stylesheet">
 <!--- Dark-mode css -->
<link href="{{URL::asset('assets/css-rtl/style-dark.css')}}" rel="stylesheet">
<!---Skinmodes css-->
<link href="{{URL::asset('assets/css-rtl/skin-modes.css')}}" rel="stylesheet">
@else
<!--- Style css -->
<link href="{{URL::asset('assets/css/style.css')}}" rel="stylesheet">
 <!--- Dark-mode css -->
<link href="{{URL::asset('assets/css/style-dark.css')}}" rel="stylesheet">
<!---Skinmodes css-->
<link href="{{URL::asset('assets/css/skin-modes.css')}}" rel="stylesheet">
@endif
