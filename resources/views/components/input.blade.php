<div class="form-group{{ $errors->has($key) ? ' has-error' : '' }}">
    <label class="control-label col-lg-2">{{ $placeholder }}</label>
    <div class="col-lg-10">
        <input type="{{ $type }}" name="{{ $key }}"
            value="{{ $type=='password'?'':(isset($object) ? ( isset($object->$key) ? $object->$key : old($key) ) : old($key)) }}"
            class="form-control {{ $class }} border-radius" placeholder="{{ $placeholder }}">
        @if ($errors->has($key))
        <span class="help-block">
            <strong>{{ $errors->first($key) }}</strong>
        </span>
        @endif
    </div>
</div>