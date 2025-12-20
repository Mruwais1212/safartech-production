<div>
    @if (Session::has('success'))
        <div class="col-12 alert-class" style="position: fixed;bottom: 10px; z-index: 999;">
            <div class="alert-click-hide alert alert-success alert alert-success col-9 col-xl-3 rounded-0 mb-1"
                style="z-index: 11;opacity:.9;cursor:pointer;border-radius: 10px !important;background: #f67c36;color: #000000;"
                onclick="$(this).fadeOut();">{{ Session::get('success') }}
            </div>
        </div>
    @endif


    @if (Session::has('error'))
        <div class="col-12 alert-class" style="position: fixed;bottom: 10px; z-index: 999;">
            <div class="alert-click-hide alert alert-danger alert alert-danger col-9 col-xl-3 rounded-0 mb-1"
                style="z-index: 11;opacity:.9;cursor:pointer;border-radius: 10px !important;background: #ff0000a8;color: white;"
                onclick="$(this).fadeOut();">{{ Session::get('error') }}
            </div>
        </div>
    @endif

    @if ($errors->all())
        <div class="col-12 " style="position: fixed;bottom: 10px; z-index: 999;">
            {!! implode(
                '',
                $errors->all('<div
                        			class="alert-click-hide alert alert-danger alert alert-danger col-9 col-xl-3 rounded-0 mb-1"
                        			style="z-index: 11;opacity:.9;cursor:pointer;border-radius: 10px !important;background: #ff0000a8;color: white;" onclick="$(this).fadeOut();">:message</div>'),
            ) !!}
        </div>
    @endif
</div>
