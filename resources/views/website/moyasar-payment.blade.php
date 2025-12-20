@extends('website.layout')
@section('title', __('dashboard.Payment'))
@section('files')
    <link rel="stylesheet" href="https://cdn.moyasar.com/mpf/1.14.0/moyasar.css" />
    <script src="https://cdnjs.cloudflare.com/polyfill/v3/polyfill.min.js?version=4.8.0&features=fetch"></script>
    <script src="https://cdn.moyasar.com/mpf/1.14.0/moyasar.js"></script>
@endsection
@section('content')
    <!-- Header Start -->
    <div style="background-image: url('/site/img/summary.png');background-position-y:center ;"
        class="container-fluid header bg-white">

        <div class="background without-waves"></div> <!-- div of shadow and waves -->

        <div class="row g-0 align-items-center flex-column-reverse flex-md-row">
            <div class="col-md-12 p-5 mt-lg-5 mb-lg-5">
                <div class="container">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-md-6 main-header-col">
                            <h1 class="display-5 animated fadeIn mb-4 text-white">{{ __('dashboard.Payment') }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Header End -->

    <!-- steps form -->
    <div class="container last-step">
        <div class="row">
            <div class="container">
                <div class="card-title">
                    <h2 class="text-center">{{ __('dashboard.Payment') }}</h2>
                </div>
                <div class="col-md-12">
                    <div class="mysr-form"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        Moyasar.init({
            element: '.mysr-form',
            // Amount in the smallest currency unit.
            // For example:
            // 10 SAR = 10 * 100 Halalas
            // 10 KWD = 10 * 1000 Fils
            // 10 JPY = 10 JPY (Japanese Yen does not have fractions)
            amount: {{ $price * 100 }},
            currency: "{{ $currency }}",
            description: 'Booking A trip',
            publishable_api_key: 'pk_test_rBewqoMCPMJwgkdFYaN5wfTZ4Nz8jAzj3xBTFQjU',
            callback_url: '{{ url('moyasar-callback') }}',
            methods: ['creditcard', 'stcpay', 'mada'],
            language: 'ar',
            on_completed: '{{ asset('moyasar-callback') }}'
        })
    </script>
    <style>
        .moyasar-form {
            width: 100%;
            margin: 0 auto;
            padding: 10px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-family: "Tajawal", sans-serif !important;
        }

        .moyasar-form .moyasar-form-row {
            margin-bottom: 10px;
            fontt-family: "Tajawal", sans-serif !important;
        }

        #mysr-form-form-el#mysr-form-form-el#mysr-form-form-el.mysr-form-moyasarForm[payment-form=true] {
            font-family: "Tajawal", sans-serif !important;
        }

        #mysr-form-form-el#mysr-form-form-el#mysr-form-form-el.mysr-form-moyasarForm[payment-form=true] .mysr-form-label,
        #mysr-form-form-el#mysr-form-form-el#mysr-form-form-el.mysr-form-moyasarForm[payment-form=true] label.mysr-form-label {
            font-family: "Tajawal", sans-serif !important;
        }

        .moyasar-form .moyasar-form-row label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .moyasar-form .moyasar-form-row input[type="text"],
        .moyasar-form .moyasar-form-row input[type="email"],
        .moyasar-form .moyasar-form-row select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .moyasar-form .moyasar-form-row input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;

            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .moyasar-form .moyasar-form-row input[type="submit"]:hover {
            background-color: #0056b3;
            color: #fff;
        }
    </style>
@endsection
