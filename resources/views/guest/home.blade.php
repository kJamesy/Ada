@extends('_layouts.main-template')

@section('title', 'Home')

@section('body')
    <div id="landing-page">
        <nav class="navbar navbar-expand-lg fixed-top">
            <a class="navbar-brand" href="{{ route('guest.home') }}">{{ strtolower(config('app.name')) }}</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            @if ( config('newsletter.show_admin_link') )
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.home') }}">admin</a>
                        </li>
                    </ul>
                </div>
            @endif
        </nav>
        <div class="am-signin-wrapper">
            <div class="am-signin-box">
                <div class="row no-gutters">
                    <div class="d-none"></div>
                    <div class="col-lg-12 text-center">
                        <div>
                            <h1>{{ strtoupper(config('newsletter.client_name')) }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
