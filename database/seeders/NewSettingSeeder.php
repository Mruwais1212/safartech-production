<?php

namespace Database\Seeders;

use App\Models\Panel\Setting;
use App\Models\Panel\SettingType;
use Illuminate\Database\Seeder;

class NewSettingSeeder extends Seeder
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

        // //////////////////////////////////////////////////////////
        Setting::updateOrCreate([
            'name_ar' => 'اسم الشركة ',
            'name_en' => 'Company name',
            'code' => 'company_name',
            'setting_type_id' => 3,
            'value' => 'سفر تك',
            'note_ar' => 'من فضلك ادخل اسم الشركة',
            'note_en' => 'Please enter the company name',
            'input_type' => 'text',
            'sort' => 9,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'رقم السجل التجاري للشركة ',
            'name_en' => 'Company\'s commercial registration number',
            'code' => 'commercial_registration',
            'setting_type_id' => 3,
            'value' => '000000000000000',
            'note_ar' => 'من فضلك ادخل رقم السجل التجاري ',
            'note_en' => 'Please enter the commercial registration number',
            'input_type' => 'text',
            'sort' => 10,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'نسبة الربح بالنسبة للفنادق',
            'name_en' => 'Profit percentage for hotels',
            'code' => 'hotel_profit',
            'setting_type_id' => 3,
            'value' => '12',
            'note_ar' => 'من فضلك ادخل نسبة الربح بالنسبة للفنادق',
            'note_en' => 'Please enter the Profit percentage for hotels',
            'input_type' => 'text',
            'sort' => 11,
            'status' => 1,
        ]);

        Setting::updateOrCreate([
            'name_ar' => 'نسبة الربح بالنسبة لرحلات الطيران',
            'name_en' => 'Profit percentage for flight trips',
            'code' => 'flight_profit',
            'setting_type_id' => 3,
            'value' => '12',
            'note_ar' => 'من فضلك ادخل نسبة الربح بالنسبة لرحلات الطيران',
            'note_en' => 'Please enter the Profit percentage for flight trips',
            'input_type' => 'text',
            'sort' => 12,
            'status' => 1,
        ]);

    }
}
