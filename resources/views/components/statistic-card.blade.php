<div class="row text-center">
    @foreach ($statistics as $key => $statistic)
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua" style="background-color: white !important;color:#284946 !important">
                <div class="inner">
                    <a href="{{ @$links[$key] }}" style="color:#284946 !important">
                        <h3>
                            <div class="icon" style="display: flex;">
                                <i class="{{ $icons[$key] }}"
                                    style="float: left;margin-top: 22px;font-size: 30px;color:#284946 !important"></i>
                            </div>
                            <span class="statistic-count">{{ $statistic }}</span>
                        </h3>
                        <p>{{ __('dashboard.' . $key) }} </p>
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
