<div class="app-sidebar sidebar-shadow">
    <div class="scrollbar-sidebar ps">
        <div class="app-sidebar__inner">
            @php
                $route = Route::currentRouteName();
                $user = Auth::User();
            @endphp
            <ul class="vertical-nav-menu metismenu">
                <li class="app-sidebar__heading">Menu</li>
                @if ($user->hasPermissionTo('dashboard'))
                <li class="{{ $route == 'dashboard' ? 'mm-active' : '' }}">
                    <a href="{{ route('dashboard') }}" aria-expanded="true"><i class="metismenu-icon pe-7s-rocket"></i>Dashboards</a>
                </li>
                @endif
                @if ($user->hasAnyPermission(['users.index', 'users.create']))
                    <li class="{{ $route == 'users.index' || $route == 'users.create' || $route == 'users.edit' ? 'mm-active' : '' }}">
                        <a href="#" aria-expanded="false">
                            <i class="metismenu-icon pe-7s-users"></i>Users
                            <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                        </a>
                        <ul class="mm-collapse">
                            @if ($user->hasPermissionTo('users.index'))
                                <li><a href="{{ route('users.index') }}" class="{{ $route == 'users.index' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>All Users</a></li>
                            @endif
                            @if ($user->hasPermissionTo('users.create'))
                                <li><a href="{{ route('users.create') }}" class="{{ $route == 'users.create' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>Add User</a></li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if ($user->hasAnyPermission(['admins.index', 'admins.create']))
                <li class="{{ $route == 'admins.index' || $route == 'admins.create' ? 'mm-active' : '' }}">
                    <a href="#" aria-expanded="false">
                        <i class="metismenu-icon pe-7s-users"></i>Sub Admins
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="mm-collapse">
                        @if ($user->hasPermissionTo('admins.index'))
                            <li><a href="{{ route('admins.index') }}" class="{{ $route == 'admins.index' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>All Sub Admin</a></li>
                        @endif
                        @if ($user->hasPermissionTo('admins.index'))
                            <li><a href="{{ route('admins.create') }}" class="{{ $route == 'admins.create' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>Add Sub Admin</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                {{-- @if ($user->hasPermissionTo('interest.index') || $user->hasPermissionTo('interest.create'))
                <li class="{{ $route == 'interest.index' || $route == 'interest.create' || $route == 'interest.edit' ? 'mm-active' : '' }}">
                    <a href="#" aria-expanded="false">
                        <i class="metismenu-icon pe-7s-box2"></i>Interests
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="mm-collapse">
                        @if ($user->hasPermissionTo('interest.index'))
                            <li><a href="{{ route('interest.index') }}" class="{{ $route == 'interest.index' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>All Interests</a></li>
                        @endif
                        @if ($user->hasPermissionTo('interest.create'))
                            <li><a href="{{ route('interest.create') }}" class="{{ $route == 'interest.create' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>Add Interest</a></li>
                        @endif
                    </ul>
                </li>
                @endif --}}
                @if ($user->hasAnyPermission(['category.index', 'category.create']))
                <li class="{{ $route == 'category.index' || $route == 'category.create' || $route == 'category.edit' || $route == 'category-group.index' ? 'mm-active' : '' }}">
                    <a href="#" aria-expanded="false">
                        <i class="metismenu-icon pe-7s-edit"></i>Category
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="mm-collapse">
                        @if ($user->hasPermissionTo('category-group.index'))
                            <li><a href="{{ route('category-group.index') }}" class="{{ $route == 'category-group.index' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>Category Group</a></li>
                        @endif
                        @if ($user->hasPermissionTo('category.index'))
                            <li><a href="{{ route('category.index') }}" class="{{ $route == 'category.index' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>All Categories</a></li>
                        @endif
                        @if ($user->hasPermissionTo('category.create'))
                            <li><a href="{{ route('category.create') }}" class="{{ $route == 'category.create' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>Add Category</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($user->hasAnyPermission(['new.business', 'business.index', 'recomended.business', 'business.create']))
                <li class="{{ $route == 'new.business' || $route == 'business.index' || $route == 'recomended.business' || $route == 'business.create' || $route == 'business.show' || $route == 'business.edit' ? 'mm-active' : '' }}">
                    <a href="#" aria-expanded="false">
                        <i class="metismenu-icon pe-7s-culture"></i>Business
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="mm-collapse">
                        @if ($user->hasPermissionTo('new.business'))
                            <li><a href="{{ route('new.business') }}" class="{{  $route == 'new.business' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>New Business</a></li>
                        @endif
                        @if ($user->hasPermissionTo('business.index'))
                            <li><a href="{{ route('business.index') }}" class="{{  $route == 'business.index' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>All Business</a></li>
                        @endif
                        @if ($user->hasPermissionTo('recomended.business'))
                            <li><a href="{{ route('recomended.business') }}" class="{{  $route == 'recomended.business' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>Recommended Business</a></li>
                        @endif
                        @if ($user->hasPermissionTo('business.create'))
                            <li><a href="{{ route('business.create') }}" class="{{ $route == 'business.create' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>Add Business</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($user->hasAnyPermission(['favorite-business.index', 'favorite-business.create']))
                <li class="{{ $route == 'new.business' || $route == 'favorite-business.index' || $route == 'favorite-business.create' ? 'mm-active' : '' }}">
                    <a href="#" aria-expanded="false">
                        <i class="metismenu-icon pe-7s-culture"></i>Favorite Business
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="mm-collapse">
                        @if ($user->hasPermissionTo('favorite-business.index'))
                            <li><a href="{{ route('favorite-business.index') }}" class="{{  $route == 'favorite-business.index' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>All Business</a></li>
                        @endif
                        @if ($user->hasPermissionTo('favorite-business.create'))
                            <li><a href="{{ route('favorite-business.create') }}" class="{{ $route == 'favorite-business.create' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>Add Business</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($user->hasAnyPermission(['business-report.index']))
                <li class="{{ $route == 'business-report.index' || $route == 'business-report.show' ? 'mm-active' : '' }}">
                    <a href="#" aria-expanded="false">
                        <i class="metismenu-icon pe-7s-culture"></i>Business Reports
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="mm-collapse">
                        @if ($user->hasPermissionTo('business-report.index'))
                            <li><a href="{{ route('business-report.index') }}" class="{{  $route == 'business-report.index' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>Business Reports</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($user->hasAnyPermission(['business-language.index', 'business-language.create']))
                <li class="{{ $route == 'business-language.index' || $route == 'business-language.create' || $route == 'business-language.edit' ? 'mm-active' : '' }}">
                    <a href="#" aria-expanded="false">
                        <i class="metismenu-icon pe-7s-culture"></i>Business Language
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="mm-collapse">
                        @if ($user->hasPermissionTo('business-language.index'))
                            <li><a href="{{ route('business-language.index') }}" class="{{ $route == 'business-language.index' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>All Languages</a></li>
                        @endif
                        @if ($user->hasPermissionTo('business-language.create'))
                            <li><a href="{{ route('business-language.create') }}" class="{{ $route == 'business-language.create' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>Add Language</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($user->hasAnyPermission(['event-category.index', 'event-category.create']))
                <li class="{{ $route == 'event-category.index' || $route == 'event-category.create' || $route == 'event-category.edit' ? 'mm-active' : '' }}">
                    <a href="#" aria-expanded="false">
                        <i class="metismenu-icon pe-7s-culture"></i>Event Category
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="mm-collapse">
                        @if ($user->hasPermissionTo('event-category.index'))
                            <li><a href="{{ route('event-category.index') }}" class="{{ $route == 'event-category.index' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>All Event categories</a></li>
                        @endif
                        @if ($user->hasPermissionTo('event-category.create'))
                            <li><a href="{{ route('event-category.create') }}" class="{{ $route == 'event-category.create' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>Add Event Category</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($user->hasAnyPermission(['support.index']))
                <li class="{{ $route == 'support.index' ? 'mm-active' : ''}}">
                    <a href="#" aria-expanded="false">
                        <i class="metismenu-icon pe-7s-culture"></i>Support
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="mm-collapse">
                        @if ($user->hasPermissionTo('support.index'))
                            <li><a href="{{ route('support.index') }}" class="{{ $route == 'support.index' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>All Support Queries</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($user->hasAnyPermission(['custom-notification.index', 'custom-notification.create']))
                <li class="{{ $route == 'custom-notification.index' || $route == 'custom-notification.create' ? 'mm-active' : '' }}">
                    <a href="#" aria-expanded="false">
                        <i class="metismenu-icon pe-7s-culture"></i>Custom Notifications
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="mm-collapse">
                        @if ($user->hasPermissionTo('custom-notification.index'))
                            <li><a href="{{ route('custom-notification.index') }}" class="{{ $route == 'custom-notification.index' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>All Custom Notifications</a></li>
                        @endif
                        @if ($user->hasPermissionTo('custom-notification.create'))
                            <li><a href="{{ route('custom-notification.create') }}" class="{{ $route == 'custom-notification.create' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>Add Custom Notifications</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @if ($user->hasAnyPermission(['plan.index', 'plan.create']))
                <li class="{{ $route == 'plan.index' || $route == 'plan.create' ? 'mm-active' : '' }}">
                    <a href="#" aria-expanded="false">
                        <i class="metismenu-icon pe-7s-culture"></i>Plans
                        <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>
                    </a>
                    <ul class="mm-collapse">
                        @if ($user->hasPermissionTo('plan.index'))
                            <li><a href="{{ route('plan.index') }}" class="{{ $route == 'plan.index' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>All Plans</a></li>
                        @endif
                        @if ($user->hasPermissionTo('plan.create'))
                            <li><a href="{{ route('plan.create') }}" class="{{ $route == 'plan.create' ? 'mm-active' : ''}}"><i class="metismenu-icon"></i>Add Plan</a></li>
                        @endif
                    </ul>
                </li>
                @endif
            </ul>
        </div>
        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
        </div>
        <div class="ps__rail-y" style="top: 0px; right: 0px;">
            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
        </div>
    </div>
</div>
