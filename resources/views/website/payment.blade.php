@extends('website.layout')
@section('title', 'Payment')
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
                            <h1 class="display-5 animated fadeIn mb-4 text-white">Payment</h1>
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
            <div class="col-md-12">
                <form action="success-trip.html" class="steps-form">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-9">
                                <article class="card payment-card">
                                    <div class="container">
                                        <div class="card-title">
                                            <h2>Payment</h2>
                                        </div>
                                        <div class="card-body">
                                            <div class="payment-type">
                                                <h4>Choose payment method below</h4>
                                                <div class="types flex justify-space-between">
                                                    <a kind="visa" class="type selected">
                                                        <div class="logo">
                                                            <img src="/site/img/visa.png" alt="visa">
                                                        </div>
                                                        <div class="text">
                                                            <p>Pay with Visa</p>
                                                        </div>
                                                    </a>
                                                    <a kind="tamara" class="type">
                                                        <div class="logo">
                                                            <img src="/site/img/tamara.jpg" alt="tamara">
                                                        </div>
                                                        <div class="text">
                                                            <p>Tamara</p>
                                                        </div>
                                                    </a>
                                                    <a kind="mada" class="type">
                                                        <div class="logo">
                                                            <img src="/site/img/mada.png" alt="mada">
                                                        </div>
                                                        <div class="text">
                                                            <p>Pay with Tamara</p>
                                                        </div>
                                                    </a>
                                                    <a kind="stc" class="type">
                                                        <div class="logo">
                                                            <img src="/site/img/stc.png" alt="stc">
                                                        </div>
                                                        <div class="text">
                                                            <p>Pay with STC</p>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="payment-info flex justify-space-between">
                                                <div class="column billing">
                                                    <div class="title">
                                                        <div class="num">1</div>
                                                        <h4>Billing Info</h4>
                                                    </div>
                                                    <div class="field full">
                                                        <label for="name">Full Name</label>
                                                        <input id="name" type="text" placeholder="Full Name">
                                                    </div>
                                                    <div class="field full">
                                                        <label for="address">Billing Address</label>
                                                        <input id="address" type="text" placeholder="Billing Address">
                                                    </div>
                                                    <div class="flex justify-space-between">
                                                        <div class="field half">
                                                            <label for="city">City</label>
                                                            <input id="city" type="text" placeholder="City">
                                                        </div>
                                                        <div class="field half">
                                                            <label for="state">State</label>
                                                            <input id="state" type="text" placeholder="State">
                                                        </div>
                                                    </div>
                                                    <div class="field full">
                                                        <label for="zip">Zip</label>
                                                        <input id="zip" type="text" placeholder="Zip">
                                                    </div>
                                                </div>
                                                <div class="column shipping visa">
                                                    <div class="title">
                                                        <div class="num">2</div>
                                                        <h4>Credit Card Info</h4>
                                                    </div>
                                                    <div class="field full">
                                                        <label for="name">Cardholder Name</label>
                                                        <input id="name" type="text" placeholder="Full Name">
                                                    </div>
                                                    <div class="field full">
                                                        <label for="address">Card Number</label>
                                                        <input id="address" type="text"
                                                            placeholder="1234-5678-9012-3456">
                                                    </div>
                                                    <div class="flex justify-space-between">
                                                        <div class="field half">
                                                            <label for="city">Exp. Month</label>
                                                            <input id="city" type="text" placeholder="12">
                                                        </div>
                                                        <div class="field half">
                                                            <label for="state">Exp. Year</label>
                                                            <input id="state" type="text" placeholder="19">
                                                        </div>
                                                    </div>
                                                    <div class="field full">
                                                        <label for="zip">CVC Number</label>
                                                        <input id="zip" type="text" placeholder="468">
                                                    </div>
                                                </div>
                                                <div class="column shipping tamara">
                                                    <div class="title">
                                                        <div class="num">2</div>
                                                        <h4>Tamara Info</h4>
                                                    </div>
                                                    <div class="field full">
                                                        <label for="name">Cardholder Name</label>
                                                        <input id="name" type="text" placeholder="Full Name">
                                                    </div>
                                                </div>
                                                <div class="column shipping mada">
                                                    <div class="title">
                                                        <div class="num">2</div>
                                                        <h4>Mada Info</h4>
                                                    </div>
                                                    <div class="field full">
                                                        <label for="name">Cardholder Name</label>
                                                        <input id="name" type="text" placeholder="Full Name">
                                                    </div>
                                                </div>
                                                <div class="column shipping stc">
                                                    <div class="title">
                                                        <div class="num">2</div>
                                                        <h4>STC Payment Info</h4>
                                                    </div>
                                                    <div class="field full">
                                                        <label for="name">Cardholder Name</label>
                                                        <input id="name" type="text" placeholder="Full Name">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-actions flex justify-space-between">
                                            <button class="btn btn-warning py-3 w-100">Proceed</button>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </div>

                    </div>
            </div>
            </form>
        </div>
    </div>
@endsection
