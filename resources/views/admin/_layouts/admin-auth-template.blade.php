@extends('_layouts.main-template')

@section('body')
    @if ( config('newsletter.show_frontend_navbar') )
        <nav class="navbar navbar-expand-lg fixed-top">
            <a class="navbar-brand" href="{{ route('guest.home') }}">{{ strtolower(config('app.name')) }}</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item @yield('active-login')"><a class="nav-link" href="{{ route('admin.auth.show_login') }}">login</a></li>
                    @if ( config('newsletter.allow_registration') )
                        <li class="nav-item @yield('active-registration')"><a class="nav-link" href="{{ route('admin.auth.show_registration') }}">register</a></li>
                    @endif
                </ul>
            </div>
        </nav>
    @endif

    <div class="main-content">
        @yield('content')
    </div>
@endsection