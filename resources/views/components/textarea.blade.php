<div class="form-group{{ $errors->has($key) ? ' has-error' : '' }}">
    <label class="control-label col-lg-2">{{ $placeholder }}</label>
    <div class="col-lg-10">
        <textarea name="{{ $key }}" class="form-control {{ $class }}"
            placeholder="{{ $placeholder }}">{{ isset($object) ? $object->$key : old($key) }}</textarea>
        @if ($errors->has($key))
        <span class="help-block">
            <strong>{{ $errors->first($key) }}</strong>
        </span>
        @endif
    </div>
</div>