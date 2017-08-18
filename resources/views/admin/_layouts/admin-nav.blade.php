<ul class="nav flex-column admin-nav bg-primary">
    <li class="nav-item">
        <a class="nav-link @yield('dashboard_active')" href="{{ route('admin.home') }}">Dashboard</a>
    </li>
    @if ( (array_key_exists('read_subscribers', $permissions) && $permissions['read_subscribers']) || ! array_key_exists('read_subscribers', $permissions) )
        <li class="nav-item">
            <a class="nav-link @yield('subscribers_active')" href="{{ route('subscribers.index') }}">Subscribers</a>
        </li>
    @endif
    @if ( (array_key_exists('read_mailing_lists', $permissions) && $permissions['read_mailing_lists']) || ! array_key_exists('read_mailing_lists', $permissions) )
        <li class="nav-item">
            <a class="nav-link @yield('mailing_lists_active')" href="{{ route('mailing-lists.index') }}">Mailing Lists</a>
        </li>
    @endif
    @if ( (array_key_exists('read_users', $permissions) && $permissions['read_users']) || ! array_key_exists('read_users', $permissions) )
        <li class="nav-item">
            <a class="nav-link @yield('users_active')" href="{{ route('users.index') }}">Users</a>
        </li>
    @endif
</ul>