@extends('admin._layouts.admin-template')

@section('title', 'Mailing Lists')

@section('view-header-styles-scripts')
    <script>
        window.permissionsKey = '{!! $permissionsKey !!}';
        window.settingsKey = '{!! $settingsKey !!}';
        window.links = {
            home: '{{ route('settings.index') }}',
            base: '{{ explode( $_SERVER['SERVER_NAME'], route('mailing-lists.index'))[1] }}',
            admin: '{{ rtrim(route('admin.home'), '/') }}'
        }
    </script>
@endsection

@section('mailing_lists_active', 'active')
@section('content')
    <div id="admin-mailing-lists-app">
        <admin-mailing-lists>

        </admin-mailing-lists>
    </div>
@endsection