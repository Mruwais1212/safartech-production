<!DOCTYPE html>
<html lang="{{ __('dashboard.lang') }}" dir="{{ __('dashboard.dir') }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('dashboard.dashboard') }} - {{ __('dashboard.Control panel login page') }}</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet"
        type="text/css">
    <link href="/assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/core.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/components.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/colors.css" rel="stylesheet" type="text/css">
    @if (app()->getLocale() == 'en')
    <link href="/assets/css/AdminLTE.min.css" rel="stylesheet" type="text/css">
    @endif
    <script type="text/javascript" src="/assets/js/plugins/loaders/pace.min.js"></script>
    <script type="text/javascript" src="/assets/js/core/libraries/jquery.min.js"></script>
    <script type="text/javascript" src="/assets/js/core/libraries/bootstrap.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/loaders/blockui.min.js"></script>
    <script type="text/javascript" src="/assets/js/core/app.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Almarai&display=swap');

        body {
            font-family: 'Almarai', sans-serif;
        }

          #password-icon-ar {
            position: absolute;
            top: 50%;
            left: 0.5rem;
            transform: translateY(-50%);
            cursor: pointer;
        }

        #password-icon-en {
            position: absolute;
            top: 50%;
            right: 0.5rem;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="page-content">
            <div class="content-wrapper">
                <div class="content">
                    <form method="post" action="/admin-panel/login?lang={{ app()->getLocale() }}">
                        {!! csrf_field() !!}
                        @include('admin.message')
                        <div class="panel panel-body login-form">
                            <div class="text-center">
                                <img style="width: 180px;" src="/settings/setting-logo.png">
                                <h5 class="content-group">{{ __('dashboard.dashboard') }} {{ $title }}
                                    <small class="display-block">
                                        {{ __('dashboard.Enter your account login information below') }}
                                    </small>
                                </h5>
                            </div>
                            <div
                                class="form-group has-feedback has-feedback-left{{ $errors->has('email') ? ' has-error' : '' }}">
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                                    style="border-radius: 7px;" placeholder="{{ __('dashboard.email') }}">
                                @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                                @endif
                                <div class="form-control-feedback">
                                    <i class="icon-user text-muted"></i>
                                </div>
                            </div>
                            <div
                                class="form-group has-feedback has-feedback-left{{ $errors->has('password') ? ' has-error' : '' }}">
                                <input type="password" name="password" class="form-control" style="border-radius: 7px;"
                                    placeholder="{{ __('dashboard.password') }}">
                                @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                                @endif
                                <div class="form-control-feedback">
                                    <i class="icon-lock2 text-muted"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="">
                                    <label for="checkbox">
                                        <input type="checkbox" id="checkbox" name="remember">
                                        {{ __('messages.remember') }}
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" name="submit" class="btn btn-primary btn-block"
                                    style="background: {{ $background }};border-color:white;">{{ __('dashboard.login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="footer text-muted" style="padding: 4px;bottom: 0px;">
            {{ __('dashboard.All rights reserved to') }} {{ $title }} &copy; {{ date('Y') }}.
        </div>
    </div>
</body>

</html>