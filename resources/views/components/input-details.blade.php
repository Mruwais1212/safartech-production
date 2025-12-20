<div class="form-group {{ $formClass }}">
    <label class="control-label col-lg-12 text-{{ __('dashboard.right') }}"  >{{ $placeholder }}</label>
    <div class="col-lg-12">
        <input type="{{ $type }}" value="{{ $value }}" class="form-control {{ $class }}"
            placeholder="{{ $placeholder }}" readonly>
    </div>
</div>
