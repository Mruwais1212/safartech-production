<a data-toggle="modal" data-target="#myModal{{ $activity->id }}" onclick="return false;" href="#">
    {{ __('dashboard.view_details') }}
</a>
<div class="modal fade" id="myModal{{ $activity->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">{{ __('dashboard.details') }} </h4>
            </div>
            <div class="modal-body">
                <div class="">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    {{ __('dashboard.title') }}
                                </th>
                                <th>
                                    {{ __('dashboard.old') }}
                                </th>
                                <th>
                                    {{ __('dashboard.new') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ((array) json_decode($activity->data) as $key => $item)
                                <tr>
                                    <td>
                                        {{ __('dashboard.' . $prefix . '_' . $key) }}
                                    </td>
                                    <td>
                                        @if (in_array($key, [
                                                'created_at',
                                                'updated_at',
                                                'accepted_at',
                                                'deliver_the_child_at',
                                                'receive_the_child_at',
                                                'cancelled_at',
                                            ]) && $item->old)
                                            {{ \Carbon\Carbon::parse($item->old)->diffForHumans() }}
                                        @elseif (in_array($key, ['status']) && $item->old && $activity->activity_type == 'App\Models\Order')
                                            {{ __('dashboard.' . Str::lower(\App\Enums\OrderStatus::getKey($item->old))) }}
                                        @elseif (in_array($key, ['status']) && $item->old && $activity->activity_type == 'App\Models\WorkRequest')
                                            {{ __('dashboard.' . Str::lower(\App\Enums\WorkRequestStatus::getKey($item->old))) }}
                                        @elseif (in_array($key, ['status']) && $item->old === 0 && $activity->activity_type == 'App\Models\Order')
                                            {{ __('dashboard.' . Str::lower(\App\Enums\OrderStatus::getKey($item->old))) }}
                                        @elseif (in_array($key, ['status']) && $item->old === 0 && $activity->activity_type == 'App\Models\WorkRequest')
                                            {{ __('dashboard.' . Str::lower(\App\Enums\WorkRequestStatus::getKey($item->old))) }}
                                        @else
                                            {{ @$item->old }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (in_array($key, [
                                                'created_at',
                                                'updated_at',
                                                'accepted_at',
                                                'deliver_the_child_at',
                                                'receive_the_child_at',
                                                'cancelled_at',
                                            ]) && $item->new)
                                            {{ \Carbon\Carbon::parse($item->new)->diffForHumans() }}
                                        @elseif (in_array($key, ['status']) && $item->new && $activity->activity_type == 'App\Models\Order')
                                            {{ __('dashboard.' . Str::lower(\App\Enums\OrderStatus::getKey($item->new))) }}
                                        @elseif (in_array($key, ['status']) && $item->new && $activity->activity_type == 'App\Models\WorkRequest')
                                            {{ __('dashboard.' . Str::lower(\App\Enums\WorkRequestStatus::getKey($item->new))) }}
                                        @else
                                            {{ @$item->new }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                    data-dismiss="modal">{{ __('dashboard.close') }}</button>
            </div>
        </div>

    </div>
</div>
