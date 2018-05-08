@extends('admin._layouts.admin-auth-template')

@section('title', 'Admin Registration')

@section('active-registration', 'active')

@section('content')
    <div class="am-signup-wrapper">
        <div class="am-signup-box">
            <div class="row no-gutters">
                <div class="col-lg-5">
                    <div>
                        <h1>{{ strtoupper(config('newsletter.client_name')) }}</h1>
                        <p><a href="{{ route('admin.auth.show_login') }}">Log in</a></p>
                    </div>
                </div>
                <div class="col-lg-7">
                    <h5 class="tx-gray-800 mg-b-25">Admin Registration</h5>

                    <form method="POST" action="{{ route('admin.auth.store_registration') }}">
                        {{ csrf_field() }}

                        <div class="row row-xs">
                            <div class="col">
                                <div class="form-group">
                                    <label for="first_name" class="form-control-label">First Name</label>
                                    <input id="first_name" type="text" class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ old('first_name') }}" >

                                    @if ($errors->has('first_name'))
                                        <small class="invalid-feedback">
                                            {{ $errors->first('first_name') }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="last_name" class="form-control-label">Last Name</label>
                                    <input id="last_name" type="text" class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ old('last_name') }}" >

                                    @if ($errors->has('last_name'))
                                        <small class="invalid-feedback">
                                            {{ $errors->first('last_name') }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="username" class="form-control-label">Username</label>
                            <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}">

                            @if ($errors->has('username'))
                                <small class="invalid-feedback">
                                    {{ $errors->first('username') }}
                                </small>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-control-label">Email</label>
                            <input id="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}">

                            @if ($errors->has('email'))
                                <small class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </small>
                            @endif
                        </div>

                        <div class="row row-xs">
                            <div class="col">
                                <div class="form-group">
                                    <label for="password" class="form-control-label">Password</label>
                                    <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password">

                                    @if ($errors->has('password'))
                                        <small class="invalid-feedback">
                                            {{ $errors->first('password') }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="password-confirm" class="form-control-label">Confirm Password</label>
                                    <input id="password-confirm" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password_confirmation">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mg-b-20 tx-12">By registering, you agree to our Terms of Use</div>

                        <button type="submit" class="btn btn-block">Register</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
