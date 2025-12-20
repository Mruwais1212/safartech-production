<div class="form-group text-center mb-0" style="margin-top: -54px;margin-left: 18px;">
    <a data-toggle="modal" data-target="#myModal-search" onclick="return false;" href="#" class="btn btn-primary"
     style="float: left;">
        {{ __('dashboard.filter') }}
    </a>
</div>
<div class="modal fade " id="myModal-search" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 14px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center" id="myModalLabel" style="padding-bottom: 18px;">
                    {{ __('dashboard.filter') }} </h4>
            </div>
            <form action="{{ request()->current_url }}" method="get">
                <div class="modal-body">
                    {{ $slot }}
                </div>
                <div class="modal-footer text-center" style="padding-bottom: 0px;">
                    <div class="form-group" style="padding-top: 18px; padding-bottom: 0px;">
                        <button type="button"
                            class="btn btn-default"data-dismiss="modal">{{ __('dashboard.close') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('dashboard.filter') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
