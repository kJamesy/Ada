@extends('admin._layouts.admin-template')

@section('title', 'Email Contents')

@section('view-header-styles-scripts')
    <script>
        window.permissionsKey = '{!! $permissionsKey !!}';
        window.settingsKey = '{!! $settingsKey !!}';
        window.links = {
            home: '{{ route('profile.index') }}',
            base: '{{ explode( $_SERVER['SERVER_NAME'], route('email-contents.index'))[1] }}',
            admin: '{{ rtrim(route('admin.home'), '/') }}'
        }
    </script>
    @include('admin._layouts.admin-tinymce')
@endsection

@section('email_contents_active', 'active')
@section('content')
    <div class="am-mainpanel">
        <div class="am-pagetitle">
            <h5 class="am-title"><i class="icon ion-ios-paper-outline"></i> Email Contents</h5>
        </div>
        <div class="am-pagebody" id="admin-email-contents-app">
            <admin-email-contents>

            </admin-email-contents>
        </div>
    </div>
@endsection