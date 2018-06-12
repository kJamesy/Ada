@extends('admin._layouts.admin-template')

@section('title', 'User Guides')

@section('view-header-styles-scripts')
    <script>
        window.permissionsKey = '{!! $permissionsKey !!}';
        window.settingsKey = '{!! $settingsKey !!}';
        window.links = {
            home: '{{ route('profile.index') }}',
            base: '{{ explode( $_SERVER['SERVER_NAME'], route('user-guides.index'))[1] }}',
            admin: '{{ rtrim(route('admin.home'), '/') }}'
        }
    </script>
    @include('admin._layouts.admin-tinymce')
@endsection

@section('user_guides_active', 'active')
@section('content')
    <div class="am-mainpanel">
        <div class="am-pagetitle">
            <h5 class="am-title"><i class="icon ion-ios-book-outline"></i> User Guides</h5>
        </div>
        <div class="am-pagebody" id="admin-user-guides-app">
            <admin-user-guides>

            </admin-user-guides>
        </div>
    </div>
@endsection