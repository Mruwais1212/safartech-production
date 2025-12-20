<div class="form-group {{ $formClass }}">
    <div class="col-lg-12">
        <input type="{{ $type }}" name="{{ $key }}"
            value="{{ request()->has($key) ? request()->get($key) : old($key) }}" class="form-control {{ $class }}"
            placeholder="{{ $placeholder }}">
    </div>
</div>
