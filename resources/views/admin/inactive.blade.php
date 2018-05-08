@extends('_layouts.main-template')

@section('title', 'Admin Awaiting Approval')

@section('body')
    <div id="landing-page">
        <nav class="navbar navbar-expand-lg fixed-top">
            <a class="navbar-brand" href="{{ route('guest.home') }}">{{ strtolower(config('app.name')) }}</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.auth.get_logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">logout</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="am-signin-wrapper">
            <div class="am-signin-box">
                <div class="row no-gutters">
                    <div class="col-lg-5">
                        <h1>{{ strtoupper(config('newsletter.client_name')) }}</h1>
                    </div>
                    <div class="col-lg-7 text-center">
                        <h3 class="mb-4"><i class="icon ion-android-alarm-clock"></i> Account Inactive</h3>
                        <form id="logout-form" action="{{ route('admin.auth.post_logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                        Hi {{ $user->first_name }}, Your account is awaiting approval.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection