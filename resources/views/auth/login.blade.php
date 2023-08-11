<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../images/favicon.ico">
    <meta name="_token" content="{{ csrf_token() }}" />

    <title>Valuez School - Log in </title>

    <!-- Vendors Style-->
    <link rel="stylesheet" href="{{ asset('assets/src/css/vendors_css.css') }}">

    <!-- Style-->
    <link rel="stylesheet" href="{{ asset('assets/src/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/src/css/skin_color.css') }}">

</head>

<body class="hold-transition theme-primary bg-img"
    style="background-image: url({{ asset('assets/images/auth-bg/bg-20.png') }});background-size: contain;background-position: bottom; ">

    <div class="container h-p100">
        <div class="row align-items-center justify-content-md-center h-p100">

            <div class="col-12">
                <div class="row justify-content-center g-0">
                    <div class="col-lg-5 col-md-5 col-12">
                        <div class="bg-white rounded10 shadow-lg">
                            <div class="content-top-agile p-20 pb-0">
                                <h2 class="text-primary fw-600">Let's Get Started</h2>
                                <p class="mb-0 text-fade">Sign in to continue to Valuez School.</p>
                            </div>
                            <div class="p-40">
                                <form method="POST" action="{{ route('login.process') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label class="form-label">Email id <span class="text-danger">*</span></label>
                                        <div class="controls">
                                            <input type="text" name="email" class="form-control"
                                                placeholder="Username">
                                        </div>
                                        @if ($errors->has('email'))
                                            <div class="form-control-feedback">
                                                <small class="text-danger">{{ $errors->first('email') }}</small>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Password <span class="text-danger">*</span></label>
                                        <div class="controls">
                                            <input type="password" name="password" class="form-control"
                                                placeholder="Password">
                                        </div>
                                        @if ($errors->has('password'))
                                            <div class="form-control-feedback">
                                                <small class="text-danger">{{ $errors->first('password') }}</small>
                                            </div>
                                        @endif

                                    </div>
                                    <div class="row">
                                        @if ($errors->has('error'))
                                            <div class="form-control-feedback">
                                                <small class="text-danger">{{ $errors->first('error') }}</small>
                                            </div>
                                        @endif
                                        <div class="col-auto mt-4">
                                            <div class="checkbox">
                                                <input type="checkbox" id="basic_checkbox_1" name="remember">
                                                <label for="basic_checkbox_1">Remember Me</label>
                                            </div>
                                        </div>
                                        <div class="col-12 text-center mt-2">
                                            <button type="submit" class="btn btn-primary w-p100 mt-10">SIGN IN</button>
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Vendor JS -->
    <script src="{{ asset('assets/src/js/vendors.min.js') }}"></script>
    <script src="{{ asset('assets/src/js/pages/chat-popup.js') }}"></script>
    <script src="{{ asset('assets/icons/feather-icons/feather.min.js') }}"></script>

</body>

</html>
