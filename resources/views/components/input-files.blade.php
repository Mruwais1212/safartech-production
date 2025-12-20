<div class="form-group {{ $errors->has($key) ? ' has-error' : '' }}">
    <label class="control-label col-lg-2">
        {{ $placeholder }}
    </label>
    <div class="col-lg-5">
        <input type="file" name="{{ $options ? $key . '[]' : $key }}" value="" class="form-control"
            {{ $options }}>
        <span class="help-block">
            <strong>{{ $errors->has($key) ? $errors->first($key) : ' ' }}</strong>
        </span>
    </div>
    @if (isset($object) && isset($prefix) && !empty($object->$key))
        <div class="col-lg-5">
            <img width="150" height="100px" src="{{ $prefix }}/{{ $object->$key }}" alt="" />
            <span class="help-block">
                <strong>{{ __('dashboard.current_image') }}</strong>
            </span>
        </div>
    @endif
</div>
