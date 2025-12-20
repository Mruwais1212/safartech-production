@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.datatablejs')
@endsection
@section('content')
<x-page-header :header="__('dashboard.communication_messages')">
    <li class="active"><a href="">{{ __('dashboard.communication_messages') }}</a></li>
</x-page-header>
<x-panel-datatable :title=" __('dashboard.communication_messages')">
    <thead>
        <tr>
            <th>#</th>
            <th>{{ __('dashboard.name') }}</th>
            <th>{{ __('dashboard.email') }}</th>
            <th>{{ __('dashboard.phone') }}</th>
            <th>{{ __('dashboard.message_title') }}</th>
            <th>{{ __('dashboard.message') }}</th>
            <th>{{ __('dashboard.action_taken') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($contacts as $key => $contact)
        <tr parent_id="{{ $contact->id }}">
            <td>{{ $key+1 }}</td>
            <td>{{ @$contact->user->name }}</td>
            <td>{{ @$contact->user->email }}</td>
            <td>{{ @$contact->user->phone }}</td>
            <td>{{ @$contact->subject }}</td>
            <td>{{ $contact->message }}</td>
            <td align="center" class="center">
                <ul class="icons-list">
                    @if (in_array('contact-delete', $my_permissions))
                    <li class="text-danger-600">
                        <a onclick="return false;" contact_id="{{ $contact->id }}"
                            delete_url="/admin-panel/contacts/{{ $contact->id }}" class="sweet_warning" href="#"><i
                                class="icon-trash"></i>
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