<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    @yield('title')
    <link rel="shortcut icon" href="">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">
    <meta name="js_local" content="/{{ App::getLocale() }}">
    <!-- style-rtl -->
    <link rel="stylesheet" href="https://cdn.salla.network/fonts/pingarlt.css?v=1.0">
    <link rel="stylesheet" href="https://cdn.salla.network/fonts/sallaicons.css">
    <link rel="pingback" href="https://salla.com/xmlrpc.php">

    <style>
        body {
            font-family: PingARLT !important;
        }
    </style>
</head>

<body class="green-skin">
    <div id="main-wrapper">
        @yield('content')
    </div>
</body>
</html>
