@extends('_layouts.main-template')

@section('title', 'Home')

@section('header-styles-scripts')
    <style>
        body {
            padding-top: 55px;
        }
    </style>
@endsection

@section('body')
    <div id="landing-page">
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
            <a class="navbar-brand" href="{{ route('guest.home') }}">{{ strtolower(config('app.name')) }}</a>
            <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
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
                        <h1>{{ config('newsletter.client_name') }}</h1>
                        <h3 class="text-muted mt-3">Newsletter application</h3>
                        <div class="mt-5">
                            <a href="{{ route('guest-user-guides.index') }}">User Guide</a>
                            | <a href="{{ route('guest-developer-guides.index') }}">Developer Guide</a>
                            | <a target="_blank" href="//github.com/kJamesy/tulip">Github</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
