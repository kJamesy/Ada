@extends('admin._layouts.admin-template')

@section('title', 'Campaigns')

@section('view-header-styles-scripts')
    <script>
        window.permissionsKey = '{!! $permissionsKey !!}';
        window.settingsKey = '{!! $settingsKey !!}';
        window.links = {
            home: '{{ route('profile.index') }}',
            base: '{{ explode( $_SERVER['SERVER_NAME'], route('campaigns.index'))[1] }}',
            admin: '{{ rtrim(route('admin.home'), '/') }}'
        }
    </script>
@endsection

@section('campaigns_active', 'active')
@section('content')
    <div class="am-mainpanel">
        <div class="am-pagetitle">
            <h5 class="am-title"><i class="icon ion-speakerphone"></i> Campaigns</h5>
        </div>
        <div class="am-pagebody" id="admin-campaigns-app">
            <admin-campaigns>

            </admin-campaigns>
        </div>
    </div>
@endsection