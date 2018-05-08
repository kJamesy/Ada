@extends('_layouts.main-template')

@section('header-styles-scripts')
    <script>
        window.user = {!! $user !!};
        window.permissions = {!! json_encode($permissions) !!};
        window.settings = {!! json_encode($settings) !!};
    </script>
    @yield('view-header-styles-scripts')
@endsection

@section('body')
    <div class="am-header">
        <div class="am-header-left">
            <a id="naviconLeft" href="" class="am-navicon d-none d-lg-flex"><i class="icon ion-navicon-round"></i></a>
            <a id="naviconLeftMobile" href="" class="am-navicon d-lg-none"><i class="icon ion-navicon-round"></i></a>
            <a href="{{ route('guest.home') }}" class="am-logo">{{ config('newsletter.client_name') }}</a>
        </div>

        <div class="am-header-right">
            <div class="dropdown dropdown-profile">
                <a href="" class="nav-link nav-link-profile" data-toggle="dropdown">
                    <span class="logged-name"><span class="hidden-xs-down">{{ Auth::guard('web')->user()->name }}</span> <i class="icon ion-ios-arrow-down mg-l-3"></i></span>
                </a>
                <div class="dropdown-menu wd-200">
                    <ul class="list-unstyled user-profile-nav">
                        <li><a href="{{ route('profile.index') }}"><i class="icon ion-ios-person-outline"></i> Profile</a></li>
                        <li>
                            <a href="{{ route('admin.auth.get_logout') }}"><i class="icon ion-power"></i> Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @include('admin._layouts.admin-nav')
    @yield('content')
@endsection