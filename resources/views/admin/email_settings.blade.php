@extends('admin._layouts.admin-template')

@section('title', 'Email Settings')

@section('view-header-styles-scripts')
    <script>
        window.permissionsKey = '{!! $permissionsKey !!}';
        window.settingsKey = '{!! $settingsKey !!}';
        window.links = {
            home: '{{ route('profile.index') }}',
            base: '{{ explode( $_SERVER['SERVER_NAME'], route('email-settings.index'))[1] }}',
            admin: '{{ rtrim(route('admin.home'), '/') }}'
        }
    </script>
@endsection

@section('email_settings_active', 'active')
@section('content')
    <div class="am-mainpanel">
        <div class="am-pagetitle">
            <h5 class="am-title"><i class="icon ion-email-unread"></i> Email Settings</h5>
        </div>
        <div class="am-pagebody" id="admin-email-settings-app">
            <admin-email-settings>

            </admin-email-settings>
        </div>
    </div>
@endsection