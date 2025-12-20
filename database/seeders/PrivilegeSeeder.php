<?php

namespace Database\Seeders;

use App\Models\Panel\Privilege;
use App\Models\User;
use Illuminate\Database\Seeder;

class PrivilegeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        auth('admin')->login(User::find(1));
        $permission = Privilege::updateOrCreate(['controller' => 'Panel\Auth\PrivilegeController', 'method' => null], [
            'name_ar' => 'الصلاحيات',
            'name_en' => 'Permissions',
            'url' => null,
            'code' => 'privileges',
            'type' => 1,
            'icon' => 'fa fa-expeditedssl',
            'parent_id' => null,
            'sort' => 9,
            'status' => 1,
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\PrivilegeController', 'method' => 'index'], [
            'name_ar' => 'الصلاحيات',
            'name_en' => 'Permissions',
            'url' => '/admin-panel/privilege',
            'code' => 'privileges-index',
            'type' => 1,
            'icon' => null,
            'parent_id' => $permission->id,
            'sort' => 7,
            'status' => 1,
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\PrivilegeController', 'method' => 'create'], [
            'name_ar' => 'انشاء صلاحية',
            'name_en' => 'Create Permission',
            'url' => '/admin-panel/privilege/create',
            'code' => 'privileges-create',
            'type' => 1,
            'icon' => null,
            'parent_id' => $permission->id,
            'sort' => 1,
            'status' => 1,
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\PrivilegeController', 'method' => 'edit'], [
            'name_ar' => 'تعديل صلاحية',
            'name_en' => 'Edit Permission',
            'url' => null,
            'type' => 2,
            'code' => 'privileges-edit',
            'icon' => null,
            'parent_id' => $permission->id,
            'sort' => 2,
            'status' => 1,
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\PrivilegeController', 'method' => 'destroy'], [
            'name_ar' => 'حذف صلاحية',
            'name_en' => 'Delete Permission',
            'url' => null,
            'type' => 2,
            'code' => 'privileges-delete',
            'icon' => null,
            'parent_id' => $permission->id,
            'sort' => 3,
            'status' => 1,
        ]);

        $setting_type = Privilege::updateOrCreate(['controller' => 'Panel\Auth\SettingTypeController', 'method' => null], [
            'name_ar' => ' انواع الاعدادات',
            'name_en' => 'Setting Types',
            'url' => null,
            'type' => 1,
            'code' => 'setting-types',
            'icon' => 'fa fa-user-times',
            'parent_id' => null,
            'sort' => 10,
            'status' => 1,
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\SettingTypeController', 'method' => 'index'], [
            'name_ar' => 'انواع الاعدادات',
            'name_en' => 'Setting Types',
            'url' => '/admin-panel/setting-type',
            'code' => 'setting-types-index',
            'type' => 1,
            'icon' => null,
            'parent_id' => $setting_type->id,
            'sort' => 8,
            'status' => 1,
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\SettingTypeController', 'method' => 'create'], [
            'name_ar' => 'انشاء نوع الاعداد',
            'name_en' => 'Create Setting Type',
            'url' => '/admin-panel/setting-type/create',
            'type' => 1,
            'icon' => null,
            'parent_id' => $setting_type->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'setting-types-create',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\SettingTypeController', 'method' => 'edit'], [
            'name_ar' => 'تعديل نوع الاعداد',
            'name_en' => 'Edit Setting Type',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $setting_type->id,
            'sort' => 2,
            'status' => 1,
            'code' => 'setting-types-edit',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\SettingTypeController', 'method' => 'destroy'], [
            'name_ar' => 'حذف نوع الاعداد',
            'name_en' => 'Delete Setting Type',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $setting_type->id,
            'sort' => 3,
            'status' => 1,
            'code' => 'setting-types-delete',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\HomeController', 'method' => 'index'], [
            'name_ar' => 'الصفحة الرئيسية',
            'name_en' => 'Home',
            'url' => '/admin-panel',
            'type' => 1,
            'icon' => 'fa fa-home',
            'parent_id' => null,
            'sort' => 1,
            'status' => 1,
            'code' => 'home',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\ActivityController', 'method' => 'index'], [
            'name_ar' => 'نشاط الموقع',
            'name_en' => 'Site Activity',
            'url' => '/admin-panel/activity',
            'type' => 1,
            'icon' => 'fa fa-address-book',
            'parent_id' => null,
            'sort' => 2,
            'status' => 1,
            'code' => 'activity',
        ]);

        $slider = Privilege::updateOrCreate(['controller' => 'Panel\Auth\SliderController', 'method' => null], [
            'name_ar' => 'البنرات الاعلانية',
            'name_en' => 'Sliders',
            'url' => null,
            'type' => 1,
            'icon' => 'fa fa-newspaper-o',
            'parent_id' => null,
            'sort' => 7,
            'status' => 1,
            'code' => 'sliders',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\SliderController', 'method' => 'index'], [
            'name_ar' => 'البنرات الاعلانية',
            'name_en' => 'Sliders',
            'url' => '/admin-panel/slider',
            'type' => 1,
            'icon' => null,
            'parent_id' => $slider->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'sliders-index',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\SliderController', 'method' => 'create'], [
            'name_ar' => 'انشاء البنر الاعلانية',
            'name_en' => 'Create Slider',
            'url' => '/admin-panel/slider/create',
            'type' => 1,
            'icon' => null,
            'parent_id' => $slider->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'sliders-create',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\SliderController', 'method' => 'edit'], [
            'name_ar' => 'تعديل البنر الاعلانية',
            'name_en' => 'Edit Slider',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $slider->id,
            'sort' => 2,
            'status' => 1,
            'code' => 'sliders-edit',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\SliderController', 'method' => 'destroy'], [
            'name_ar' => 'حذف البنر الاعلانية',
            'name_en' => 'Delete Slider',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $slider->id,
            'sort' => 3,
            'status' => 1,
            'code' => 'sliders-delete',
        ]);

        $permission = Privilege::updateOrCreate(['controller' => 'Panel\Auth\PrivilegeGroupController', 'method' => null], [
            'name_ar' => 'انواع الصلاحيات',
            'name_en' => 'Permission Groups',
            'url' => null,
            'code' => 'permission-groups',
            'type' => 1,
            'icon' => 'fa fa-unlock-alt',
            'parent_id' => null,
            'sort' => 8,
            'status' => 1,
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\PrivilegeGroupController', 'method' => 'index'], [
            'name_ar' => 'انواع الصلاحيات',
            'name_en' => 'Permission Groups',
            'url' => '/admin-panel/privilege-group',
            'type' => 1,
            'icon' => null,
            'parent_id' => $permission->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'permission-groups-index',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\PrivilegeGroupController', 'method' => 'create'], [
            'name_ar' => 'انشاء  نوع صلاحية',
            'name_en' => 'Create Permission Group',
            'url' => '/admin-panel/privilege-group/create',
            'type' => 1,
            'icon' => null,
            'parent_id' => $permission->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'permission-groups-create',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\PrivilegeGroupController', 'method' => 'edit'], [
            'name_ar' => 'تعديل نوع صلاحية',
            'name_en' => 'Edit Permission Group',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $permission->id,
            'sort' => 2,
            'status' => 1,
            'code' => 'permission-groups-edit',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\PrivilegeGroupController', 'method' => 'destroy'], [
            'name_ar' => 'حذف نوع صلاحية',
            'name_en' => 'Delete Permission Group',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $permission->id,
            'sort' => 3,
            'status' => 1,
            'code' => 'permission-groups-delete',
        ]);

        $setting = Privilege::updateOrCreate(['controller' => 'Panel\Auth\SettingController', 'method' => null], [
            'name_ar' => 'الاعدادات',
            'name_en' => 'Settings',
            'url' => null,
            'type' => 1,
            'icon' => 'fa fa-pencil',
            'parent_id' => null,
            'sort' => 11,
            'status' => 1,
            'code' => 'setting',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\SettingController', 'method' => 'index'], [
            'name_ar' => 'الاعدادات',
            'name_en' => 'Setting',
            'url' => '/admin-panel/setting',
            'type' => 1,
            'icon' => null,
            'parent_id' => $setting->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'setting-index',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\SettingController', 'method' => 'create'], [
            'name_ar' => 'انشاء الاعداد',
            'name_en' => 'Create Setting',
            'url' => '/admin-panel/setting/create',
            'type' => 1,
            'icon' => null,
            'parent_id' => $setting->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'setting-create',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\SettingController', 'method' => 'edit'], [
            'name_ar' => 'تعديل الاعداد',
            'name_en' => 'Edit Setting',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $setting->id,
            'sort' => 2,
            'status' => 1,
            'code' => 'setting-edit',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\SettingController', 'method' => 'destroy'], [
            'name_ar' => 'حذف الاعداد',
            'name_en' => 'Delete Setting',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $setting->id,
            'sort' => 3,
            'status' => 1,
            'code' => 'setting-delete',
        ]);

        $fixed_page = Privilege::updateOrCreate(['controller' => 'Panel\Auth\FixedPageController', 'method' => null], [
            'name_ar' => 'الصفحات الثابتة',
            'name_en' => 'Fixed Pages',
            'url' => null,
            'type' => 1,
            'icon' => 'fa fa-file',
            'parent_id' => null,
            'sort' => 12,
            'status' => 1,
            'code' => 'fixed-page',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\FixedPageController', 'method' => 'index'], [
            'name_ar' => 'الصفحات الثابتة',
            'name_en' => 'Fixed Pages',
            'url' => '/admin-panel/fixed-page',
            'type' => 1,
            'icon' => null,
            'parent_id' => $fixed_page->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'fixed-page-index',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\FixedPageController', 'method' => 'create'], [
            'name_ar' => ' انشاء صفحة ثابتة',
            'name_en' => ' Create Fixed Page',
            'url' => '/admin-panel/fixed-page/create',
            'type' => 1,
            'icon' => null,
            'parent_id' => $fixed_page->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'fixed-page-create',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\FixedPageController', 'method' => 'edit'], [
            'name_ar' => ' تعديل صفحة ثابتة',
            'name_en' => 'Edit Fixed Page',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $fixed_page->id,
            'sort' => 2,
            'status' => 1,
            'code' => 'fixed-page-edit',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\FixedPageController', 'method' => 'destroy'], [
            'name_ar' => 'حذف صفحة ثابتة',
            'name_en' => 'Delete Fixed Page',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $fixed_page->id,
            'sort' => 3,
            'status' => 1,
            'code' => 'fixed-page-delete',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\NotificationController', 'method' => 'index'], [
            'name_ar' => 'الاشعارات',
            'name_en' => 'Notifications',
            'url' => '/admin-panel/notifications',
            'type' => 1,
            'icon' => 'fa fa-bell',
            'parent_id' => null,
            'sort' => 12,
            'status' => 1,
            'code' => 'notification',
        ]);

        $group_notification = Privilege::updateOrCreate(['controller' => 'Panel\Auth\GroupNotificationController', 'method' => null], [
            'name_ar' => 'الاشعارات الجماعية',
            'name_en' => 'Group Notifications',
            'url' => null,
            'type' => 1,
            'icon' => 'fa fa-bullhorn',
            'parent_id' => null,
            'sort' => 12,
            'status' => 1,
            'code' => 'group-notification',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\GroupNotificationController', 'method' => 'index'], [
            'name_ar' => 'الاشعارات الجماعية',
            'name_en' => 'Group Notifications',
            'url' => '/admin-panel/group-notification',
            'type' => 1,
            'icon' => null,
            'parent_id' => $group_notification->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'group-notification-index',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\GroupNotificationController', 'method' => 'create'], [
            'name_ar' => ' انشاء اشعار جماعي',
            'name_en' => ' Create Group Notifications',
            'url' => '/admin-panel/group-notification/create',
            'type' => 1,
            'icon' => null,
            'parent_id' => $group_notification->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'group-notification-create',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\ContactController', 'method' => 'index'], [
            'name_ar' => 'رسائل التواصل',
            'name_en' => 'Contact Messages',
            'url' => '/admin-panel/contact',
            'type' => 1,
            'icon' => 'fa fa-phone',
            'parent_id' => null,
            'sort' => 13,
            'status' => 1,
            'code' => 'contact',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Auth\SettingsController', 'method' => 'index'], [
            'name_ar' => 'الاعدادات',
            'name_en' => 'Settings',
            'url' => '/admin-panel/settings',
            'type' => 1,
            'icon' => 'fa fa-gear',
            'parent_id' => null,
            'sort' => 14,
            'status' => 1,
            'code' => 'settings',
        ]);

        // Privilege::updateOrCreate(['controller' => 'Panel\Auth\PaymentSettingController', 'method' => 'index'], [
        //     'name_ar' => 'اعدادات الدفع', 'name_en' => 'Payment Settings', 'url' => '/admin-panel/payment-settings',
        //     'type' => 1, 'icon' => 'fa fa-credit-card', 'parent_id' => null, 'sort' => 15, 'status' => 1, 'code' => 'payment-settings',
        // ]);

        $country = Privilege::updateOrCreate(['controller' => 'Panel\Backend\CountryController', 'method' => null], [
            'name_ar' => 'الدول',
            'name_en' => 'Countries',
            'url' => null,
            'type' => 1,
            'icon' => 'fa fa-flag',
            'parent_id' => null,
            'sort' => 10,
            'status' => 1,
            'code' => 'country',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\CountryController', 'method' => 'index'], [
            'name_ar' => 'الدول',
            'name_en' => 'Countries',
            'url' => '/admin-panel/country',
            'type' => 1,
            'icon' => null,
            'parent_id' => $country->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'country-index',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\CountryController', 'method' => 'create'], [
            'name_ar' => ' انشاء دولة',
            'name_en' => ' Create Country',
            'url' => '/admin-panel/country/create',
            'type' => 2,
            'icon' => null,
            'parent_id' => $country->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'country-create',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\CountryController', 'method' => 'edit'], [
            'name_ar' => ' تعديل دولة',
            'name_en' => 'Edit Country',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $country->id,
            'sort' => 2,
            'status' => 1,
            'code' => 'country-edit',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\CountryController', 'method' => 'destroy'], [
            'name_ar' => 'حذف دولة',
            'name_en' => 'Delete Country',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $country->id,
            'sort' => 3,
            'status' => 1,
            'code' => 'country-delete',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\CityController', 'method' => 'index'], [
            'name_ar' => 'المدن',
            'name_en' => 'Cities',
            'url' => '/admin-panel/city',
            'type' => 1,
            'icon' => null,
            'parent_id' => $country->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'city-index',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\CityController', 'method' => 'create'], [
            'name_ar' => ' انشاء مدينة',
            'name_en' => ' Create City',
            'url' => '/admin-panel/city/create',
            'type' => 2,
            'icon' => null,
            'parent_id' => $country->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'city-create',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\CityController', 'method' => 'edit'], [
            'name_ar' => ' تعديل مدينة',
            'name_en' => 'Edit City',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $country->id,
            'sort' => 2,
            'status' => 1,
            'code' => 'city-edit',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\CityController', 'method' => 'destroy'], [
            'name_ar' => 'حذف مدينة',
            'name_en' => 'Delete City',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $country->id,
            'sort' => 3,
            'status' => 1,
            'code' => 'city-delete',
        ]);

        $travel_interest = Privilege::updateOrCreate(['controller' => 'Panel\Backend\TravelInterestController', 'method' => null], [
            'name_ar' => 'أنواع الرحلات',
            'name_en' => 'Travel Interests',
            'url' => null,
            'type' => 1,
            'icon' => 'fa fa-car',
            'parent_id' => null,
            'sort' => 3,
            'status' => 1,
            'code' => 'travel-interest',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\TravelInterestController', 'method' => 'index'], [
            'name_ar' => 'أنواع الرحلات',
            'name_en' => 'Travel Interests',
            'url' => '/admin-panel/travel-interest',
            'type' => 1,
            'icon' => 'fa fa-car',
            'parent_id' => $travel_interest->id,
            'sort' => 3,
            'status' => 1,
            'code' => 'travel-interest-index',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\TravelInterestController', 'method' => 'create'], [
            'name_ar' => ' انشاء نوع رحلة',
            'name_en' => ' Create Travel Interest',
            'url' => '/admin-panel/travel-interest',
            'type' => 2,
            'icon' => null,
            'parent_id' => $travel_interest->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'travel-interest-create',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\TravelInterestController', 'method' => 'edit'], [
            'name_ar' => ' تعديل نوع رحلة',
            'name_en' => 'Edit Travel Interest',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $travel_interest->id,
            'sort' => 2,
            'status' => 1,
            'code' => 'travel-interest-edit',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\TravelInterestController', 'method' => 'destroy'], [
            'name_ar' => 'حذف نوع رحلة',
            'name_en' => 'Delete Travel Interest',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $travel_interest->id,
            'sort' => 3,
            'status' => 1,
            'code' => 'travel-interest-delete',
        ]);

        $destination = Privilege::updateOrCreate(['controller' => 'Panel\Backend\DestinationController', 'method' => null], [
            'name_ar' => 'وجهات الرحلات',
            'name_en' => 'Destinations',
            'url' => null,
            'type' => 1,
            'icon' => 'fa fa-plane',
            'parent_id' => null,
            'sort' => 3,
            'status' => 1,
            'code' => 'destination',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\DestinationController', 'method' => 'index'], [
            'name_ar' => 'وجهات الرحلات',
            'name_en' => 'Destinations',
            'url' => '/admin-panel/destination',
            'type' => 1,
            'icon' => null,
            'parent_id' => $destination->id,
            'sort' => 3,
            'status' => 1,
            'code' => 'destination-index',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\DestinationController', 'method' => 'create'], [
            'name_ar' => ' انشاء وجهة رحلة',
            'name_en' => ' Create Destination',
            'url' => '/admin-panel/destination',
            'type' => 2,
            'icon' => null,
            'parent_id' => $destination->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'destination-create',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\DestinationController', 'method' => 'edit'], [
            'name_ar' => ' تعديل وجهة رحلة',
            'name_en' => 'Edit Destination',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $destination->id,
            'sort' => 2,
            'status' => 1,
            'code' => 'destination-edit',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\DestinationController', 'method' => 'destroy'], [
            'name_ar' => 'حذف وجهة رحلة',
            'name_en' => 'Delete Destination',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $destination->id,
            'sort' => 3,
            'status' => 1,
            'code' => 'destination-delete',
        ]);

        $page_content = Privilege::updateOrCreate(['controller' => 'Panel\Backend\PageContentController', 'method' => null], [
            'name_ar' => ' محتوي الصفحات',
            'name_en' => 'Pages Contents',
            'url' => null,
            'type' => 1,
            'icon' => 'fa fa-file',
            'parent_id' => null,
            'sort' => 12,
            'status' => 1,
            'code' => 'page-content',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\PageContentController', 'method' => 'index'], [
            'name_ar' => 'محتوي الصفحات',
            'name_en' => 'Pages Contents',
            'url' => '/admin-panel/page-content',
            'type' => 1,
            'icon' => null,
            'parent_id' => $page_content->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'page-content-index',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\PageContentController', 'method' => 'create'], [
            'name_ar' => ' انشاء محتوي الصفحة',
            'name_en' => ' Create Page Content',
            'url' => '/admin-panel/page-content/create',
            'type' => 1,
            'icon' => null,
            'parent_id' => $page_content->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'page-content-create',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\PageContentController', 'method' => 'edit'], [
            'name_ar' => ' تعديل محتوي الصفحة',
            'name_en' => 'Edit Page Content',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $page_content->id,
            'sort' => 2,
            'status' => 1,
            'code' => 'page-content-edit',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\PageContentController', 'method' => 'destroy'], [
            'name_ar' => 'حذف محتوي الصفحة',
            'name_en' => 'Delete Page Content',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $page_content->id,
            'sort' => 3,
            'status' => 1,
            'code' => 'page-content-delete',
        ]);

        $reservation = Privilege::updateOrCreate(['controller' => 'Panel\Backend\ReservationController', 'method' => null], [
            'name_ar' => 'حجوزات',
            'name_en' => 'Reservations',
            'url' => null,
            'type' => 1,
            'icon' => 'fa fa-calendar',
            'parent_id' => null,
            'sort' => 4,
            'status' => 1,
            'code' => 'reservation',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\ReservationController', 'method' => 'index'], [
            'name_ar' => 'حجوزات',
            'name_en' => 'Reservations',
            'url' => '/admin-panel/reservations',
            'type' => 1,
            'icon' => null,
            'parent_id' => $reservation->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'reservation-index',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\ReservationController', 'method' => 'hotel'], [
            'name_ar' => 'حجوزات الفنادق',
            'name_en' => 'Hotel Reservations',
            'url' => '/admin-panel/hotel-reservations',
            'type' => 1,
            'icon' => null,
            'parent_id' => $reservation->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'reservation-hotel',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\ReservationController', 'method' => 'flight'], [
            'name_ar' => 'حجوزات الطيران',
            'name_en' => 'Flight Reservations',
            'url' => '/admin-panel/flight-reservations',
            'type' => 1,
            'icon' => null,
            'parent_id' => $reservation->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'reservation-flight',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\ReservationController', 'method' => 'hotelAndFlight'], [
            'name_ar' => 'حجوزات الفنادق والطيران',
            'name_en' => 'Hotel and Flight Reservations',
            'url' => '/admin-panel/hotel-and-flight-reservations',
            'type' => 1,
            'icon' => null,
            'parent_id' => $reservation->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'reservation-hotel-and-flight',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\OperationController', 'method' => 'index'], [
            'name_ar' => 'العمليات',
            'name_en' => 'Operations',
            'url' => '/admin-panel/operations',
            'type' => 1,
            'icon' => 'fa fa-cogs',
            'parent_id' => null,
            'sort' => 1,
            'status' => 1,
            'code' => 'operation',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\CacheHotelController', 'method' => 'index'], [
            'name_ar' => 'الفنادق المحفوظة',
            'name_en' => 'Cached Hotels',
            'url' => '/admin-panel/cached-hotels',
            'type' => 1,
            'icon' => 'fa fa-cogs',
            'parent_id' => null,
            'sort' => 1,
            'status' => 1,
            'code' => 'cache-hotel',
        ]);

        $ai_page = Privilege::updateOrCreate(['controller' => 'Panel\Backend\AiPlannerContentController', 'method' => null], [
            'name_ar' => '  صفحة دليله',
            'name_en' => 'Dalelah Page',
            'url' => null,
            'type' => 1,
            'icon' => 'fa fa-braille',
            'parent_id' => null,
            'sort' => 13,
            'status' => 1,
            'code' => 'ai-planner-sections',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\AiPlannerContentController', 'method' => 'index'], [
            'name_ar' => ' صفحة ديلية',
            'name_en' => 'Dalelah Page',
            'url' => '/admin-panel/ai-planner-sections',
            'type' => 1,
            'icon' => null,
            'parent_id' => $ai_page->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'ai-planner-sections-index',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\AiPlannerContentController', 'method' => 'create'], [
            'name_ar' => ' انشاء عنصر في صفحة دليله',
            'name_en' => ' Create Item in Dalelah Page',
            'url' => '/admin-panel/ai-planner-sections/create',
            'type' => 1,
            'icon' => null,
            'parent_id' => $ai_page->id,
            'sort' => 1,
            'status' => 1,
            'code' => 'ai-planner-sections-create',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\AiPlannerContentController', 'method' => 'edit'], [
            'name_ar' => ' تعديل عنصر في صفحة دليله',
            'name_en' => 'Edit Item in Dalelah Page',
            'url' => 'null',
            'type' => 2,
            'icon' => null,
            'parent_id' => $ai_page->id,
            'sort' => 2,
            'status' => 1,
            'code' => 'ai-planner-sections-edit',
        ]);

        Privilege::updateOrCreate(['controller' => 'Panel\Backend\AiPlannerContentController', 'method' => 'destroy'], [
            'name_ar' => 'حذف صفحة ثابتة',
            'name_en' => 'Delete Fixed Page',
            'url' => null,
            'type' => 2,
            'icon' => null,
            'parent_id' => $ai_page->id,
            'sort' => 3,
            'status' => 1,
            'code' => 'ai-planner-sections-delete',
        ]);

    }
}
