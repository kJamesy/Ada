@extends('admin._layouts.admin-auth-template')

@section('title', 'Admin Password Reset')

@section('content')
    <div class="am-signin-wrapper">
        <div class="am-signin-box">
            <div class="row no-gutters">
                <div class="col-lg-5">
                    <div>
                        <h1>{{ strtoupper(config('newsletter.client_name')) }}</h1>
                        <p><a href="{{ route('admin.auth.show_login') }}">Log in</a></p>
                    </div>
                </div>
                <div class="col-lg-7">
                    <h5 class="tx-gray-800 mg-b-25">Admin Reset Password</h5>

                    @if (session('status'))
                        <div class="alert alert-info">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.auth.process_password_reset_form') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group">
                            <label for="email" class="form-control-label">Email</label>
                            <input id="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email or old('email') }}" >
                            @if ($errors->has('email'))
                                <small class="invalid-feedback">
                                    {{ $errors->first('email') }}
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
                            <label for="password-confirm" class="form-control-label">Confirm Password</label>
                            <input id="password-confirm" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password_confirmation">

                            @if ($errors->has('password'))
                                <small class="invalid-feedback">
                                    {{ $errors->first('password') }}
                                </small>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-block">Reset Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
