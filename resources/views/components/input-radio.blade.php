<div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
    <label class="control-label col-lg-2">{{ $placeholder }}</label>
    <div class="col-lg-10">
        {{ $slot }}
    </div>
</div>