@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.datatablejs')
    <script type="text/javascript" src="/assets/js/notify.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/switchery.min.js"></script>
    <script type="text/javascript" src="/assets/js/plugins/forms/styling/switch.min.js"></script>
    <script type="text/javascript" src="/assets/js/pages/form_checkboxes_radios.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        integrity="sha512-VEd+nq25CkR676O+pLBnDW09R7VQX9Mdiij052gVCp5yVH3jGtH70Ho/UUv4mJDsEdTvqRCFZg0NKGiojGnUCw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css"
        integrity="sha512-3pIirOrwegjM6erE5gPSwkUzO+3cTjpnV9lexlNZqvupR64iZBnOOTiiLPb9M36zpMScbmUNIcHUqKD47M719g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
        function setSwitchery(switchElement, checkedBool) {
            if ((checkedBool && !switchElement.isChecked()) || (!checkedBool && switchElement.isChecked())) {
                switchElement.setPosition(true);
                switchElement.handleOnchange(true);
            }
        }

        $(document).on("click", ".switchery", function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            })
            var id = $(this).parent().find('input').attr('object_id');
            var d_url = $(this).parent().find('input').attr('delete_url');

            $.ajax({
                url: d_url,
                type: 'post',
                data: '',
                success: function(response) {
                    toastr.success(response.message);
                },
                error: function(err) {
                    toastr.error(err.message);
                }
            })
        });
    </script>
@endsection
@section('content')
    <x-page-header :header="__('dashboard.view_users')" :url="'/admin-panel/users/create'">
        <li class="active">
            <a href="/admin-panel/users">{{ __('dashboard.view_users') }}</a>
        </li>
    </x-page-header>
    <x-panel-datatable :title="__('dashboard.users')">
        <thead>
            <tr>
                <th>{{ __('dashboard.id') }}</th>
                <th>{{ __('dashboard.user_name') }}</th>
                <th>{{ __('dashboard.phone') }}</th>
                <th>{{ __('dashboard.block') }}</th>
                <th>{{ __('dashboard.action_taken') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td style="text-align: right;">
                        <img src="{{ file_exists('uploads/' . $user->photo) ? asset('uploads/' . $user->photo) : '/images/default_user.png' }}"
                            class="rounded-circle mr-2" width="50" height="50" alt="">
                            <br>
                        {{ $user->name }}
                    </td>
                    <td>{{ $user->phone }} ({{ $user->phone_code }})</td>
                    <td>
                        <div class="checkbox checkbox-switchery switchery-sm switchery-double">
                            <input type="checkbox" object_id="{{ $user->id }}"
                                delete_url="/admin-panel/block-user/{{ $user->id }}" class="switchery sweet_switch"
                                {{ @$user->block == 0 ? '' : 'checked' }} />
                        </div>
                    </td>
                    <td align="center" class="center">
                        <ul class="icons-list">
                            @if (in_array('user-delete', $my_permissions))
                                <li class="text-danger-600">
                                    <a onclick="return false;" user_id="{{ $user->id }}"
                                        delete_url="/admin-panel/users/{{ $user->id }}" class="sweet_warning"
                                        href="#"><i class="icon-trash"></i>
                                    </a>
                                </li>
                            @endif
                            @if (in_array('user-edit', $my_permissions))
                                <li class="text-primary-600">
                                    <a href="/admin-panel/users/{{ $user->id }}/edit">
                                        <i class="icon-pencil7"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </x-panel-datatable>
@endsection
