@extends('admin._layouts.admin-template')

@section('title', 'Emails')

@section('view-header-styles-scripts')
    <script>
        window.permissionsKey = '{!! $permissionsKey !!}';
        window.settingsKey = '{!! $settingsKey !!}';
        window.links = {
            home: '{{ route('profile.index') }}',
            base: '{{ explode( $_SERVER['SERVER_NAME'], route('emails.index'))[1] }}',
        }
    </script>
    @include('admin._layouts.admin-tinymce')
@endsection

@section('emails_active', 'active')
@section('content')
    <div class="am-mainpanel">
        <div class="am-pagetitle">
            <h5 class="am-title"><i class="icon ion-ios-email-outline"></i> Emails</h5>
        </div>
        <div class="am-pagebody" id="admin-emails-app">
            <admin-emails>

            </admin-emails>
        </div>
    </div>
@endsection