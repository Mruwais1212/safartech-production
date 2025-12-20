@extends('admin.layout')
@section('js_files')
@include('admin.panel.components.addjs')
<script type="text/javascript">
    $(document).ready(function() {
            $('.add-sub_privilegeantage').click(function() {
                var type = $(this).attr('type');
                $.get('/admin-panel/privilegeItems', function(data) {
                    $('.append-at' + type).append(data);
                })
                $('.action-create').click();
            });

            $(window).load(function() {
                $('.action-create').click();
            });
            $(document).on('change', '.icon_class', function() {
                var icon = $(this).val();
                if (icon == "fa-warning") {
                    $(this).parent('div').next().next().next('.desc').show();
                } else {
                    $(this).parent('div').next().next().next('.desc').hide();
                }
            });
        });
</script>
<link href="/assets/css/fontawesome-iconpicker.css" rel="stylesheet">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<style type="text/css">
    .iconpicker .iconpicker-item {
        float: right;
    }

    .remove-extra {
        width: 23px;
        display: inline-block;
        float: right;
        height: 23px;
        color: #fff;
        background: #dc4747;
        text-align: center;
        border-radius: 50%;
        margin-left: 5px;
        cursor: pointer;
    }

    .elemContainer {
        height: 140px;
        border: solid 2px #ddd;
        padding-top: 16px;
    }
</style>
@endsection
@section('content')
<x-page-header
    :header=" isset($privilege) ? __('dashboard.edit')  .' '. __('dashboard.privilege'): __('dashboard.add') .' '. __('dashboard.privilege') "
    :url="'/admin-panel/privilege'">
    <li><a href="/admin-panel/privilege">{{ __('dashboard.view_privileges') }}</a></li>
    <li class="active">
        {{ isset($privilege) ? __('dashboard.edit') : __('dashboard.add') }} {{ __('dashboard.privilege') }}
    </li>
</x-page-header>
<x-panel-form
    :title=" isset($privilege) ? __('dashboard.edit')  .' '. __('dashboard.privilege'): __('dashboard.add') . __('dashboard.privilege') "
    :url="isset($privilege) ? '/admin-panel/privilege/' . $privilege->id : '/admin-panel/privilege'"
    :object="isset($privilege) ?$privilege:null">

    <x-input key='name_ar' :object="isset($privilege)?$privilege:null" :placeholder="__('dashboard.privilege_name_ar')"
        type='text'></x-input>

    <x-input key='name_en' :object="isset($privilege)?$privilege:null" :placeholder="__('dashboard.privilege_name_en')"
        type='text'></x-input>

    <x-input key='code' :object="isset($privilege)?$privilege:null" :placeholder="__('dashboard.privilege_code')"
        type='text'></x-input>

    <x-input key='controller' :object="isset($privilege)?$privilege:null"
        :placeholder="__('dashboard.privilege_controller')" type='text'></x-input>

    <x-input key='method' :object="isset($privilege)?$privilege:null" :placeholder="__('dashboard.privilege_method')"
        type='text'></x-input>

    <x-select key='type' :placeholder="__('dashboard.privilege_type')">
        <option value="1" {{ isset($privilege)?($privilege->type==1?'selected':''):'' }}>
            Get</option>
        <option value="2" {{ isset($privilege)?($privilege->type==2?'selected':''):'' }}
            >Other</option>
    </x-select>

    <x-input key='icon' :object="isset($privilege)?$privilege:null" :placeholder="__('dashboard.privilege_icon')"
        type='text' class="iconpicker action-create icp-glyphs"></x-input>

    <x-input key='url' :object="isset($privilege)?$privilege:null" :placeholder="__('dashboard.privilege_url')"
        type='text'></x-input>

    <h3>{{ __('dashboard.sub_privileges') }}</h3>
    <div class="form-group">
        @if (!isset($privilege) || $privilege->subPrivilege->count() == 0)
        <div class="append-at1">
            {!! View::make('admin.panel.privilege.item')->render() !!}
            <div class="clearfix"></div>
            <br>
        </div>
        @else
        <div class="append-at1">
            @foreach ($privilege->subPrivilege as $sub_privilege)
            {!! View::make('admin.panel.privilege.item')->with('sub_privilege',
            $sub_privilege)->render()!!}
            <div class="clearfix"></div>
            <br>
            @endforeach
        </div>
        @endif
        <div class="clearfix"></div>
        <br>
        <a onclick="return false" type="1"
            class="btn btn-primary add-sub_privilegeantage pull-right">{{ __('dashboard.add_sub_privilege') }}</a>
    </div>
</x-panel-form>
<script src="/assets/js/fontawesome-iconpicker.js"></script>
<script>
    $(function() {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                $('.action-destroy').on('click', function() {
                    $.iconpicker.batch('.icp.iconpicker-element', 'destroy');
                });
                // Live binding of buttons
                $(document).on('click', '.action-placement', function(e) {
                    $('.action-placement').removeClass('active');
                    $(this).addClass('active');
                    $('.icp-opts').data('iconpicker').updatePlacement($(this).text());
                    e.preventDefault();
                    return false;
                });

                $(document).on('click', '.action-create', function() {


                    $('.icp-auto').iconpicker();

                    $('.icp-dd').iconpicker({
                        //title: 'Dropdown with picker',
                        //component:'.btn > i'
                    });

                    $('.icp-glyphs').iconpicker({
                        title: 'Prepending glypghicons',
                        hideOnSelect: true,
                        icons: $.merge(['glyphicon glyphicon-home', 'glyphicon glyphicon-repeat',
                            'glyphicon glyphicon-search',
                            'glyphicon glyphicon-arrow-left', 'glyphicon glyphicon-arrow-right',
                            'glyphicon glyphicon-star'
                        ], $.iconpicker.defaultOptions.icons),
                        fullClassFormatter: function(val) {
                            if (val.match(/^fa-/)) {
                                return 'fa ' + val;
                            } else {
                                return 'glyphicon ' + val;
                            }
                        }
                    });

                    $('.icp-opts').iconpicker({
                        title: 'With custom options',
                        icons: ['fa-github', 'fa-heart', 'fa-html5', 'fa-css3'],
                        selectedCustomClass: 'label label-success',
                        mustAccept: true,
                        placement: 'bottomRight',
                        showFooter: true,
                        // note that this is ignored cause we have an accept button:
                        hideOnSelect: true,
                        templates: {
                            footer: '<div class="popover-footer">' +
                                '<div style="text-align:left; font-size:12px;">Placements: \n\
                                                    <a href="#" class=" action-placement">inline</a>\n\
                                                    <a href="#" class=" action-placement">topLeftCorner</a>\n\
                                                    <a href="#" class=" action-placement">topLeft</a>\n\
                                                    <a href="#" class=" action-placement">top</a>\n\
                                                    <a href="#" class=" action-placement">topRight</a>\n\
                                                    <a href="#" class=" action-placement">topRightCorner</a>\n\
                                                    <a href="#" class=" action-placement">rightTop</a>\n\
                                                    <a href="#" class=" action-placement">right</a>\n\
                                                    <a href="#" class=" action-placement">rightBottom</a>\n\
                                                    <a href="#" class=" action-placement">bottomRightCorner</a>\n\
                                                    <a href="#" class=" active action-placement">bottomRight</a>\n\
                                                    <a href="#" class=" action-placement">bottom</a>\n\
                                                    <a href="#" class=" action-placement">bottomLeft</a>\n\
                                                    <a href="#" class=" action-placement">bottomLeftCorner</a>\n\
                                                    <a href="#" class=" action-placement">leftBottom</a>\n\
                                                    <a href="#" class=" action-placement">left</a>\n\
                                                    <a href="#" class=" action-placement">leftTop</a>\n\
                                                    </div><hr></div>'
                        }
                    }).data('iconpicker').show();
                }).trigger('click');


                // Events sample:
                // This event is only triggered when the actual input value is changed
                // by user interaction
                $('.icp').on('iconpickerSelected', function(e) {
                    $('.lead .picker-target').get(0).className = 'picker-target fa-3x ' +
                        e.iconpickerInstance.options.iconBaseClass + ' ' +
                        e.iconpickerInstance.options.fullClassFormatter(e.iconpickerValue);
                });
            });
</script>
@endsection