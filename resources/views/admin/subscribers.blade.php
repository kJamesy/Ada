@extends('admin._layouts.admin-template')

@section('title', 'Subscribers')

@section('view-header-styles-scripts')
    <script>
        window.permissionsKey = '{!! $permissionsKey !!}';
        window.settingsKey = '{!! $settingsKey !!}';
        window.links = {
            home: '{{ route('profile.index') }}',
            base: '{{ explode( $_SERVER['SERVER_NAME'], route('subscribers.index'))[1] }}',
        }
    </script>
@endsection

@section('subscribers_active', 'active')
@section('content')
    <div class="am-mainpanel">
        <div class="am-pagetitle">
            <h5 class="am-title"><i class="icon ion-android-people"></i> Subscribers</h5>
        </div>
        <div class="am-pagebody" id="admin-subscribers-app">
            <admin-subscribers>

            </admin-subscribers>
        </div>
    </div>
@endsection