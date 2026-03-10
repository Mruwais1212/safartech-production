<div class="navbar navbar-inverse" style="background:{{ $background }}">
    <div class="navbar-header">
        <a class="navbar-brand" target="_blank" href="#"><span>{{ __('dashboard.dashboard') }}
                {{ $title }}</span></a>
        <ul class="nav navbar-nav visible-xs-block">
            <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
            <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>
    </div>
    <div class="navbar-collapse collapse" id="navbar-mobile">
        <ul class="nav navbar-nav">
            <li>
                <a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a>
            </li>
        </ul>
        <ul class="nav navbar-nav navbar-{{ __('dashboard.left') }}">
            <li>
                <a
                    href="{{ LaravelLocalization::getLocalizedURL(app()->getLocale() == 'ar' ? 'en' : 'ar', null, [], true) }}">
                    @if (app()->getLocale() == 'ar')
                        English
                    @else
                        العربية
                    @endif
                </a>
            </li>
            <li class="dropdown dropdown-user">
                <a class="dropdown-toggle" data-toggle="dropdown">
                    <img src="/settings/setting-logo.png" style="background: #fff;" alt="">
                    <span>{{ auth($guard)->user()->name }}</span>
                    <i class="caret"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-{{ __('dashboard.left') }}">
                    <li>
                        <a href="/{{ $guard }}-panel/edit-profile">
                            <i class="icon-user-plus"></i>{{ __('dashboard.edit_profile') }}
                        </a>
                    </li>
                    <li>
                        <a href="#" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();"><i class="icon-switch2"></i>
                            {{ __('dashboard.logout') }}</a>
                        <form id="admin-logout-form" action="/{{ $guard }}-panel/logout" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>
