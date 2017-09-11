@extends('admin._layouts.admin-template')

@section('title', 'Email Settings')

@section('view-header-styles-scripts')
    <script>
        window.permissionsKey = '{!! $permissionsKey !!}';
        window.settingsKey = '{!! $settingsKey !!}';
        window.links = {
            home: '{{ route('settings.index') }}',
            base: '{{ explode( $_SERVER['SERVER_NAME'], route('email-settings.index'))[1] }}',
            admin: '{{ rtrim(route('admin.home'), '/') }}'
        }
    </script>
@endsection

@section('email_settings_active', 'active')
@section('content')
    <div id="admin-email-settings-app">
        <admin-email-settings>

        </admin-email-settings>
    </div>
@endsection