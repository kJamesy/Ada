@extends('admin._layouts.admin-template')

@section('title', $user->name)

@section('view-header-styles-scripts')
    <script>
        window.links = {
            home: '{{ route('profile.index') }}',
            base: '{{ explode( $_SERVER['HTTP_HOST'], route('profile.index'))[1] }}',
        }
    </script>
@endsection

{{--@section('profile_active', 'active')--}}
@section('content')
    <div class="am-mainpanel">
        <div class="am-pagetitle">
            <h5 class="am-title"><i class="icon ion-ios-person-outline"></i> Profile</h5>
        </div>
        <div class="am-pagebody" id="admin-profile-app">
            <admin-profile>

            </admin-profile>
        </div>
    </div>
@endsection