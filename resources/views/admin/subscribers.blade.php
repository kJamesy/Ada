@extends('admin._layouts.admin-template')

@section('title', 'Subscribers')

@section('view-header-styles-scripts')
    <script>
        window.permissionsKey = '{!! $permissionsKey !!}';
        window.settingsKey = '{!! $settingsKey !!}';
        window.links = {
            home: '{{ route('settings.index') }}',
            base: '{{ explode( $_SERVER['SERVER_NAME'], route('subscribers.index'))[1] }}',
        }
    </script>
@endsection

@section('subscribers_active', 'active')
@section('content')
    <div id="admin-subscribers-app">
        <admin-subscribers>

        </admin-subscribers>
    </div>
@endsection