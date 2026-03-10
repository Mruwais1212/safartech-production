@extends('website.layout')

@section('content')
    <form id="moyasar-success-post-form" action="{{ url('moyasar-success') }}" method="POST" class="d-none">
        @csrf
        <input type="hidden" name="id" value="{{ $paymentId }}">
        <input type="hidden" name="nonce" value="{{ $nonce }}">
    </form>

    <script>
        document.getElementById('moyasar-success-post-form').submit();
    </script>
@endsection
