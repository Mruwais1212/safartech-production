@extends('admin.layout')
@section('js_files')
    @include('admin.panel.components.addjs')
    <script type="text/javascript" src="/assets/js/admin/cbFamily.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $("h3.checked input:checkbox").cbFamily(function() {
                return $(this).parents("h3.checked").next().find("input:checkbox");
            });
        });
    </script>
@endsection
@section('content')
    <x-page-header :header="isset($privilege_group)
        ? __('dashboard.edit') . ' ' . __('dashboard.privilege_group')
        : __('dashboard.add') . ' ' . __('dashboard.privilege_group')" :url="'/admin-panel/privilege-group'">
        <li>
            <a href="/admin-panel/privilege-group">{{ __('dashboard.view_group_permissions') }}</a>
        </li>
        <li class="active">
            {{ isset($privilege_group) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.privilege_group') }}
        </li>
    </x-page-header>
    <x-panel-form :title="isset($privilege_group)
        ? __('dashboard.edit') . ' ' . __('dashboard.privilege_group')
        : __('dashboard.add') . ' ' . __('dashboard.privilege_group')" :url="isset($privilege_group)
        ? '/admin-panel/privilege-group/' . $privilege_group->id
        : '/admin-panel/privilege-group'" :object="isset($privilege_group) ? $privilege_group : null">

        <x-input key='name_ar' :object="isset($privilege_group) ? $privilege_group : null" :placeholder="__('dashboard.privilege_group_name_ar')" type='text'></x-input>

        <x-input key='name_en' :object="isset($privilege_group) ? $privilege_group : null" :placeholder="__('dashboard.privilege_group_name_en')" type='text'></x-input>

        <div class="form-group prevs">
            <label class="control-label col-lg-2">{{ __('dashboard.permissions') }}</label>
            <div class="col-lg-10">
                @php
                    $prevs = [];
                    $i = 1;
                @endphp
                @foreach ($privileges->groupBy('guard') as $key => $privilegesGroup)
                    <hr>
                    @if ($key == 1)
                        <h2 class="text-center text-green">Admin</h2>
                    @else
                        <h2 class="text-center text-green">Center</h2>
                    @endif
                    @foreach ($privilegesGroup as $privilege)
                        @php
                            if (isset($privilege_group->id) && !empty($privilege_group->privileges)) {
                                $prevs = isset($privilege_ids) ? $privilege_ids : [];
                            }
                        @endphp
                        <section>
                            <h3 class="{{ $privilege->children()->count() > 0 ? 'checked' : '' }}"><label>
                                    <input type="checkbox" name="privileges[]" value="{{ $privilege->id }}"
                                        {{ in_array($privilege->id, $prevs) ? 'checked' : '' }} />
                                    {{ app()->getLocale() == 'ar' ? $privilege->name_ar : $privilege->name_en }}
                                </label>
                            </h3>
                            @if ($privilege->children()->count())
                                <div class="children">
                                    @foreach ($privilege->children()->get() as $children)
                                        <label><input type="checkbox" name="privileges[]" value="{{ $children->id }}"
                                                @if (in_array($children->id, $prevs)) checked @endif />
                                            {{ app()->getLocale() == 'ar' ? $children->name_ar : $children->name_en }}
                                        </label>&nbsp; &nbsp;
                                    @endforeach
                                </div>
                            @endif
                        </section>
                    @endforeach
                @endforeach
            </div>
        </div>
    </x-panel-form>
@endsection
