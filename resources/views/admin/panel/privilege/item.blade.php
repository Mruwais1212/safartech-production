<br>
<div class="elemContainer">
    <div class="col-lg-2">
        <div class="remove-extra cancel-add sweet_warning" elmType="element" method="get"
            @if (isset($sub_privilege))delete_url="/admin-panel/{{ $sub_privilege->id }}" @endif
            object_id="{{ isset($sub_privilege)?$sub_privilege->id:'d' }}">-</div>
    </div>
    <div class="col-lg-3">
        <input type="text" name="sub_privilege[name_ar][]"
            value="{{ isset($sub_privilege)?$sub_privilege->name_ar:'' }}" class="form-control"
            placeholder="{{ __('dashboard.privilege_name_ar') }}">
    </div>
    <div class="col-lg-3">
        <input type="text" name="sub_privilege[name_en][]"
            value="{{ isset($sub_privilege)?$sub_privilege->name_en:'' }}" class="form-control"
            placeholder="{{ __('dashboard.privilege_name_en') }} ">
    </div>
    <div class="col-lg-2">
        <input type="text" name="sub_privilege[code][]"
            value="{{ isset($sub_privilege)?$sub_privilege->code:'' }}" class="form-control"
            placeholder="{{ __('dashboard.privilege_code') }} ">
    </div>
    <div class="col-lg-2">
        <select name="sub_privilege[status][]" class="form-control feature_item">
            <option value="0" {{ isset($sub_privilege)?($sub_privilege->status==0?'selected':''):'' }}> {{ __('dashboard.inactive') }}</option>
            <option value="1" {{ isset($sub_privilege)?($sub_privilege->status==1?'selected':''):'' }} >{{ __('dashboard.active') }}</option>
        </select>

        <input type="hidden" name="sub_privilege[id][]" value="{{ isset($sub_privilege)?$sub_privilege->id:'' }}">
    </div>
    <br>
    <div style="margin-top: 26px;">
        <div class="col-lg-3">
            <input style="direction: ltr" type="text" name="sub_privilege[url][]"
                value="{{ isset($sub_privilege)?$sub_privilege->url:'' }}" class="form-control"
                placeholder="{{ __('dashboard.privilege_url') }}">
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <input placeholder="{{ __('dashboard.privilege_controller') }}"
                    value="{{ isset($sub_privilege) ? $sub_privilege->controller  : old('sub_privilege_controller') }}"
                    class="form-control" type="text" name="sub_privilege[controller][]">
            </div>

        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input placeholder="{{ __('dashboard.privilege_method') }}"
                    value="{{ isset($sub_privilege) ? $sub_privilege->method  : old('sub_privilege_method') }}"
                    class="form-control" type="text" name="sub_privilege[method][]">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <select name="sub_privilege[type][]" class="form-control feature_item">
                    <option value="1" {{ isset($sub_privilege)?($sub_privilege->type==1?'selected':''):'' }}>
                        Get</option>
                    <option value="2" {{ isset($sub_privilege)?($sub_privilege->type==2?'selected':''):'' }}
                        >Other</option>
                </select>
            </div>
        </div>
    </div>
</div>
<div class="clearfix"></div>
<hr>