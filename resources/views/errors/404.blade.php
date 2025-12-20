@if (request()->is('admin-panel/*'))
    @extends(request()->is('admin-panel/*')?'errors::illustrated-layout':'website.empty')
    @section('title', __('dashboard.not_found'))
    @section('code', '404')
    @section('message', __('dashboard.not_found'))
    @section('image')
        <img src="{{ asset('errors/404.png') }}" alt="404">
    @endsection
@else
    @include('website.not-found')
@endif
