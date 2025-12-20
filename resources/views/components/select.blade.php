<div class="form-group {{ $errors->has($key) ? ' has-error' : '' }}">
    <label class="control-label col-md-2"> {{ $placeholder }} </label>
    <div class="col-lg-10">
        <select name="{{ $key }}" class="form-control {{ $class }} border-radius" {{ $multiple?"multiple":"" }}>
            {{ $slot }}
        </select>
        @if ($errors->has($key))
        <span class="help-block">
            <strong>{{ $errors->first($key) }}</strong>
        </span>
        @endif
    </div>
</div>