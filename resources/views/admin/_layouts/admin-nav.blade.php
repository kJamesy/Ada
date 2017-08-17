<ul class="nav flex-column admin-nav bg-primary">
    <li class="nav-item">
        <a class="nav-link @yield('dashboard_active')" href="{{ route('admin.home') }}">Dashboard</a>
    </li>
    @if ( $permissions['read_mailing_lists'] )
        <li class="nav-item">
            <a class="nav-link @yield('mailing_lists_active')" href="{{ route('mailing-lists.index') }}">Mailing Lists</a>
        </li>
    @endif
    @if ( $permissions['read_users'] )
        <li class="nav-item">
            <a class="nav-link @yield('users_active')" href="{{ route('users.index') }}">Users</a>
        </li>
    @endif
</ul>