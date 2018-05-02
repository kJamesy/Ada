@extends('admin._layouts.admin-template')

@section('title', 'Mailing Lists')

@section('view-header-styles-scripts')
    <script>
        window.permissionsKey = '{!! $permissionsKey !!}';
        window.settingsKey = '{!! $settingsKey !!}';
        window.links = {
            home: '{{ route('profile.index') }}',
            base: '{{ explode( $_SERVER['SERVER_NAME'], route('mailing-lists.index'))[1] }}',
            admin: '{{ rtrim(route('admin.home'), '/') }}'
        }
    </script>
@endsection

@section('mailing_lists_active', 'active')
@section('content')
    <div class="am-mainpanel">
        <div class="am-pagetitle">
            <h5 class="am-title"><i class="icon ion-ios-list-outline"></i> Mailing Lists</h5>
        </div>
        <div class="am-pagebody" id="admin-mailing-lists-app">
            <admin-mailing-lists>

            </admin-mailing-lists>
        </div>
    </div>
@endsection