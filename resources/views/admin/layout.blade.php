<!DOCTYPE html>
<html lang="{{ __('dashboard.lang') }}" dir="{{ __('dashboard.dir') }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">
    <title>{{ __('dashboard.dashboard') }} - {{ $title }}</title>
    <meta name="_lang" content="{{ App::getLocale() }}">
    <link rel="shortcut icon" href="/settings/setting-website_icon.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo&display=swap" rel="stylesheet">
    <link href="/assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/bootstrap.css" rel="stylesheet" type="text/css">
    @if (app()->getLocale() == 'ar')
    <link href="/assets/css/AdminLTE-RTL.min.css" rel="stylesheet" type="text/css">
    @else
    <link href="/assets/css/AdminLTE.min.css" rel="stylesheet" type="text/css">
    @endif
    <link href="/assets/css/core.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/components.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/colors.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="/assets/js/plugins/loaders/pace.min.js"></script>
    <script type="text/javascript" src="/assets/js/core/libraries/jquery.min.js"></script>
    <script type="text/javascript" src="/assets/js/core/libraries/bootstrap.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/loaders/blockui.min.js"></script>
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    @yield('js_files')
    <script type="text/javascript">
        $(document).ready(function() {
            $("#myInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();

                $(".side_list").filter(function() {
                    $(this).find('.hidden-ul').css('display', "block");
                    $(this).toggle($(this).find('h5,span').text().toLowerCase().trim().indexOf(
                        value) > -1)
                    if ($("#myInput").val().toLowerCase() == '') {
                        $('.hidden-ul').css('display', "none");
                    }
                });
            });
        })
    </script>
</head>

<body>
    <x-navbar :background="$background" :title="$title"></x-navbar>
    <div class="page-container">
        <div class="page-content">
            <x-sidebar :privileges="$privileges" :background="$background"></x-sidebar>
            <div class="content-wrapper">
                @include('admin.message')
                @yield('content')
                <div class="footer text-muted" style="padding: 4px;bottom: 0px;">
                    {{ __('dashboard.All rights reserved to') }} {{ $title }} &copy; {{ date('Y') }}.
                </div>
            </div>

        </div>
        @if (auth('admin')->user()->group_privilege_id==1)
        <div>
            <a href="/admin-panel/logs" target="_blank">
                <i style=" position: fixed;bottom: 2px;left: 27px;font-size: 57px;" class="fa fa-history"></i>
            </a>
        </div>
        @endif
    </div>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-messaging.js"></script>
    <script src="{{ url('/firebase.js') }}"></script>
    <script>
        $(".navigation > li > a").click(function() {
            $(this).toggleClass('active');
        });
        $(".sidebar-main-toggle").click(function() {
            $('.content-wrapper').toggleClass('activewidth');
        });
        $(".sidebar-main-toggle").click(function() {
            $('.sidebar-content').toggleClass('ovauto');
            $('.content-wrapper.only').toggleClass('add-product-content');
        });
        $(".sidebar-main-toggle").click(function() {
            $('.sidebar-main.sidebar').toggleClass('sidebarheight');
            $('.sidebar-main.sidebar').toggleClass('small-sidebar');
        });
        $(".alert").delay(4000).slideUp(200, function() {
               $(this).alert('close');
        });
    </script>
    @yield('js_files_footer')
</body>

</html>
