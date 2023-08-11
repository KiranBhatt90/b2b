<header class="main-header">
    <div class="d-flex align-items-center logo-box justify-content-start">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="logo">
            @php
                if (session()->get('usertype') != 'superadmin' && session()->get('usertype') != 'contentadmin') {
                    $user = Auth::user();
                    $schoolid = $user->school_id;
                    $school = App\Models\School::where('id', $schoolid)->first();
                    $logo = 'uploads/school/' . $school->school_logo;
                    $header_name = $school->school_name;

                    $display_name = !empty($school->school_logo) ? 'd-none' : '';
                    $display_logo = empty($school->school_logo) ? 'd-none' : '';
                } else {
                    $logo = 'assets/images/logo-valuez.png';
                    $header_name = '';
                    $display_name = '';
                    $display_logo = '';
                }
            @endphp


            <!-- logo-->
            <div class="logo-mini">
                <span class="light-logo"><img src="{{ asset('assets/images/logo-valuez.png') }}" alt="logo"
                        style="max-height:60px;"></span>
            </div>
            {{-- <div class="logo-lg {{ $display_name }}">
                {{ $header_name }}
            </div> --}}

        </a>
    </div>
    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <div class="app-menu">
            <ul class="header-megamenu nav">
                <li class="btn-group nav-item">
                    <a href="#" class="waves-effect waves-light nav-link push-btn btn-primary-light ms-0"
                        data-toggle="push-menu" role="button">
                        <i data-feather="menu"></i>
                    </a>
                </li>
                <li class="btn-group d-lg-inline-flex d-none">
                    <div class="app-menu">
                        <div class="search-bx mx-5">
                            <form>
                                <div class="input-group">
                                    <input type="search" class="form-control" placeholder="Search">
                                    <div class="input-group-append">
                                        <button class="btn" type="submit" id="button-addon3"><i
                                                class="icon-Search"><span class="path1"></span><span
                                                    class="path2"></span></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </li>
                {{-- <li class="btn-group nav-item"><h4 class="title-bx text-primary">{{ $header_name }}</h4></li>
                <li class="btn-group nav-item">
                    <div class="d-flex pt-1 align-items-center">
                        <img src="{{ asset($logo) }}"
                            class="bg-primary-light h-40" alt="">
                    </div>
                </li> --}}
            </ul>
        </div>
        @php
        $user_type = session('usertype') == 'superadmin' ? 'Super Admin' : session('usertype');
        $fullname = auth()->user()->name;
        @endphp
        <div class="navbar-custom-menu r-side">
            <ul class="nav navbar-nav">
                {{-- <li class="btn-group d-md-inline-flex d-none">
                    <a href="javascript:void(0)" title="skin Change" class="waves-effect skin-toggle waves-light">
                        <label class="switch">
                            <input type="checkbox" data-mainsidebarskin="toggle" id="toggle_left_sidebar_skin">
                            <span class="switch-on"><i data-feather="sun"></i></span>
                            <span class="switch-off"><i data-feather="moon"></i></span>
                        </label>
                    </a>
                </li> --}}
                @if($user_type=="admin"||$user_type=='teacher')
                <li class="dropdown notifications-menu btn-group">
                    <a href="#" class="waves-effect waves-light btn-primary-light svg-bt-icon bg-transparent notifyList"
                        data-bs-toggle="dropdown" title="Notifications">
                        <i data-feather="bell"></i>
                        {{-- <div class="pulse-wave"></div> --}}
                    </a>
                    <ul class="dropdown-menu animated bounceIn">
                        <li class="header">
                            <div class="p-20">
                                <div class="flexbox">
                                    <div>
                                        <h4 class="mb-0 mt-0">Notifications</h4>
                                    </div>
                                    <div>
                                        <a href="javascript:void(0);" class="text-danger text-nowrap clear_all_notify">Clear All</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <!-- inner menu: contains the actual data -->
                            <ul class="menu sm-scrol" id="notify_list">
                                {{-- <li><a href="#">noti</a></li> --}}
                            </ul>
                        </li>
                        <li class="footer bg-primary">
                            <a href="{{ route('notify.schoolview') }}" class="text-white">View all</a>
                        </li>
                    </ul>
                </li>
               @endif
                <!-- User Account-->
                <li class="dropdown user user-menu">
                    <a href="#"
                        class="waves-effect waves-light dropdown-toggle w-auto l-h-12 bg-transparent p-0 no-shadow"
                        title="User" data-bs-toggle="modal" data-bs-target="#quick_user_toggle">
                        <div class="d-flex pt-1 align-items-center">
                            <div class="text-end me-10">
                                <p class="pt-5 fs-14 mb-0 fw-700">{{ $fullname }}</p>
                                <small class="fs-10 mb-0 text-uppercase text-mute">{{ ucfirst($user_type) }}</small>
                            </div>
                            <img src="{{ asset('assets/images/avatar/avatar-13.png') }}"
                                class="avatar rounded-circle bg-primary-light h-40 w-40" alt="" />
                        </div>
                    </a>
                </li>
                <!-- Control Sidebar Toggle Button -->
                <li class="btn-group nav-item d-none">
                    <a href="#" data-provide="fullscreen"
                        class="waves-effect waves-light nav-link btn-primary-light svg-bt-icon" title="Full Screen">
                        <i data-feather="maximize"></i>
                    </a>
                </li>

            </ul>
        </div>
    </nav>
</header>

<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar-->
    <section class="sidebar position-relative">
        <div class="multinav">
            <div class="multinav-scroll" style="height: 97%;">
                <!-- sidebar menu-->
                <ul class="sidebar-menu" data-widget="tree">

                    @if (session('usertype') == 'superadmin')
                        <li class="{{ Request::is('admin-dashboard') ? 'active' : '' }}">
                            <a href="{{ route('admin-dashboard') }}"><i
                                    data-feather="home"></i><span>Dashboard</span></a>
                        </li>
                        <li class="header">Account</li>
                        <li class="{{ Request::is('school/*') ? 'active' : '' }}">
                            <a href="{{ route('school.list') }}"><i data-feather="grid"></i>Manage School</a>
                        </li>
                        <li class="{{ Request::is('users/*') ? 'active' : '' }}">
                            <a href="{{ route('users.admin.list') }}"><i data-feather="user"></i><span>Manage
                                    Users</span></a>
                        </li>
                    @elseif(session('usertype') == 'admin')
                        <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}"><i data-feather="home"></i><span>Dashboard</span></a>
                        </li>
                    @elseif(session('usertype') == 'teacher')
                        <li class="{{ Request::is('teacher/*') ? 'active' : '' }}">
                            <a href="{{ route('teacher.class.list') }}"><i data-feather="list"></i><span>Grade
                                    list</span></a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('signout') }}"><i data-feather="log-out"></i><span>Logout</span></a>
                    </li>

                </ul>

                <div class="sidebar-widgets">
                    <div class="mx-25 mb-30 pb-20 side-bx">
                        <div class="text-center">
                            <img src="{{ asset($logo) }}" class="sideimg p-5"
                                alt="">
                            <h4 class="title-bx text-primary" id="get_schoolname" data-text="{{ $header_name }}">{{ $header_name }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</aside>
