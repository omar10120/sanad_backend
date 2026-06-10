@extends('layouts.master')
@section('title')
{{ trans('main_trans.Mohamad_alaa_alshahrour') }}
@endsection
@section('css')
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="my-auto">
						<div class="d-flex">
							<h4 class="content-title mb-0 my-auto">{{ trans('main_trans.Developer') }}</h4>
						</div>
					</div>
				</div>
				<!-- breadcrumb -->
@endsection
@section('content')

@include('components.flash-messages')

				<!-- row -->
				<div class="row row-sm">
					<div class="col-lg-4">
						<div class="card mg-b-20">
							<div class="card-body">
								<div class="pl-0">
									<div class="main-profile-overview">
										<div class="main-img-user profile-user">
											<img alt="" src="{{URL::asset('assets/img/media/alaa.jpg')}}">
										</div>
										<div class="d-flex justify-content-between mg-b-20">
											<div>
												<h5 class="main-profile-name">{{ trans('main_trans.Mohamad_alaa_alshahrour') }}</h5>
												<p class="main-profile-name-text">{{ trans('main_trans.Informatics_engineer') }}</p>
											</div>
										</div>
										<hr class="mg-y-30">
										<label class="main-content-label tx-13 mg-b-20">{{ trans('main_trans.Social') }}</label>
										<div class="main-profile-social-list">
											<div class="media">
												<div class="media-icon bg-danger-transparent text-danger">
													<i class="icon ion-logo-github"></i>
												</div>
												<div class="media-body">
													                                <span>{{ trans('main_trans.Github') }}</span> <a href="https://www.github.com/AlaaAlshahrour">github.com/AlaaAlshahrour</a>
												</div>
											</div>
											<div class="media">
												<div class="media-icon bg-primary-transparent text-primary">
													<i class="icon ion-logo-facebook"></i>
												</div>
												<div class="media-body">
													                                <span>{{ trans('main_trans.Facebook') }}</span> <a href="https://www.facebook.com/Mohamad.Alaa.Alshahrour">facebook.com/Mohamad.Alaa.Alshahrour</a>
												</div>
											</div>
											<div class="media">
												<div class="media-icon bg-info-transparent text-info">
													<i class="icon ion-logo-linkedin"></i>
												</div>
												<div class="media-body">
													                                <span>{{ trans('main_trans.Linkedin') }}</span> <a href="https://www.linkedin.com/in/mohamad-alaa-alshahrour">linkedin.com/in/mohamad-alaa-alshahrour</a>
												</div>
											</div>
											<div class="media">
												<div class="media-icon bg-success-transparent text-success">
													<i class="icon ion-logo-whatsapp"></i>
												</div>
												<div class="media-body">
													                                <span>{{ trans('main_trans.Whatsapp') }}</span> <a href="">+963 964 630 090</a>
												</div>
											</div>
										</div>
										<hr class="mg-y-30">
										<h6>{{ trans('main_trans.Skills') }}</h6>
										<div class="skill-bar mb-4 clearfix mt-3">
											                                <span>{{ trans('main_trans.Laravel') }}</span>
											<div class="progress mt-2">
												<div class="progress-bar bg-primary-gradient" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 85%"></div>
											</div>
										</div>
										<!--skill bar-->
										<div class="skill-bar mb-4 clearfix">
											                                <span>{{ trans('main_trans.Flutter') }}</span>
											<div class="progress mt-2">
												<div class="progress-bar bg-danger-gradient" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 82%"></div>
											</div>
										</div>
										<!--skill bar-->
										<div class="skill-bar mb-4 clearfix">
											                                <span>{{ trans('main_trans.Java') }}</span>
											<div class="progress mt-2">
												<div class="progress-bar bg-success-gradient" role="progressbar" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100" style="width: 75%"></div>
											</div>
										</div>
										<!--skill bar-->
									</div><!-- main-profile-overview -->
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-8">
						<div class="row row-sm">
							<div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
								<div class="card ">
									<div class="card-body">
										<div class="counter-status d-flex md-mb-0">
											<div class="counter-icon bg-primary-transparent">
												<i class="icon-star text-primary"></i>
											</div>
                                            <div class="mr-auto">
												<h5 class="tx-13"></h5>
											</div>
											<div class="mr-auto">
												<h5 class="tx-13">{{ trans('main_trans.Quality') }}</h5>
												<h2 class="mb-0 tx-22 mb-1 mt-1">{{ trans('main_trans.High') }}</h2>
                                                {{-- <p class="text-muted mb-0 tx-11"><i class="si si-arrow-up-circle text-success mr-1"></i>increase</p> --}}
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
								<div class="card ">
									<div class="card-body">
										<div class="counter-status d-flex md-mb-0">
											<div class="counter-icon bg-danger-transparent">
												<i class="icon-paypal text-danger"></i>
											</div>
                                            <div class="mr-auto">
												<h5 class="tx-13"></h5>
											</div>
											<div class="mr-auto">
												<h5 class="tx-13">{{ trans('main_trans.Work') }}</h5>
												<h2 class="mb-0 tx-20 mb-1 mt-1">{{ trans('main_trans.Professional') }}</h2>
                                                {{-- <p class="text-muted mb-0 tx-11"><i class="si si-arrow-up-circle text-success mr-1"></i>increase</p> --}}
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
								<div class="card ">
									<div class="card-body">
										<div class="counter-status d-flex">
											<div class="counter-icon bg-success-transparent">
												<i class="icon-rocket text-success"></i>
											</div>
                                            <div class="mr-auto">
												<h5 class="tx-13"></h5>
											</div>
											<div class="mr-auto">
												<h5 class="tx-13">{{ trans('main_trans.Performance') }}</h5>
												<h2 class="mb-0 tx-22 mb-1 mt-1">{{ trans('main_trans.Fast') }}</h2>
												{{-- <p class="text-muted mb-0 tx-11"><i class="si si-arrow-up-circle text-success mr-1"></i>increase</p> --}}
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="card">
							<div class="card-body">
								<div class="border-left border-bottom border-right border-top p-4">
									<div>
										<h4 class="tx-20 text-uppercase mb-3">{{ trans('main_trans.BIO') }}</h4>
										<p class="m-b-5">{{ trans('main_trans.BIO_content') }}</p>
										<div class="m-t-30">
											<h4 class="tx-15 text-uppercase mt-3">{{ trans('main_trans.Experience') }}</h4>
											<div class=" p-t-10">
												<h5 class="text-primary m-b-5 tx-14">{{ trans('main_trans.Web_developer') }}</h5>
												<p><b>2022-2024</b></p>
												<p class="text-muted tx-13 m-b-0">{{ trans('main_trans.Web_developer_content') }}</p>
											</div>
											<hr>
										</div>
									</div>
									<div class="tab-pane" id="profile">
										<div class="row">
											<div class="col-sm-7">
												<div class="border p-1 card thumb">
													<a href="#" class="image-popup" title="{{ trans('main_trans.Programming_contest') }}"> <img src="{{URL::asset('assets/img/media/prog_contest.jpg')}}" class="thumb-img" alt="work-thumbnail"> </a>
													<h4 class="text-center tx-14 mt-3 mb-0">{{ trans('main_trans.Programming_contest') }}</h4>
													<div class="ga-border"></div>
													<p class="text-muted text-center"><small>2023</small></p>
												</div>
											</div>
											<div class="col-sm-5">
												<div class=" border p-1 card thumb">
													<a href="#" class="image-popup" title="EnElectro"> <img src="{{URL::asset('assets/img/media/login1.jpg')}}" class="thumb-img" alt="work-thumbnail"> </a>
													                            <h4 class="text-center tx-14 mt-3 mb-0">{{ trans('main_trans.EnElectro') }}</h4>
													<div class="ga-border"></div>
													<p class="text-muted text-center"><small>2024</small></p>
												</div>
											</div>
										</div>
									</div>
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
@endsection
