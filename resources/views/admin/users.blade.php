@extends('admin._layouts.admin-template')

@section('title', 'Users')

@section('view-header-styles-scripts')
    <script>
        window.permissionsKey = '{!! $permissionsKey !!}';
        window.settingsKey = '{!! $settingsKey !!}';
        window.links = {
            home: '{{ route('profile.index') }}',
            base: '{{ explode( $_SERVER['SERVER_NAME'], route('users.index'))[1] }}',
        }
    </script>
@endsection

@section('users_active', 'active')
@section('content')
    <div class="am-mainpanel">
        <div class="am-pagetitle">
            <h5 class="am-title"><i class="icon ion-ios-body-outline"></i> Users</h5>
        </div>
        <div class="am-pagebody" id="admin-users-app">
            <admin-users>

            </admin-users>
        </div>
    </div>
@endsection