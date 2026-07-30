<!-- main-sidebar -->
		<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
		<aside class="app-sidebar sidebar-scroll">
			<div class="main-sidebar-header active">
				<a class="desktop-logo logo-light active" href="{{ url('/') }}"><img src="{{URL::asset('assets/img/brand/sanad.jpg')}}" class="main-logo" alt="logo"></a>
				<a class="logo-icon mobile-logo icon-light active" href="{{ url('/') }}"><img src="{{URL::asset('assets/img/brand/logo.png')}}" class="logo-icon" alt="logo"></a>
			</div>
			<div class="main-sidemenu">
				<div class="app-sidebar__user clearfix">
					<div class="dropdown user-pro-body">
						<div class="">
                            @if(Auth::user()->photo == null)
                                <img alt="user-img" class="avatar avatar-xl brround" src="{{URL::asset('assets/image/sanad.jpg')}}">
                            @else
                                <img alt="user-img" class="avatar avatar-xl brround" src="{{URL::asset('assets/image/Users/'.Auth::user()->id.'/'.Auth::user()->photo)}}">
                            @endif
                            <span class="avatar-status profile-status bg-green"></span>
						</div>
						<div class="user-info">
							<h4 class="font-weight-semibold mt-3 mb-0">{{Auth::user()->name}}</h4>
							<span class="mb-0 text-muted">{{Auth::user()->email}}</span>
						</div>
					</div>
				</div>
				<ul class="side-menu">
					<li class="side-item side-item-category">{{ trans('main_trans.Main') }}</li>
					<li class="slide">
						<a class="side-menu__item" href="{{ url('/' . $page='dashboard') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" ><path d="M0 0h24v24H0V0z" fill="none"/><path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/><path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/></svg><span class="side-menu__label">{{ trans('main_trans.Home') }}</span></a>
					</li>
					<li class="side-item side-item-category">{{ trans('main_trans.General') }}</li>

                    @can('Type-show')
                        <li class="slide">
                            <a class="side-menu__item" href="{{ url('/' . $page='type') }}"><svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" class="side-menu__icon" viewBox="0 0 24 24" ><g><rect fill="none"/></g><g><g/><g><path d="M21,5c-1.11-0.35-2.33-0.5-3.5-0.5c-1.95,0-4.05,0.4-5.5,1.5c-1.45-1.1-3.55-1.5-5.5-1.5S2.45,4.9,1,6v14.65 c0,0.25,0.25,0.5,0.5,0.5c0.1,0,0.15-0.05,0.25-0.05C3.1,20.45,5.05,20,6.5,20c1.95,0,4.05,0.4,5.5,1.5c1.35-0.85,3.8-1.5,5.5-1.5 c1.65,0,3.35,0.3,4.75,1.05c0.1,0.05,0.15,0.05,0.25,0.05c0.25,0,0.5-0.25,0.5-0.5V6C22.4,5.55,21.75,5.25,21,5z M3,18.5V7 c1.1-0.35,2.3-0.5,3.5-0.5c1.34,0,3.13,0.41,4.5,0.99v11.5C9.63,18.41,7.84,18,6.5,18C5.3,18,4.1,18.15,3,18.5z M21,18.5 c-1.1-0.35-2.3-0.5-3.5-0.5c-1.34,0-3.13,0.41-4.5,0.99V7.49c1.37-0.59,3.16-0.99,4.5-0.99c1.2,0,2.4,0.15,3.5,0.5V18.5z"/><path d="M11,7.49C9.63,6.91,7.84,6.5,6.5,6.5C5.3,6.5,4.1,6.65,3,7v11.5C4.1,18.15,5.3,18,6.5,18 c1.34,0,3.13,0.41,4.5,0.99V7.49z" opacity=".3"/></g><g><path d="M17.5,10.5c0.88,0,1.73,0.09,2.5,0.26V9.24C19.21,9.09,18.36,9,17.5,9c-1.28,0-2.46,0.16-3.5,0.47v1.57 C14.99,10.69,16.18,10.5,17.5,10.5z"/><path d="M17.5,13.16c0.88,0,1.73,0.09,2.5,0.26V11.9c-0.79-0.15-1.64-0.24-2.5-0.24c-1.28,0-2.46,0.16-3.5,0.47v1.57 C14.99,13.36,16.18,13.16,17.5,13.16z"/><path d="M17.5,15.83c0.88,0,1.73,0.09,2.5,0.26v-1.52c-0.79-0.15-1.64-0.24-2.5-0.24c-1.28,0-2.46,0.16-3.5,0.47v1.57 C14.99,16.02,16.18,15.83,17.5,15.83z"/></g></g></svg><span class="side-menu__label">{{ trans('main_trans.Study') }}</span></a>
                        </li>
                    @endcan

                 

                    @can('Question-show')
                        <li class="slide">
                            <a class="side-menu__item" href="{{ url('/' . $page='question-type') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M13 4H6v16h12V9h-5V4zm3 14H8v-2h8v2zm0-6v2H8v-2h8z" opacity=".3"/><path d="M8 16h8v2H8zm0-4h8v2H8zm6-10H6c-1.1 0-2 .9-2 2v16c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/></svg><span class="side-menu__label">{{ trans('main_trans.Question_types') }}</span></a>
                        </li>
                        <li class="slide">
                            <a class="side-menu__item" href="{{ url('/' . $page='question-reports') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M3 5h18v14H3z" opacity=".3"/><path d="M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM3 19V5h18v14H3zm4-9h10v2H7v-2zm0 4h7v2H7v-2zM7 7h10v2H7V7z"/></svg><span class="side-menu__label">{{ trans('main_trans.Question_reports') }}</span></a>
                        </li>
                    @endcan
                    <li class="side-item side-item-category">{{ trans('main_trans.Coures') }}</li>

                    
                        <li class="slide">
                            <a class="side-menu__item" href="{{ route('course-type.index') }}"><svg xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24" class="side-menu__icon" viewBox="0 0 24 24" ><g><rect fill="none"/></g><g><g/><g><path d="M21,5c-1.11-0.35-2.33-0.5-3.5-0.5c-1.95,0-4.05,0.4-5.5,1.5c-1.45-1.1-3.55-1.5-5.5-1.5S2.45,4.9,1,6v14.65 c0,0.25,0.25,0.5,0.5,0.5c0.1,0,0.15-0.05,0.25-0.05C3.1,20.45,5.05,20,6.5,20c1.95,0,4.05,0.4,5.5,1.5c1.35-0.85,3.8-1.5,5.5-1.5 c1.65,0,3.35,0.3,4.75,1.05c0.1,0.05,0.15,0.05,0.25,0.05c0.25,0,0.5-0.25,0.5-0.5V6C22.4,5.55,21.75,5.25,21,5z M3,18.5V7 c1.1-0.35,2.3-0.5,3.5-0.5c1.34,0,3.13,0.41,4.5,0.99v11.5C9.63,18.41,7.84,18,6.5,18C5.3,18,4.1,18.15,3,18.5z M21,18.5 c-1.1-0.35-2.3-0.5-3.5-0.5c-1.34,0-3.13,0.41-4.5,0.99V7.49c1.37-0.59,3.16-0.99,4.5-0.99c1.2,0,2.4,0.15,3.5,0.5V18.5z"/><path d="M11,7.49C9.63,6.91,7.84,6.5,6.5,6.5C5.3,6.5,4.1,6.65,3,7v11.5C4.1,18.15,5.3,18,6.5,18 c1.34,0,3.13,0.41,4.5,0.99V7.49z" opacity=".3"/></g><g><path d="M17.5,10.5c0.88,0,1.73,0.09,2.5,0.26V9.24C19.21,9.09,18.36,9,17.5,9c-1.28,0-2.46,0.16-3.5,0.47v1.57 C14.99,10.69,16.18,10.5,17.5,10.5z"/><path d="M17.5,13.16c0.88,0,1.73,0.09,2.5,0.26V11.9c-0.79-0.15-1.64-0.24-2.5-0.24c-1.28,0-2.46,0.16-3.5,0.47v1.57 C14.99,13.36,16.18,13.16,17.5,13.16z"/><path d="M17.5,15.83c0.88,0,1.73,0.09,2.5,0.26v-1.52c-0.79-0.15-1.64-0.24-2.5-0.24c-1.28,0-2.46,0.16-3.5,0.47v1.57 C14.99,16.02,16.18,15.83,17.5,15.83z"/></g></g></svg><span class="side-menu__label">{{ trans('main_trans.subject_videos') }}</span></a>
                        </li>
                    

                    <li class="side-item side-item-category">{{ trans('main_trans.Users') }}</li>
                    <li class="slide">
						<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg><span class="side-menu__label">{{ trans('main_trans.Users') }}</span><i class="angle fe fe-chevron-down"></i></a>
                        <ul class="slide-menu">
                            @can('User-show')
                                <li><a class="slide-item" href="{{ url('/' . $page='users') }}">{{ trans('main_trans.Show_users') }}</a></li>
                            @endcan
                            @can('User-show')
                                <li><a class="slide-item" href="{{ url('/' . $page='student') }}">{{ trans('main_trans.Students_2024_2025') }}</a></li>
                            @endcan
                            @can('User-show')
                                <li><a class="slide-item" href="{{ route('student.current-academic-year') }}">{{ trans('main_trans.Students_2025_2026') }}</a></li>
                            @endcan
                            @can('Role-show')
                                <li><a class="slide-item" href="{{ url('/' . $page='roles') }}">{{ trans('main_trans.Show_roles') }}</a></li>
                            @endcan
							<li><a class="slide-item" href="{{ url('/' . $page='profile') }}">{{ trans('main_trans.Profile') }}</a></li>
						</ul>
					</li>

                    @canany(['Notification-show','Notification-add'])
                    <li class="slide">
{{--                        @if(config('features.advanced_notifications'))--}}
                            <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg><span class="side-menu__label">{{ trans('main_trans.Notifications') }}</span><i class="angle fe fe-chevron-down"></i></a>
                            <ul class="slide-menu">
                                @can('Notification-show')
                                    <li><a class="slide-item" href="{{ route('notifications.index') }}">{{ trans('main_trans.All_notifications') }}</a></li>
                                @endcan
                                @can('Notification-add')
                                    <li><a class="slide-item" href="{{ route('notifications.create') }}">{{ trans('main_trans.Send_notification') }}</a></li>
                                @endcan
                            </ul>
{{--                        @else--}}
{{--                            <a class="side-menu__item" href="#" onclick="showProModal(event)"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg><span class="side-menu__label">{{ trans('main_trans.Notifications') }}</span> <i class="fas fa-crown text-warning ml-1"></i></a>--}}
{{--                        @endif--}}
                    </li>
                    @endcanany

                    @canany(['Code-show','Code-add'])
                    <li class="slide">
                        <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><path d="M9 9h.01"/><path d="M15 9h.01"/><path d="M9 15h.01"/><path d="M15 15h.01"/><path d="M12 12h.01"/></svg><span class="side-menu__label">{{ trans('main_trans.Codes') }}</span><i class="angle fe fe-chevron-down"></i></a>
                        <ul class="slide-menu">
                            @can('Code-show')
                                <li><a class="slide-item" href="{{ url('/' . $page='code-package') }}">{{ trans('main_trans.Code_packages') }}</a></li>
                            @endcan
                        </ul>
                    </li>
                    @endcanany

                    @canany(['Phone-verification-codes'])
                        <li class="slide">
                            @if(config('features.phone_verification_codes'))
                                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><path d="M9 9h.01"/><path d="M15 9h.01"/><path d="M9 15h.01"/><path d="M15 15h.01"/><path d="M12 12h.01"/></svg><span class="side-menu__label">{{ trans('main_trans.phone_verification_codes') }}</span><i class="angle fe fe-chevron-down"></i></a>
                                <ul class="slide-menu">
                                    @can('Phone-verification-codes')
                                        <li><a class="slide-item" href="{{ route('phone-verification-codes.index') }}">{{ trans('main_trans.phone_verification_codes') }}</a></li>
                                    @endcan
                                </ul>
                            @else
                                <a class="side-menu__item" href="#" onclick="showProModal(event)"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><path d="M9 9h.01"/><path d="M15 9h.01"/><path d="M9 15h.01"/><path d="M15 15h.01"/><path d="M12 12h.01"/></svg><span class="side-menu__label">{{ trans('main_trans.phone_verification_codes') }}</span> <i class="fas fa-crown text-warning ml-1"></i></a>
                            @endif
                        </li>
                    @endcanany

                    @canany(['Role-show','Role-add'])
                        <li class="slide">
                            @if(config('features.app_updates'))
                                <a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/><line x1="12" y1="18" x2="12.01" y2="18"/><line x1="9" y1="6" x2="15" y2="6"/></svg><span class="side-menu__label">{{ trans('main_trans.App_updates') }}</span><i class="angle fe fe-chevron-down"></i></a>
                                <ul class="slide-menu">
                                    @can('Role-show')
                                        <li><a class="slide-item" href="{{ url('/' . $page='app-update') }}">{{ trans('main_trans.App_updates') }}</a></li>
                                    @endcan
                                </ul>
                            @else
                                <a class="side-menu__item" href="#" onclick="showProModal(event)"><svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/><line x1="12" y1="18" x2="12.01" y2="18"/><line x1="9" y1="6" x2="15" y2="6"/></svg><span class="side-menu__label">{{ trans('main_trans.App_updates') }}</span> <i class="fas fa-crown text-warning ml-1"></i></a>
                            @endif
                        </li>
                    @endcanany

				</ul>
			</div>
		</aside>
<!-- main-sidebar -->
