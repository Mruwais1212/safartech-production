<a data-toggle="modal" data-target="#myModal{{ $object->id }}" onclick="return false;" href="#">
    <i class="fa fa-eye"></i>
</a>
<div class="modal fade" id="myModal{{ $object->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" style="border-radius: 14px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center" id="myModalLabel" style="padding-bottom: 18px;">
                    {{ __('dashboard.details') }} </h4>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            <div class="modal-footer text-center" style="padding-bottom: 0px;">
                <div class="form-group" style="padding-top: 18px; padding-bottom: 0px;">
                    <button type="button"
                        class="btn btn-default"data-dismiss="modal">{{ __('dashboard.close') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
