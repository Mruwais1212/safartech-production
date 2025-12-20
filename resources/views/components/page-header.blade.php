<div class="page-header">
    <div class="page-header-content">
        <div class="page-title">
            <h4>
                <span class="text-semibold">{{ __('dashboard.dashboard') }}</span> - {{ $header }}
            </h4>
        </div>
        @if ($url)
        <div class="heading-elements">
            <div class="heading-btn-group">
                <a href="{{ $url }}" class="btn btn-primary p-2">
                    {{ in_array(array_reverse(explode('/',request()->path()))[0],['create','edit']) ? __('dashboard.view_all') : __('dashboard.add_new') }}
                </a>
            </div>
        </div>
        @endif
    </div>

    <x-breadcrumb>
        {{ $slot }}
    </x-breadcrumb>
</div>