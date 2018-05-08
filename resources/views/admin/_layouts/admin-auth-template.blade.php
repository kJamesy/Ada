@extends('_layouts.main-template')

@section('body')
    @if ( config('newsletter.allow_registration') )
        <nav class="navbar navbar-expand-lg fixed-top">
            <a class="navbar-brand" href="{{ route('guest.home') }}">{{ strtoupper(config('app.name')) }}</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item @yield('active-login')"><a class="nav-link" href="{{ route('admin.auth.show_login') }}">Login</a></li>
                    <li class="nav-item @yield('active-registration')"><a class="nav-link" href="{{ route('admin.auth.show_registration') }}">Register</a></li>
                </ul>
            </div>
        </nav>
    @endif

    <div class="main-content">
        @yield('content')
    </div>
@endsection