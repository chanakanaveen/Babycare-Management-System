<!DOCTYPE html>
<html>
	<head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
		<!-- Basic Page Info -->
		<meta charset="utf-8" />
		<title>@yield('pagetitle')</title>

		<!-- Site favicon -->
		<link
			rel="apple-touch-icon"
			sizes="180x180"
			href="/back/vendors/images/apple-touch-icon.png"
		/>
		<link
			rel="icon"
			type="image/png"
			sizes="32x32"
			href="/back/vendors/images/favicon-32x32.png"
		/>
		<link
			rel="icon"
			type="image/png"
			sizes="16x16"
			href="/back/vendors/images/favicon-16x16.png"
		/>

		<!-- Mobile Specific Metas -->
		<meta
			name="viewport"
			content="width=device-width, initial-scale=1, maximum-scale=1"
		/>

		<!-- Google Font -->
		<link
			href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
			rel="stylesheet"
		/>
		<!-- CSS -->
		<link rel="stylesheet" type="text/css" href="/back/vendors/styles/core.css" />
		<link
			rel="stylesheet"
			type="text/css"
			href="/back/vendors/styles/icon-font.min.css"
		/>
		<link rel="stylesheet" type="text/css" href="/back/vendors/styles/style.css" />
		<link rel="stylesheet" type="text/css" href="/back/vendors/styles/custom.css" />

        <!-- switchery css -->
		<link
        rel="stylesheet"
        type="text/css"
        href="/back/src/plugins/switchery/switchery.min.css"
    />
    <!-- bootstrap-touchspin css -->
		<link
        rel="stylesheet"
        type="text/css"
        href="/back/src/plugins/bootstrap-touchspin/jquery.bootstrap-touchspin.css"
    />
    <!-- bootstrap-tagsinput css -->
		<link
        rel="stylesheet"
        type="text/css"
        href="/back/src/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css"
    />

		<!-- Google Tag Manager -->
		<script>
			(function (w, d, s, l, i) {
				w[l] = w[l] || [];
				w[l].push({ "gtm.start": new Date().getTime(), event: "gtm.js" });
				var f = d.getElementsByTagName(s)[0],
					j = d.createElement(s),
					dl = l != "dataLayer" ? "&l=" + l : "";
				j.async = true;
				j.src = "https://www.googletagmanager.com/gtm.js?id=" + i + dl;
				f.parentNode.insertBefore(j, f);
			})(window, document, "script", "dataLayer", "GTM-NXZMQSS");
		</script>
		<!-- End Google Tag Manager -->

        {{-- <link rel="stylesheet" href="/extra-assets/ijabo/ijabo.min.css"> --}}
        <link rel="stylesheet" href="/extra-assets/ijaboCropTool/ijaboCropTool.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
        @livewireStyles
        @stack('stylesheet')
	</head>
	<body>
		{{-- <div class="pre-loader">
			<div class="pre-loader-box">
				<div class="loader-logo">
					<img src="/back/vendors/images/deskapp-logo.svg" alt="" />
				</div>
				<div class="loader-progress" id="progress_div">
					<div class="bar" id="bar1"></div>
				</div>
				<div class="percent" id="percent1">0%</div>
				<div class="loading-text">Loading...</div>
			</div>
		</div> --}}

		<div class="header">
			<div class="header-left">
				<div class="menu-icon bi bi-list"></div>
				<div
					class="search-toggle-icon bi bi-search"
					data-toggle="header_search"
				></div>
				<div class="header-search">
					<form>
						<div class="form-group mb-0">
							<i class="dw dw-search2 search-icon"></i>
							<input
								type="text"
								class="form-control search-input"
								placeholder="Search Here"
							/>
							<div class="dropdown">
								<a
									class="dropdown-toggle no-arrow"
									href="#"
									role="button"
									data-toggle="dropdown"
								>
									<i class="ion-arrow-down-c"></i>
								</a>
								<div class="dropdown-menu dropdown-menu-right">
									<div class="form-group row">
										<label class="col-sm-12 col-md-2 col-form-label"
											>From</label
										>
										<div class="col-sm-12 col-md-10">
											<input
												class="form-control form-control-sm form-control-line"
												type="text"
											/>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-12 col-md-2 col-form-label">To</label>
										<div class="col-sm-12 col-md-10">
											<input
												class="form-control form-control-sm form-control-line"
												type="text"
											/>
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-12 col-md-2 col-form-label"
											>Subject</label
										>
										<div class="col-sm-12 col-md-10">
											<input
												class="form-control form-control-sm form-control-line"
												type="text"
											/>
										</div>
									</div>
									<div class="text-right">
										<button class="btn btn-primary">Search</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="header-right">
				<div class="dashboard-setting user-notification">
					<div class="dropdown">
						<a
							class="dropdown-toggle no-arrow"
							href="javascript:;"
							data-toggle="right-sidebar"
						>
							<i class="dw dw-settings2"></i>
						</a>
					</div>
				</div>
				@include('partials.notification-bell')

                {{-- <livewire::admin-seller-header-profile /> --}}
                @livewire('admin-seller-header-profile')

			</div>
		</div>

		@include('partials.notification-banner')

		<div class="right-sidebar">
			<div class="sidebar-title">
				<h3 class="weight-600 font-16 text-blue">
					Layout Settings
					<span class="btn-block font-weight-400 font-12"
						>User Interface Settings</span
					>
				</h3>
				<div class="close-sidebar" data-toggle="right-sidebar-close">
					<i class="icon-copy ion-close-round"></i>
				</div>
			</div>
			<div class="right-sidebar-body customscroll">
				<div class="right-sidebar-body-content">
					<h4 class="weight-600 font-18 pb-10">Header Background</h4>
					<div class="sidebar-btn-group pb-30 mb-10">
						<a
							href="javascript:void(0);"
							class="btn btn-outline-primary header-white active"
							>White</a
						>
						<a
							href="javascript:void(0);"
							class="btn btn-outline-primary header-dark"
							>Dark</a
						>
					</div>

					<h4 class="weight-600 font-18 pb-10">Sidebar Background</h4>
					<div class="sidebar-btn-group pb-30 mb-10">
						<a
							href="javascript:void(0);"
							class="btn btn-outline-primary sidebar-light"
							>White</a
						>
						<a
							href="javascript:void(0);"
							class="btn btn-outline-primary sidebar-dark active"
							>Dark</a
						>
					</div>

					<h4 class="weight-600 font-18 pb-10">Menu Dropdown Icon</h4>
					<div class="sidebar-radio-group pb-10 mb-10">
						<div class="custom-control custom-radio custom-control-inline">
							<input
								type="radio"
								id="sidebaricon-1"
								name="menu-dropdown-icon"
								class="custom-control-input"
								value="icon-style-1"
								checked=""
							/>
							<label class="custom-control-label" for="sidebaricon-1"
								><i class="fa fa-angle-down"></i
							></label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input
								type="radio"
								id="sidebaricon-2"
								name="menu-dropdown-icon"
								class="custom-control-input"
								value="icon-style-2"
							/>
							<label class="custom-control-label" for="sidebaricon-2"
								><i class="ion-plus-round"></i
							></label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input
								type="radio"
								id="sidebaricon-3"
								name="menu-dropdown-icon"
								class="custom-control-input"
								value="icon-style-3"
							/>
							<label class="custom-control-label" for="sidebaricon-3"
								><i class="fa fa-angle-double-right"></i
							></label>
						</div>
					</div>

					<h4 class="weight-600 font-18 pb-10">Menu List Icon</h4>
					<div class="sidebar-radio-group pb-30 mb-10">
						<div class="custom-control custom-radio custom-control-inline">
							<input
								type="radio"
								id="sidebariconlist-1"
								name="menu-list-icon"
								class="custom-control-input"
								value="icon-list-style-1"
								checked=""
							/>
							<label class="custom-control-label" for="sidebariconlist-1"
								><i class="ion-minus-round"></i
							></label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input
								type="radio"
								id="sidebariconlist-2"
								name="menu-list-icon"
								class="custom-control-input"
								value="icon-list-style-2"
							/>
							<label class="custom-control-label" for="sidebariconlist-2"
								><i class="fa fa-circle-o" aria-hidden="true"></i
							></label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input
								type="radio"
								id="sidebariconlist-3"
								name="menu-list-icon"
								class="custom-control-input"
								value="icon-list-style-3"
							/>
							<label class="custom-control-label" for="sidebariconlist-3"
								><i class="dw dw-check"></i
							></label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input
								type="radio"
								id="sidebariconlist-4"
								name="menu-list-icon"
								class="custom-control-input"
								value="icon-list-style-4"
								checked=""
							/>
							<label class="custom-control-label" for="sidebariconlist-4"
								><i class="icon-copy dw dw-next-2"></i
							></label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input
								type="radio"
								id="sidebariconlist-5"
								name="menu-list-icon"
								class="custom-control-input"
								value="icon-list-style-5"
							/>
							<label class="custom-control-label" for="sidebariconlist-5"
								><i class="dw dw-fast-forward-1"></i
							></label>
						</div>
						<div class="custom-control custom-radio custom-control-inline">
							<input
								type="radio"
								id="sidebariconlist-6"
								name="menu-list-icon"
								class="custom-control-input"
								value="icon-list-style-6"
							/>
							<label class="custom-control-label" for="sidebariconlist-6"
								><i class="dw dw-next"></i
							></label>
						</div>
					</div>

					<div class="reset-options pt-30 text-center">
						<button class="btn btn-danger" id="reset-settings">
							Reset Settings
						</button>
					</div>
				</div>
			</div>
		</div>

		<div class="left-side-bar">
			<div class="brand-logo">
				<a href="{{ route('home-page') }}">
					<img src="/back/vendors/images/logo-1.jpeg" alt="" class="dark-logo" />
					{{-- <img
						src="/back/vendors/images/deskapp-logo-white.svg"
						alt=""
						class="light-logo"
					/> --}}
				</a>
				<div class="close-sidebar" data-toggle="left-sidebar-close">
					<i class="ion-close-round"></i>
				</div>
			</div>
			<div class="menu-block customscroll">
				<div class="sidebar-menu">
					<ul id="accordion-menu">

                        @if (Route::is('moh.*') )
                            <li class="dropdown">
                                <a href="{{ route('moh.home') }}" class="dropdown-toggle no-arrow {{ route::is('moh.home')? 'active' : '' }}">
                                    <span class="micon bi bi-house-door"></span
                                    ><span class="mtext">Home</span>
                                </a>
                            </li>

						<li class="dropdown">
                                <a href="{{ route('moh.pending-midwives') }}" class="dropdown-toggle no-arrow {{ route::is('moh.pending-midwives')? 'active' : '' }}">
                                    <span class="micon bi bi-person-badge"></span
                                    ><span class="mtext">Midwife Details</span>
                                </a>
                            </li>

                            <li class="dropdown">
                                <a href="{{ route('moh.parents') }}" class="dropdown-toggle no-arrow {{ route::is('moh.parents')? 'active' : '' }}">
                                    <span class="micon bi bi-people"></span
                                    ><span class="mtext">Parents Details</span>
                                </a>
                            </li>

                            <li class="dropdown">
                                <a href="{{ route('moh.users') }}" class="dropdown-toggle no-arrow {{ route::is('moh.users')? 'active' : '' }}">
                                    <span class="micon bi bi-person"></span
                                    ><span class="mtext">Babies Details</span>
                                </a>
                            </li>

                            <li class="dropdown">
                                <a href="{{ route('moh.vaccines') }}" class="dropdown-toggle no-arrow {{ route::is('moh.vaccines')? 'active' : '' }}">
                                    <span class="micon bi bi-eyedropper"></span
                                    ><span class="mtext">Vaccinations</span>
                                </a>
                            </li>

                            <li class="dropdown">
                                <a href="{{ route('moh.notice') }}" class="dropdown-toggle no-arrow {{ route::is('moh.noties')? 'active' : '' }}">
                                    <span class="micon bi bi-bell"></span
                                    ><span class="mtext">Notice</span>
                                </a>
                            </li>


                            <li>
                                <a href="{{ route('moh.profile') }}" class="dropdown-toggle no-arrow {{ route::is('moh.profile')? 'active' : '' }}">
                                    <span class="micon bi bi-person-circle"></span
                                    ><span class="mtext">Profile</span>
                                </a>
                            </li>

                        @elseif (Route::is('midwife.*') )
                        <li>
							<a href="{{ route('midwife.home') }}" class="dropdown-toggle no-arrow {{ Route::is('midwife.home') ? 'active' : '' }}">
								<span class="micon bi bi-house-door"></span
								><span class="mtext">Home</span>
							</a>
						</li>

                        <li>
							<a href="{{ route('midwife.parent') }}" class="dropdown-toggle no-arrow {{ Route::is('midwife.parent') ? 'active' : '' }}">
								<span class="micon bi bi-people"></span
								><span class="mtext">Parents</span>
							</a>
						</li>

                        <li>
							<a href="{{ route('midwife.baby') }}" class="dropdown-toggle no-arrow {{ Route::is('midwife.baby') ? 'active' : '' }}">
								<span class="micon bi bi-person"></span
								><span class="mtext">Babies</span>
							</a>
						</li>

                        <li>
							<a href="{{ route('midwife.bulk-vaccination.create') }}" class="dropdown-toggle no-arrow {{ Route::is('midwife.bulk-vaccination.*') ? 'active' : '' }}">
								<span class="micon bi bi-eyedropper"></span
								><span class="mtext">Bulk Vaccinations</span>
							</a>
						</li>


                        <li>
							<a href="{{ route('midwife.notice') }}" class="dropdown-toggle no-arrow {{ Route::is('midwife.notice') ? 'active' : '' }}">
								<span class="micon bi bi-bell"></span
								><span class="mtext">Notices</span>
							</a>
						</li>

                        <li>
							<a href="{{ route('midwife.report') }}" class="dropdown-toggle no-arrow {{ Route::is('midwife.report') ? 'active' : '' }}">
								<span class="micon bi bi-graph-up"></span
								><span class="mtext"> Weight Report</span>
							</a>
						</li>

                        <li>
							<a href="{{ route('midwife.height-report') }}" class="dropdown-toggle no-arrow {{ Route::is('midwife.height-report') ? 'active' : '' }}">
								<span class="micon bi bi-arrows-expand"></span
								><span class="mtext"> Height Report</span>
							</a>
						</li>


                        <li>
							<a href="{{ route('midwife.appointment.index') }}" class="dropdown-toggle no-arrow {{ Route::is('midwife.appointment.*') ? 'active' : '' }}">
								<span class="micon bi bi-calendar-check"></span>
								<span class="mtext">Appointments</span>
							</a>
						</li>

                        <li>
							<a href="{{ route('midwife.chat.index') }}" class="dropdown-toggle no-arrow {{ Route::is('midwife.chat.*') ? 'active' : '' }}">
								<span class="micon bi bi-chat-dots"></span>
								<span class="mtext">Chat</span>
							</a>
						</li>

						<li>
							<a
								href="{{ route('midwife.profile') }}"

								class="dropdown-toggle no-arrow {{ Route::is('midwife.profile') ? 'active' : '' }}"
							>
								<span class="micon bi bi-person-circle"></span>
								<span class="mtext"
									>Profile
									</span>
							</a>
						</li>

                        @elseif (Route::is('parent.*') )

                            {{-- <li>
                                <a href="{{ route('parent.home') }}" class="dropdown-toggle no-arrow {{ Route::is('parent.home') ? 'active' : '' }}">
                                    <span class="micon fa fa-home"></span
                                    ><span class="mtext">Home</span>
                                </a>
                            </li> --}}

                            <li>
                                <a href="{{ route('parent.baby') }}" class="dropdown-toggle no-arrow {{ Route::is('parent.baby') ? 'active' : '' }}">
                                    <span class="micon bi bi-person"></span
                                    ><span class="mtext">Babies</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('parent.baby') }}" class="dropdown-toggle no-arrow {{ Route::is('parent.growth-record.*') ? 'active' : '' }}">
                                    <span class="micon bi bi-graph-up-arrow"></span
                                    ><span class="mtext">Growth Records</span>
                                </a>
                            </li>




                            <li>
                                <a href="{{ route('parent.report') }}" class="dropdown-toggle no-arrow {{ Route::is('parent.report') ? 'active' : '' }}">
                                    <span class="micon bi bi-graph-up"></span
                                    ><span class="mtext"> Weight Report</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('parent.height-report') }}" class="dropdown-toggle no-arrow {{ Route::is('parent.height-report') ? 'active' : '' }}">
                                    <span class="micon bi bi-arrows-expand"></span
                                    ><span class="mtext"> Height Report</span>
                                </a>
                            </li>

                            {{-- <li>
                                <a href="{{ route('parent.job') }}" class="dropdown-toggle no-arrow {{ Route::is('parent.job') ? 'active' : '' }}">
                                    <span class="micon bi bi-receipt-cutoff"></span
                                    ><span class="mtext">Notices</span>
                                </a>
                            </li> --}}

                            <li>
                                <a href="{{ route('parent.appointment.index') }}" class="dropdown-toggle no-arrow {{ Route::is('parent.appointment.*') ? 'active' : '' }}">
                                    <span class="micon bi bi-calendar-check"></span>
                                    <span class="mtext">Appointments</span>
                                </a>
                            </li>

                            <li>
                                <a href="{{ route('parent.chat.index') }}" class="dropdown-toggle no-arrow {{ Route::is('parent.chat.*') ? 'active' : '' }}">
                                    <span class="micon bi bi-chat-dots"></span>
                                    <span class="mtext">Chat</span>
                                </a>
                            </li>

                            <li>
                                <a
                                    href="{{ route('parent.profile') }}"

                                    class="dropdown-toggle no-arrow {{ Route::is('parent.profile') ? 'active' : '' }}"
                                >
                                    <span class="micon bi bi-person-circle"></span>
                                    <span class="mtext"
                                        >Profile
                                        </span>
                                </a>
                            </li>


                        @endif



					</ul>
				</div>
			</div>
		</div>
		<div class="mobile-menu-overlay"></div>

		<div class="main-container">
			<div class="pd-ltr-20 xs-pd-20-10">
				<div class="min-height-200px">

					<div>
                        @yield('content')
                    </div>
				</div>
                <br><br><br><br>
                {{-- <div class="row"> <br> </div> --}}
                {{-- footer --}}
				{{-- <div class="footer-wrap pd-20 mb-20 card-box">
					Design By
					<a href="#" target="_blank"
						>Chanaka Naveen</a
					>
				</div> --}}
			</div>
		</div>

		<!-- js -->
        <script type="text/javascript" src="/back/src/scripts/jquery.min.js"></script>
		<script src="/back/vendors/scripts/core.js"></script>
		<script src="/back/vendors/scripts/script.min.js"></script>
		<script src="/back/vendors/scripts/process.js"></script>
		<script src="/back/vendors/scripts/layout-settings.js"></script>

        <script>
			if( navigator.userAgent.indexOf("Firefox") != -1 ){
				history.pushState(null, null, document.URL);
				window.addEventListener('popstate', function(){
					history.pushState(null, null, document.URL);
				});
			}
		</script>

		<script src="/extra-assets/ijaboCropTool/ijaboCropTool.min.js"></script>
        <!-- Toastr JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script type="text/javascript" src="/back/src/plugins/noty/jquery.noty.js"></script>
		<script type="text/javascript" src="/back/src/plugins/noty/layouts/topRight.js"></script>
		<script type="text/javascript" src="/back/src/plugins/noty/layouts/top.js"></script>
		<script type="text/javascript" src="/back/src/plugins/noty/themes/default.js"></script>
        <script src="/back/src/plugins/switchery/switchery.min.js"></script>
        <script src="/back/src/plugins/bootstrap-touchspin/jquery.bootstrap-touchspin.js"></script>
        <script src="/back/vendors/scripts/advanced-components.js"></script>
		<script src="/back/src/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
        <script>
			window.addEventListener('showToastr', function(event){
                  toastr.remove();
				  if( event.detail[0].type === 'info' ){ toastr.info(event.detail[0].message); }
				  else if( event.detail[0].type === 'success' ){ toastr.success(event.detail[0].message); }
				  else if( event.detail[0].type === 'error' ){ toastr.error(event.detail[0].message); }
				  else if( event.detail[0].type === 'warning' ){ toastr.warning(event.detail[0].message); }
				  else{ return false; }
			});
		</script>
        @livewireScripts
        @stack('scripts')

        {{-- Include page script --}}
        @yield('myscript')
	</body>
</html>
