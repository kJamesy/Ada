@extends('admin._layouts.admin-auth-template')

@section('title', 'Admin Password Reset')

@section('content')
    <div class="am-signin-wrapper">
        <div class="am-signin-box">
            <div class="row no-gutters">
                <div class="col-lg-5">
                    <div>
                        <h1>{{ strtoupper(config('app.name')) }}</h1>
                        <p><a href="{{ route('admin.auth.show_login') }}">Log in</a></p>
                    </div>
                </div>
                <div class="col-lg-7">
                    <h5 class="tx-gray-800 mg-b-25">Admin Password Reset</h5>

                    @if (session('status'))
                        <div class="alert alert-dark">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.auth.send_password_reset_email') }}">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="email" class="form-control-label">Email</label>
                            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" >

                            @if ($errors->has('email'))
                                <small class="invalid-feedback">
                                    {{ $errors->first('email') }}
                                </small>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-block">Request Instructions</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
