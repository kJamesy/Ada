@extends('admin._layouts.admin-auth-template')

@section('title', 'Admin Login')

@section('active-login', 'active')

@section('content')
    <div class="am-signin-wrapper">
        <div class="am-signin-box">
            <div class="row no-gutters">
                <div class="col-lg-5">
                    <div>
                        <h1>{{ strtoupper(config('app.name')) }}</h1>
                        <p>
                            <a href="{{ route('admin.auth.show_password_reset') }}">Forgot Password</a> |
                            <a href="{{ route('admin.auth.show_registration') }}">Register</a>
                        </p>
                    </div>
                </div>
                <div class="col-lg-7">
                    <h5 class="tx-gray-800 mg-b-25">Admin Login</h5>

                    <form method="POST" action="{{ route('admin.auth.process_login') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="username" class="form-control-label">Username / Email</label>
                            <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" >
                            @if ($errors->has('username'))
                                <small class="invalid-feedback">
                                    {{ $errors->first('username') }}
                                </small>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-control-label">Password</label>
                            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password">

                            @if ($errors->has('password'))
                                <small class="invalid-feedback">
                                    {{ $errors->first('password') }}
                                </small>
                            @endif
                        </div>

                        <div class="form-group">
                            <div class="checkbox">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="remember">
                                        Remember Me
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-block">Sign In</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
