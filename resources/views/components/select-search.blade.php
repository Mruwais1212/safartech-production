<div class="form-group {{ $formClass }}">
    <div class="col-lg-12">
        <select name="{{ $key }}" class="form-control {{ $class }}">
            {{ $slot }}
        </select>
    </div>
</div>