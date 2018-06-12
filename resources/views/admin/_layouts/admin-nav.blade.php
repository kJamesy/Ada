
<div class="am-sideleft">
    {{--<ul class="nav am-sideleft-tab">--}}
        {{--<li class="nav-item">--}}
            {{--<a href="#mainMenu" class="nav-link active"><i class="icon ion-ios-home-outline tx-24"></i></a>--}}
        {{--</li>--}}
        {{--<li class="nav-item">--}}
            {{--<a href="#" class="nav-link"></a>--}}
        {{--</li>--}}
        {{--<li class="nav-item">--}}
            {{--<a href="#" class="nav-link"></a>--}}
        {{--</li>--}}
        {{--<li class="nav-item">--}}
            {{--<a href="#" class="nav-link"></a>--}}
        {{--</li>--}}
    {{--</ul>--}}

    <div class="tab-content">
        <div id="mainMenu" class="tab-pane active">
            <ul class="nav am-sideleft-menu">
                <li class="nav-item">
                    <a href="{{ route('admin.home') }}" class="nav-link @yield('dashboard_active')">
                        <i class="ion-ios-speedometer-outline"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link with-sub {{ ( isset($activeGroup) && $activeGroup == 'content' ) ? 'active show-sub' : '' }}">
                        <i class="icon ion-ios-folder-outline"></i>
                        <span>Content</span>
                    </a>
                    <ul class="nav-sub">
                        @if ( (array_key_exists('read_emails', $permissions) && $permissions['read_emails']) || ! array_key_exists('read_emails', $permissions) )
                            <li class="nav-item">
                                <a class="nav-link @yield('emails_active')" href="{{ route('emails.index') }}">
                                    <i class="icon ion-ios-email-outline"></i>
                                    <span>Emails</span>
                                </a>
                            </li>
                        @endif

                        @if ( (array_key_exists('read_campaigns', $permissions) && $permissions['read_campaigns']) || ! array_key_exists('read_campaigns', $permissions) )
                            <li class="nav-item">
                                <a class="nav-link @yield('campaigns_active')" href="{{ route('campaigns.index') }}">
                                    <i class="icon ion-speakerphone"></i>
                                    <span>Campaigns</span>
                                </a>
                            </li>
                        @endif

                        @if ( (array_key_exists('read_templates', $permissions) && $permissions['read_templates']) || ! array_key_exists('read_templates', $permissions) )
                            <li class="nav-item">
                                <a class="nav-link @yield('templates_active')" href="{{ route('templates.index') }}">
                                    <i class="icon ion-ios-star-outline"></i>
                                    <span>Templates</span>
                                </a>
                            </li>
                        @endif

                        @if ( (array_key_exists('read_email_contents', $permissions) && $permissions['read_email_contents']) || ! array_key_exists('read_email_contents', $permissions) )
                            <li class="nav-item">
                                <a class="nav-link @yield('email_contents_active')" href="{{ route('email-contents.index') }}">
                                    <i class="icon ion-ios-paper-outline"></i>
                                    <span>Email Contents</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="" class="nav-link with-sub {{ ( isset($activeGroup) && $activeGroup == 'recipients' ) ? 'active show-sub' : '' }}">
                        <i class="icon ion-android-share"></i>
                        <span>Recipients</span>
                    </a>
                    <ul class="nav-sub">
                        @if ( (array_key_exists('read_subscribers', $permissions) && $permissions['read_subscribers']) || ! array_key_exists('read_subscribers', $permissions) )
                            <li class="nav-item">
                                <a class="nav-link @yield('subscribers_active')" href="{{ route('subscribers.index') }}">
                                    <i class="icon ion-android-people"></i>
                                    <span>Subscribers</span>
                                </a>
                            </li>
                        @endif
                        @if ( (array_key_exists('read_mailing_lists', $permissions) && $permissions['read_mailing_lists']) || ! array_key_exists('read_mailing_lists', $permissions) )
                            <li class="nav-item">
                                <a class="nav-link @yield('mailing_lists_active')" href="{{ route('mailing-lists.index') }}">
                                    <i class="icon ion-ios-list-outline"></i>
                                    <span>Mailing Lists</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="" class="nav-link with-sub {{ ( isset($activeGroup) && $activeGroup == 'settings' ) ? 'active show-sub' : '' }}">
                        <i class="icon ion-ios-gear-outline"></i>
                        <span>Settings</span>
                    </a>
                    <ul class="nav-sub">
                        @if ( (array_key_exists('read_emails', $permissions) && $permissions['read_emails']) || ! array_key_exists('read_emails', $permissions) )
                            <li class="nav-item">
                                <a class="nav-link @yield('email_settings_active')" href="{{ route('email-settings.index') }}">
                                    <i class="icon ion-email-unread"></i>
                                    <span>Email Settings</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>

                @if ( (array_key_exists('read_users', $permissions) && $permissions['read_users']) || ! array_key_exists('read_users', $permissions) )
                    <li class="nav-item">
                        <a class="nav-link @yield('users_active')" href="{{ route('users.index') }}">
                            <i class="icon ion-ios-body-outline"></i>
                            <span>Users</span>
                        </a>
                    </li>
                @endif

                <li class="nav-item">
                    <a href="" class="nav-link with-sub {{ ( isset($activeGroup) && $activeGroup == 'documentation' ) ? 'active show-sub' : '' }}">
                        <i class="icon ion-android-bulb"></i>
                        <span>Documentation</span>
                    </a>
                    <ul class="nav-sub">
                        @if ( (array_key_exists('read_user_guides', $permissions) && $permissions['read_user_guides']) || ! array_key_exists('read_user_guides', $permissions) )
                            <li class="nav-item">
                                <a class="nav-link @yield('user_guides_active')" href="{{ route('user-guides.index') }}">
                                    <i class="icon ion-ios-book-outline"></i>
                                    <span>User Guides</span>
                                </a>
                            </li>
                        @endif
                        @if ( (array_key_exists('read_developer_guides', $permissions) && $permissions['read_developer_guides']) || ! array_key_exists('read_developer_guides', $permissions) )
                            <li class="nav-item">
                                <a class="nav-link @yield('developer_guides_active')" href="{{ route('developer-guides.index') }}">
                                    <i class="icon ion-android-options"></i>
                                    <span>Developer Guides</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            </ul>
            <div id="sidebar-credit">
                <a href="//kjamesy.london" target="_blank">&copy;{{ strtolower(config('app.name')) }} {{ date('Y') }} all rights reserved.</a>
            </div>
        </div><!-- #mainMenu -->
    </div>
</div>