<?php

namespace Database\Seeders;

use App\Models\Panel\Setting;
use Illuminate\Database\Seeder;

class refundSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate([
            'name_ar' => 'مراجعة الطلب الملغي قبل الاسترداد',
            'name_en' => 'Review cancelled order before refund',
            'code' => 'Review_cancelled',
            'setting_type_id' => 3,
            'value' => '0',
            'note_ar' => 'من فضلك اختر قيمة',
            'note_en' => 'Please select a value',
            'input_type' => 'radio',
            'sort' => 10,
            'status' => 1,
        ]);
    }
}
