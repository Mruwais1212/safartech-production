<style>
    html,
    body {
        height: 100%;
        margin: 0;
        padding: 0;
    }

    #map {
        height: 100%;
    }

    .controls {
        border-radius: 3px;
        box-sizing: border-box;
        -moz-box-sizing: border-box;
        height: 32px;
        outline: none;
    }

    #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 300px;
        float: right;
    }

    #pac-input:focus {
        border-color: #4d90fe;
    }

    .pac-container {
        font-family: Roboto;
    }

    #type-selector {
        color: #fff;
        background-color: #4d90fe;
        padding: 5px 11px 0px 11px;
    }

    #type-selector label {
        font-family: Roboto;
        font-size: 13px;
        font-weight: 300;
    }

    #gmap {
        height: 400px;
    }
</style>
<div style="display: block;width: 100%;">
    <span class="icon icon-map-marker"></span>
    <div class=" form-group {{ $errors->has('location_lat') || $errors->has('location_lng') ? 'input_error' : '' }}">
        <input id="pac-input" name="map_address" class="controls form-control mb-3" type="text" placeholder="حدد العنوان على الخريطة">
        @if ($errors->has('address') || $errors->has('location_lat') || $errors->has('location_lng'))
            <span class="help-block">
                <strong>{{ $errors->first('map_address') }}</strong>
                <strong>{{ $errors->first('location_lat') }}</strong>
                <strong>{{ $errors->first('location_lng') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="map" style="border-top: 3px solid #dedbdb;">

    <div id="gmap"></div>
</div>
