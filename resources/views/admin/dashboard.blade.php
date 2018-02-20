@extends('admin._layouts.admin-template')

@section('title', $user->name)

@section('view-header-styles-scripts')
    <script>
        window.links = {
            home: '{{ route('dashboard.index') }}',
            base: '{{ explode( $_SERVER['HTTP_HOST'], route('dashboard.index'))[1] }}',
        }
    </script>
@endsection

@section('dashboard_active', 'active')
@section('content')
    <div class="am-mainpanel">
        <div class="am-pagetitle">
            <h5 class="am-title"><i class="ion-ios-speedometer-outline"></i> Dashboard</h5>
        </div>
        <div class="am-pagebody" id="admin-dashboard-app">
            <admin-dashboard>

            </admin-dashboard>
        </div>
    </div>
@endsection