@extends('admin._layouts.admin-template')

@section('title', 'Campaigns')

@section('view-header-styles-scripts')
    <script>
        window.permissionsKey = '{!! $permissionsKey !!}';
        window.settingsKey = '{!! $settingsKey !!}';
        window.links = {
            home: '{{ route('settings.index') }}',
            base: '{{ explode( $_SERVER['SERVER_NAME'], route('campaigns.index'))[1] }}',
            admin: '{{ rtrim(route('admin.home'), '/') }}'
        }
    </script>
@endsection

@section('campaigns_active', 'active')
@section('content')
    <div id="admin-campaigns-app">
        <admin-campaigns>

        </admin-campaigns>
    </div>
@endsection