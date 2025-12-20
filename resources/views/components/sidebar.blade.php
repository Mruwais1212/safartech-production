<div class="sidebar sidebar-main" style="background: {{ $background }}">
    <div class="sidebar-content ovauto">
        <div class="sidebar-user">
            <div class="category-content">
                <div class="media">
                    <a href="/{{ $guard }}-panel/edit-profile" class="media-{{ __("dashboard.right") }}">
                        <img style="background: #fff;" src="/settings/setting-logo.png" class="img-circle img-sm" alt="">
                    </a>
                    <div class="media-body">
                        <span class="media-heading text-semibold">{{ auth($guard)->user()->name }}</span>
                    </div>
                    <div class="media-{{ __("dashboard.left") }} media-middle">
                        <ul class="icons-list">
                            <li>
                                <a href="/{{ $guard }}-panel/edit-profile"><i class="icon-cog3"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="sidebar-category sidebar-category-visible">
            <div class="category-content no-padding">
                <div>
                    <form action="#" method="get" class="sidebar-form">
                        <div class="input-group" style="margin: auto; width:85%;margin-bottom: 15px;">
                            <input type="text" id="myInput" style="height: 30px; padding:15px" name="q"
                                class="form-control" placeholder="{{ __('dashboard.search') }}" autocomplete="off">
                        </div>
                    </form>
                </div>
                <ul class="navigation navigation-main navigation-accordion">
                    @foreach ($privileges as $privilege)
                    <li class="side_list {{ $privilege->classCss() }}">
                        <a href="{{ $privilege->url }}">
                            <i class="{{ $privilege->icon }}" style="font-size: 16px"></i>
                            <span>
                                {{ app()->getLocale()=='ar'?$privilege->name_ar:$privilege->name_en }}
                            </span>
                        </a>
                        @if ($privilege->subPrivilegeWithPermission->count()!=0)
                        <ul>
                            @foreach ($privilege->subPrivilegeWithPermission as $subPrivilege)
                            <li class="{{ $subPrivilege->classSubPrivilegeCss() }} side_list">
                                <a href="{{ $subPrivilege->url }}">
                                    <span>
                                        {{ app()->getLocale()=='ar'?$subPrivilege->name_ar:$subPrivilege->name_en }}
                                    </span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </li>
                    @endforeach
                    <li class="side_list">
                        <a href="{{ url('admin-panel/financial_reports') }}">
                            <i class="fa fa-file" style="font-size: 16px"></i>
                            <span>
                                {{ __('dashboard.money_reports') }}
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
