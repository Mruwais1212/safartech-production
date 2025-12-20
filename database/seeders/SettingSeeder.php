<?php

namespace Database\Seeders;

use App\Models\Panel\Setting;
use App\Models\Panel\SettingType;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        SettingType::updateOrCreate(['name_ar' => 'بيانات التواصل', 'name_en' => ' Contact information']);
        SettingType::updateOrCreate(['name_ar' => 'الرسوم', 'name_en' => 'Fees']);
        SettingType::updateOrCreate(['name_ar' => 'الموقع', 'name_en' => 'Site']);
        SettingType::updateOrCreate(['name_ar' => 'روابط التواصل الاجتماعي', 'name_en' => 'Social links']);
        SettingType::updateOrCreate(['name_ar' => 'الخدمات', 'name_en' => 'Services']);

        ////////////////////////////////////////////////////////////

        Setting::updateOrCreate([
            'name_ar' => 'رقم الهاتف',
            'name_en' => 'Phone Number',
            'code' => 'phone',
            'setting_type_id' => 1,
            'value' => '0123456789',
            'note_ar' => 'من فضلك ادخل رقم الجوال',
            'note_en' => 'Please enter the mobile number',
            'input_type' => 'number',
            'sort' => 0,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => ' رقم الواتساب',
            'name_en' => 'WhatsApp Number',
            'code' => 'whatsapp',
            'setting_type_id' => 1,
            'value' => '0123456789',
            'note_ar' => 'من فضلك ادخل رقم الواتساب',
            'note_en' => 'Please enter the WhatsApp number',
            'input_type' => 'number',
            'sort' => 1,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'البريد الالكتروني',
            'name_en' => 'Email Address',
            'code' => 'email',
            'setting_type_id' => 1,
            'value' => 'admin@gmail.com',
            'note_ar' => 'من فضلك ادخل البريد الالكتروني',
            'note_en' => 'Please enter the email address',
            'input_type' => 'email',
            'sort' => 2,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'العنوان',
            'name_en' => 'Address',
            'code' => 'address',
            'setting_type_id' => 1,
            'value' => 'Address',
            'note_ar' => 'من فضلك ادخل العنوان',
            'note_en' => 'Please enter the address',
            'input_type' => 'text',
            'sort' => 3,
            'status' => 1,
        ]);

        ////////////////////////////////////////////////////////////
        Setting::updateOrCreate([
            'name_ar' => 'لوجو الموقع',
            'name_en' => 'Logo Website',
            'code' => 'logo',
            'setting_type_id' => 3,
            'value' => 'setting-logo.png',
            'note_ar' => 'من فضلك ادخل صورة اللوجو للموقع',
            'note_en' => 'Please enter the logo website',
            'input_type' => 'file',
            'sort' => 0,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'ايقونة الموقع',
            'name_en' => 'Website Icon',
            'code' => 'website_icon',
            'setting_type_id' => 3,
            'value' => 'website_icon.png',
            'note_ar' => 'من فضلك ادخل صورة ايقونة الموقع',
            'note_en' => 'Please enter the website icon',
            'input_type' => 'file',
            'sort' => 1,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'صورة هيدر الفاتورة',
            'name_en' => 'Header Invoice Photo',
            'code' => 'header_invoice_photo',
            'setting_type_id' => 3,
            'value' => 'header_invoice_photo.png',
            'note_ar' => 'من فضلك ادخل صورة هيدر الفاتورة',
            'note_en' => 'Please enter the header invoice photo',
            'input_type' => 'file',
            'sort' => 2,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'صورة فوتر الفاتورة',
            'name_en' => 'Footer Invoice Photo',
            'code' => 'footer_invoice_photo',
            'setting_type_id' => 3,
            'value' => 'footer_invoice_photo.png',
            'note_ar' => 'من فضلك ادخل صورة فوتر الفاتورة',
            'note_en' => 'Please enter the footer invoice photo',
            'input_type' => 'file',
            'sort' => 3,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'اسم الموقع',
            'name_en' => 'Website Name',
            'code' => 'website_name',
            'setting_type_id' => 3,
            'value' => 'Safer Tech',
            'note_ar' => 'من فضلك ادخل صورة اسم الموقع',
            'note_en' => 'Please enter the website name',
            'input_type' => 'text',
            'sort' => 4,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'وصف الموقع',
            'name_en' => 'Website Description',
            'code' => 'website_description',
            'setting_type_id' => 3,
            'value' => 'Safer Tech',
            'note_ar' => 'من فضلك ادخل وصف الموقع',
            'note_en' => 'Please enter the website description',
            'input_type' => 'textarea',
            'sort' => 5,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'الكلمات المفتاحية',
            'name_en' => 'Keywords',
            'code' => 'keywords',
            'setting_type_id' => 3,
            'value' => 'Safer Tech',
            'note_ar' => 'من فضلك ادخل الكلمات المفتاحية',
            'note_en' => 'Please enter the keywords',
            'input_type' => 'tag',
            'sort' => 6,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'لون لوحة التحكم',
            'name_en' => ' Admin Panel Color',
            'code' => 'admin_panel_color',
            'setting_type_id' => 3,
            'value' => '#007063',
            'note_ar' => 'من فضلك ادخل لون لوحة التحكم',
            'note_en' => 'Please enter the admin panel color',
            'input_type' => 'color',
            'sort' => 6,
            'status' => 3,
        ]);

        // Setting::updateOrCreate([
        //     'name_ar' => 'هل يمكنك تفعيل الدفع الالكتروني',
        //     'name_en' => 'Do you want to activate electronic payment',
        //     'code' => 'enable_or_disable_online_payment',
        //     'setting_type_id' => 3,
        //     'value' => 0, 'note_ar' => 'من فضلك قم بالاختيار نعم او لا',
        //     'note_en' => 'Please select yes or no',
        //     'input_type' => 'radio', 'sort' => 7, 'status' => 1,
        // ]);

        Setting::updateOrCreate([
            'name_ar' => 'عدد الايام الذي يتم فيها تجديد كاش الفنادق ',
            'name_en' => 'Number of days to renew the hotel cache',
            'code' => 'number_of_days_to_renew_hotel_cache',
            'setting_type_id' => 3,
            'value' => 90,
            'note_ar' => 'من فضلك ادخل عدد الايام الذي يتم فيها تجديد كاش الفنادق ',
            'note_en' => 'Please enter the number of days to renew the hotel cache',
            'input_type' => 'text',
            'sort' => 7,
            'status' => 1,
        ]);

        /////////////////////////////////////////////////////////
        Setting::updateOrCreate([
            'name_ar' => 'لينك الفيس بوك ',
            'name_en' => 'Facebook Url',
            'code' => 'facebook',
            'setting_type_id' => 4,
            'value' => 'https://www.facebook.com/',
            'note_ar' => 'من فضلك ادخل رابط الفيس بوك إن وجد',
            'note_en' => 'Please enter the facebook url if found',
            'input_type' => 'url',
            'sort' => 0,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'لينك تويتر ',
            'name_en' => 'Twitter Url',
            'code' => 'twitter',
            'setting_type_id' => 4,
            'value' => 'https://twitter.com/',
            'note_ar' => 'من فضلك ادخل رابط تويتر إن وجد',
            'note_en' => 'Please enter the twitter url if found',
            'input_type' => 'url',
            'sort' => 1,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'لينك انستجرام ',
            'name_en' => 'Instagram Url',
            'code' => 'instagram',
            'setting_type_id' => 4,
            'value' => 'https://www.instagram.com/',
            'note_ar' => 'من فضلك ادخل رابط انستجرام إن وجد',
            'note_en' => 'Please enter the instagram url if found',
            'input_type' => 'url',
            'sort' => 2,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'لينك يوتيوب ',
            'name_en' => 'Youtube Url',
            'code' => 'youtube',
            'setting_type_id' => 4,
            'value' => 'https://www.youtube.com/',
            'note_ar' => 'من فضلك ادخل رابط يوتيوب إن وجد',
            'note_en' => 'Please enter the youtube url if found',
            'input_type' => 'url',
            'sort' => 3,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'لينك لينكدان ',
            'name_en' => 'Linkedin Url',
            'code' => 'linkedin',
            'setting_type_id' => 4,
            'value' => 'https://www.linkedin.com/',
            'note_ar' => 'من فضلك ادخل رابط لينكدان إن وجد',
            'note_en' => 'Please enter the linkedin url if found',
            'input_type' => 'url',
            'sort' => 4,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'لينك سناب شات ',
            'name_en' => 'Snapchat Url',
            'code' => 'snapchat',
            'setting_type_id' => 4,
            'value' => 'https://www.snapchat.com/',
            'note_ar' => 'من فضلك ادخل رابط سناب شات إن وجد',
            'note_en' => 'Please enter the snapchat url if found',
            'input_type' => 'url',
            'sort' => 5,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'رابط تحميل الاندرويد',
            'name_en' => 'Android Download Link',
            'code' => 'android_download_link',
            'setting_type_id' => 4,
            'value' => 'https://play.google.com/store/apps/details?id=com.cab.cab',
            'note_ar' => 'من فضلك ادخل رابط تحميل الاندرويد إن وجد',
            'note_en' => 'Please enter the android download link if found',
            'input_type' => 'url',
            'sort' => 5,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'رابط تحميل الايفون',
            'name_en' => 'IOS Download Link',
            'code' => 'ios_download_link',
            'setting_type_id' => 4,
            'value' => 'https://apps.apple.com/us/app/cab-cab/id1565336033',
            'note_ar' => 'من فضلك ادخل رابط تحميل الايفون إن وجد',
            'note_en' => 'Please enter the ios download link if found',
            'input_type' => 'url',
            'sort' => 6,
            'status' => 1,
        ]);

        ///////////////////////////////////////////////////////// AI Planner Static Section
        Setting::updateOrCreate([
            'name_ar' => '  عنوان السكشن الرئيسي في صفحة دليله باللغة العربية',
            'name_en' => 'Title Of Main Section In Dalelah Page In Arabic',
            'code' => 'ai_trip_planner_title_arabic',
            'setting_type_id' => 5,
            'value' => '',
            'note_ar' => 'من فضلك ادخل عنوان السكشن',
            'note_en' => 'Please enter the section title',
            'input_type' => 'text',
            'sort' => 0,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => '  نص السكشن الرئيسي في صفحة دليله بالعربية',
            'name_en' => 'Content Of Main Section In Dalelah Page In Arabic',
            'code' => 'ai_trip_planner_content_arabic',
            'setting_type_id' => 5,
            'value' => '',
            'note_ar' => 'من فضلك ادخل نص السكشن',
            'note_en' => 'Please enter the section content',
            'input_type' => 'text',
            'sort' => 1,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => '  عنوان السكشن الرئيسي في صفحة دليله باللغة الانجليزية',
            'name_en' => 'Title Of Main Section In Dalelah Page in english',
            'code' => 'ai_trip_planner_title_english',
            'setting_type_id' => 5,
            'value' => '',
            'note_ar' => 'من فضلك ادخل عنوان السكشن',
            'note_en' => 'Please enter the section title',
            'input_type' => 'text',
            'sort' => 2,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => '  نص السكشن الرئيسي في صفحة دليله باللغة الانجليزية',
            'name_en' => 'Content Of Main Section In Dalelah Page in english',
            'code' => 'ai_trip_planner_content_english',
            'setting_type_id' => 5,
            'value' => '',
            'note_ar' => 'من فضلك ادخل نص السكشن',
            'note_en' => 'Please enter the section content',
            'input_type' => 'text',
            'sort' => 3,
            'status' => 1,
        ]);
    }
}
