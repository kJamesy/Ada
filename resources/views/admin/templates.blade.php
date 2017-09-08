@extends('admin._layouts.admin-template')

@section('title', 'Templates')

@section('view-header-styles-scripts')
    <script>
        window.permissionsKey = '{!! $permissionsKey !!}';
        window.settingsKey = '{!! $settingsKey !!}';
        window.links = {
            home: '{{ route('settings.index') }}',
            base: '{{ explode( $_SERVER['SERVER_NAME'], route('templates.index'))[1] }}',
            admin: '{{ rtrim(route('admin.home'), '/') }}'
        }
    </script>
    @include('admin._layouts.admin-tinymce')
@endsection

@section('templates_active', 'active')
@section('content')
    <div id="admin-templates-app">
        <admin-templates>

        </admin-templates>
    </div>
@endsection