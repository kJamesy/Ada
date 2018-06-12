@extends('admin._layouts.admin-template')

@section('title', 'Developer Guides')

@section('view-header-styles-scripts')
    <script>
        window.permissionsKey = '{!! $permissionsKey !!}';
        window.settingsKey = '{!! $settingsKey !!}';
        window.links = {
            home: '{{ route('profile.index') }}',
            base: '{{ explode( $_SERVER['SERVER_NAME'], route('developer-guides.index'))[1] }}',
            admin: '{{ rtrim(route('admin.home'), '/') }}'
        }
    </script>
    @include('admin._layouts.admin-tinymce')
@endsection

@section('developer_guides_active', 'active')
@section('content')
    <div class="am-mainpanel">
        <div class="am-pagetitle">
            <h5 class="am-title"><i class="icon ion-android-options"></i> Developer Guides</h5>
        </div>
        <div class="am-pagebody" id="admin-developer-guides-app">
            <admin-developer-guides>

            </admin-developer-guides>
        </div>
    </div>
@endsection