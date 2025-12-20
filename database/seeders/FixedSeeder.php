<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\PageContent;
use App\Models\Panel\FixedPage;
use App\Models\Panel\PaymentSetting;
use App\Models\TravelInterest;
use Illuminate\Database\Seeder;

class FixedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $countries = [
            ['name_ar' => 'السعودية', 'name_en' => 'Saudi Arabia', 'code' => 'SA', 'cities' => [
                ['name_ar' => 'الرياض', 'name_en' => 'Riyadh'],
                ['name_ar' => 'جدة', 'name_en' => 'Jeddah'],
                ['name_ar' => 'مكة', 'name_en' => 'Mecca'],
                ['name_ar' => 'المدينة المنورة', 'name_en' => 'Medina'],
                ['name_ar' => 'الدمام', 'name_en' => 'Dammam'],
                ['name_ar' => 'الخبر', 'name_en' => 'Khobar'],
                ['name_ar' => 'الظهران', 'name_en' => 'Dhahran'],
                ['name_ar' => 'تبوك', 'name_en' => 'Tabuk'],
                ['name_ar' => 'حائل', 'name_en' => 'Hail'],
                ['name_ar' => 'بريدة', 'name_en' => 'Buraidah'],
                ['name_ar' => 'القطيف', 'name_en' => 'Qatif'],
                ['name_ar' => 'أبها', 'name_en' => 'Abha'],
                ['name_ar' => 'خميس مشيط', 'name_en' => 'Khamis Mushait'],
                ['name_ar' => 'جازان', 'name_en' => 'Jazan'],
                ['name_ar' => 'نجران', 'name_en' => 'Najran'],
                ['name_ar' => 'سكاكا', 'name_en' => 'Sakakah'],
                ['name_ar' => 'الباحة', 'name_en' => 'Al Bahah'],
                ['name_ar' => 'عرعر', 'name_en' => 'Arar'],
            ]],
            ['name_ar' => 'مصر', 'name_en' => 'Egypt', 'code' => 'EG', 'cities' => [
                ['name_ar' => 'القاهرة', 'name_en' => 'Cairo'],
                ['name_ar' => 'الإسكندرية', 'name_en' => 'Alexandria'],
                ['name_ar' => 'الجيزة', 'name_en' => 'Giza'],
                ['name_ar' => 'بورسعيد', 'name_en' => 'Port Said'],
                ['name_ar' => 'السويس', 'name_en' => 'Suez'],
                ['name_ar' => 'الأقصر', 'name_en' => 'Luxor'],
                ['name_ar' => 'أسوان', 'name_en' => 'Aswan'],
                ['name_ar' => 'أسيوط', 'name_en' => 'Assiut'],
                ['name_ar' => 'المنصورة', 'name_en' => 'Mansoura'],
                ['name_ar' => 'الفيوم', 'name_en' => 'Faiyum'],
                ['name_ar' => 'بني سويف', 'name_en' => 'Beni Suef'],
                ['name_ar' => 'المنيا', 'name_en' => 'Minya'],
                ['name_ar' => 'سوهاج', 'name_en' => 'Sohag'],
                ['name_ar' => 'قنا', 'name_en' => 'Qena'],
                ['name_ar' => 'الغردقة', 'name_en' => 'Hurghada'],
                ['name_ar' => 'شرم الشيخ', 'name_en' => 'Sharm El Sheikh'],
                ['name_ar' => 'دمياط', 'name_en' => 'Damietta'],
                ['name_ar' => 'الإسماعيلية', 'name_en' => 'Ismailia'],
                ['name_ar' => 'الشرقية', 'name_en' => 'Zagazig'],
                ['name_ar' => 'كفر الشيخ', 'name_en' => 'Kafr El Sheikh'],
                ['name_ar' => 'مطروح', 'name_en' => 'Marsa Matruh'],
                ['name_ar' => 'البحيرة', 'name_en' => 'Damanhur'],
                ['name_ar' => 'الغربية', 'name_en' => 'Tanta'],
                ['name_ar' => 'الدقهلية', 'name_en' => 'El-Mahalla El-Kubra'],
                ['name_ar' => 'القليوبية', 'name_en' => 'Banha'],
                ['name_ar' => 'السويس', 'name_en' => 'Suez'],
                ['name_ar' => 'البحر الأحمر', 'name_en' => 'Safaga'],
            ]],
            ['name_ar' => 'الجزائر', 'name_en' => 'Algeria', 'code' => 'DZ', 'cities' => [
                ['name_ar' => 'الجزائر', 'name_en' => 'Algiers'],
                ['name_ar' => 'وهران', 'name_en' => 'Oran'],
                ['name_ar' => 'قسنطينة', 'name_en' => 'Constantine'],
                ['name_ar' => 'عنابة', 'name_en' => 'Annaba'],
                ['name_ar' => 'البليدة', 'name_en' => 'Blida'],
                ['name_ar' => 'سطيف', 'name_en' => 'Setif'],
                ['name_ar' => 'بجاية', 'name_en' => 'Bejaia'],
                ['name_ar' => 'باتنة', 'name_en' => 'Batna'],
                ['name_ar' => 'تيزي وزو', 'name_en' => 'Tizi Ouzou'],
                ['name_ar' => 'تلمسان', 'name_en' => 'Tlemcen'],
                ['name_ar' => 'الجلفة', 'name_en' => 'Djelfa'],
                ['name_ar' => 'المدية', 'name_en' => 'Medea'],
                ['name_ar' => 'سيدي بلعباس', 'name_en' => 'Sidi Bel Abbes'],
                ['name_ar' => 'بسكرة', 'name_en' => 'Biskra'],
                ['name_ar' => 'تبسة', 'name_en' => 'Tebessa'],
                ['name_ar' => 'سكيكدة', 'name_en' => 'Skikda'],
                ['name_ar' => 'الشلف', 'name_en' => 'Chlef'],
                ['name_ar' => 'ورقلة', 'name_en' => 'Ouargla'],
                ['name_ar' => 'غرداية', 'name_en' => 'Ghardaia'],
                ['name_ar' => 'عين تموشنت', 'name_en' => 'Ain Temouchent'],
                ['name_ar' => 'معسكر', 'name_en' => 'Mascara'],
                ['name_ar' => 'تيارت', 'name_en' => 'Tiaret'],
                ['name_ar' => 'الأغواط', 'name_en' => 'Laghouat'],
                ['name_ar' => 'سوق أهراس', 'name_en' => 'Souk Ahras'],
                ['name_ar' => 'خنشلة', 'name_en' => 'Khenchela'],
                ['name_ar' => 'ميلة', 'name_en' => 'Mila'],
                ['name_ar' => 'برج بوعريريج', 'name_en' => 'Bordj Bou Arreridj'],
                ['name_ar' => 'تندوف', 'name_en' => 'Tindouf'],
                ['name_ar' => 'عين الدفلى', 'name_en' => 'Ain Defla'],
                ['name_ar' => 'الأغواط', 'name_en' => 'Laghouat'],
            ]],
            ['name_ar' => 'البحرين', 'name_en' => 'Bahrain', 'code' => 'BH'],
            ['name_ar' => 'العراق', 'name_en' => 'Iraq', 'code' => 'IQ'],
            ['name_ar' => 'الأردن', 'name_en' => 'Jordan', 'code' => 'JO'],
            ['name_ar' => 'الكويت', 'name_en' => 'Kuwait', 'code' => 'KW'],
            ['name_ar' => 'لبنان', 'name_en' => 'Lebanon', 'code' => 'LB'],
            ['name_ar' => 'ليبيا', 'name_en' => 'Libya', 'code' => 'LY'],
            ['name_ar' => 'المغرب', 'name_en' => 'Morocco', 'code' => 'MA'],
            ['name_ar' => 'عمان', 'name_en' => 'Oman', 'code' => 'OM'],
            ['name_ar' => 'فلسطين', 'name_en' => 'Palestine', 'code' => 'PS'],
            ['name_ar' => 'قطر', 'name_en' => 'Qatar', 'code' => 'QA'],
            ['name_ar' => 'الصومال', 'name_en' => 'Somalia', 'code' => 'SO'],
            ['name_ar' => 'السودان', 'name_en' => 'Sudan', 'code' => 'SD'],
            ['name_ar' => 'سوريا', 'name_en' => 'Syria', 'code' => 'SY'],
            ['name_ar' => 'تونس', 'name_en' => 'Tunisia', 'code' => 'TN'],
            ['name_ar' => 'الإمارات العربية المتحدة', 'name_en' => 'United Arab Emirates', 'code' => 'AE'],
            ['name_ar' => 'اليمن', 'name_en' => 'Yemen', 'code' => 'YE'],
            ['name_ar' => 'أفغانستان', 'name_en' => 'Afghanistan', 'code' => 'AF'],
            ['name_ar' => 'ألبانيا', 'name_en' => 'Albania', 'code' => 'AL'],
            ['name_ar' => 'أندورا', 'name_en' => 'Andorra', 'code' => 'AD'],
            ['name_ar' => 'أنغولا', 'name_en' => 'Angola', 'code' => 'AO'],
            ['name_ar' => 'أنتيغوا وباربودا', 'name_en' => 'Antigua and Barbuda', 'code' => 'AG'],
            ['name_ar' => 'الأرجنتين', 'name_en' => 'Argentina', 'code' => 'AR'],
            ['name_ar' => 'أرمينيا', 'name_en' => 'Armenia', 'code' => 'AM'],
            ['name_ar' => 'أستراليا', 'name_en' => 'Australia', 'code' => 'AU'],
            ['name_ar' => 'النمسا', 'name_en' => 'Austria', 'code' => 'AT'],
            ['name_ar' => 'أذربيجان', 'name_en' => 'Azerbaijan', 'code' => 'AZ'],
            ['name_ar' => 'باهاماس', 'name_en' => 'Bahamas', 'code' => 'BS'],
            ['name_ar' => 'بنغلاديش', 'name_en' => 'Bangladesh', 'code' => 'BD'],
            ['name_ar' => 'باربادوس', 'name_en' => 'Barbados', 'code' => 'BB'],
            ['name_ar' => 'بيلاروسيا', 'name_en' => 'Belarus', 'code' => 'BY'],
            ['name_ar' => 'بلجيكا', 'name_en' => 'Belgium', 'code' => 'BE'],
            ['name_ar' => 'بليز', 'name_en' => 'Belize', 'code' => 'BZ'],
            ['name_ar' => 'بنين', 'name_en' => 'Benin', 'code' => 'BJ'],
            ['name_ar' => 'بوتان', 'name_en' => 'Bhutan', 'code' => 'BT'],
            ['name_ar' => 'بوليفيا', 'name_en' => 'Bolivia', 'code' => 'BO'],
            ['name_ar' => 'البوسنة والهرسك', 'name_en' => 'Bosnia and Herzegovina', 'code' => 'BA'],
            ['name_ar' => 'بوتسوانا', 'name_en' => 'Botswana', 'code' => 'BW'],
            ['name_ar' => 'البرازيل', 'name_en' => 'Brazil', 'code' => 'BR'],
            ['name_ar' => 'بروناي', 'name_en' => 'Brunei', 'code' => 'BN'],
            ['name_ar' => 'بلغاريا', 'name_en' => 'Bulgaria', 'code' => 'BG'],
            ['name_ar' => 'بوركينا فاسو', 'name_en' => 'Burkina Faso', 'code' => 'BF'],
            ['name_ar' => 'بوروندي', 'name_en' => 'Burundi', 'code' => 'BI'],
            ['name_ar' => 'كمبوديا', 'name_en' => 'Cambodia', 'code' => 'KH'],
            ['name_ar' => 'الكاميرون', 'name_en' => 'Cameroon', 'code' => 'CM'],
            ['name_ar' => 'كندا', 'name_en' => 'Canada', 'code' => 'CA'],
            ['name_ar' => 'الرأس الأخضر', 'name_en' => 'Cape Verde', 'code' => 'CV'],
            ['name_ar' => 'جمهورية أفريقيا الوسطى', 'name_en' => 'Central African Republic', 'code' => 'CF'],
            ['name_ar' => 'تشاد', 'name_en' => 'Chad', 'code' => 'TD'],
            ['name_ar' => 'تشيلي', 'name_en' => 'Chile', 'code' => 'CL'],
            ['name_ar' => 'الصين', 'name_en' => 'China', 'code' => 'CN'],
            ['name_ar' => 'كولومبيا', 'name_en' => 'Colombia', 'code' => 'CO'],
            ['name_ar' => 'جزر القمر', 'name_en' => 'Comoros', 'code' => 'KM'],
            ['name_ar' => 'الكونغو', 'name_en' => 'Congo', 'code' => 'CG'],
            ['name_ar' => 'كوستاريكا', 'name_en' => 'Costa Rica', 'code' => 'CR'],
            ['name_ar' => 'كرواتيا', 'name_en' => 'Croatia', 'code' => 'HR'],
            ['name_ar' => 'كوبا', 'name_en' => 'Cuba', 'code' => 'CU'],
            ['name_ar' => 'قبرص', 'name_en' => 'Cyprus', 'code' => 'CY'],
            ['name_ar' => 'التشيك', 'name_en' => 'Czech Republic', 'code' => 'CZ'],
            ['name_ar' => 'جمهورية الكونغو الديمقراطية', 'name_en' => 'Democratic Republic of the Congo', 'code' => 'CD'],
            ['name_ar' => 'الدنمارك', 'name_en' => 'Denmark', 'code' => 'DK'],
            ['name_ar' => 'جيبوتي', 'name_en' => 'Djibouti', 'code' => 'DJ'],
            ['name_ar' => 'دومينيكا', 'name_en' => 'Dominica', 'code' => 'DM'],
            ['name_ar' => 'جمهورية الدومينيكان', 'name_en' => 'Dominican Republic', 'code' => 'DO'],
            ['name_ar' => 'تيمور الشرقية', 'name_en' => 'East Timor', 'code' => 'TL'],
            ['name_ar' => 'الإكوادور', 'name_en' => 'Ecuador', 'code' => 'EC'],
            ['name_ar' => 'السلفادور', 'name_en' => 'El Salvador', 'code' => 'SV'],
            ['name_ar' => 'غينيا الإستوائية', 'name_en' => 'Equatorial Guinea', 'code' => 'GQ'],
            ['name_ar' => 'إريتريا', 'name_en' => 'Eritrea', 'code' => 'ER'],
            ['name_ar' => 'إستونيا', 'name_en' => 'Estonia', 'code' => 'EE'],
            ['name_ar' => 'إسواتيني', 'name_en' => 'Eswatini', 'code' => 'SZ'],
            ['name_ar' => 'إثيوبيا', 'name_en' => 'Ethiopia', 'code' => 'ET'],
            ['name_ar' => 'فيجي', 'name_en' => 'Fiji', 'code' => 'FJ'],
            ['name_ar' => 'فنلندا', 'name_en' => 'Finland', 'code' => 'FI'],
            ['name_ar' => 'فرنسا', 'name_en' => 'France', 'code' => 'FR'],
            ['name_ar' => 'الغابون', 'name_en' => 'Gabon', 'code' => 'GA'],
            ['name_ar' => 'غامبيا', 'name_en' => 'Gambia', 'code' => 'GM'],
            ['name_ar' => 'جورجيا', 'name_en' => 'Georgia', 'code' => 'GE'],
            ['name_ar' => 'ألمانيا', 'name_en' => 'Germany', 'code' => 'DE'],
            ['name_ar' => 'غانا', 'name_en' => 'Ghana', 'code' => 'GH'],
            ['name_ar' => 'اليونان', 'name_en' => 'Greece', 'code' => 'GR'],
            ['name_ar' => 'غرينادا', 'name_en' => 'Grenada', 'code' => 'GD'],
            ['name_ar' => 'غواتيمالا', 'name_en' => 'Guatemala', 'code' => 'GT'],
            ['name_ar' => 'غينيا', 'name_en' => 'Guinea', 'code' => 'GN'],
            ['name_ar' => 'غينيا بيساو', 'name_en' => 'Guinea-Bissau', 'code' => 'GW'],
            ['name_ar' => 'غيانا', 'name_en' => 'Guyana', 'code' => 'GY'],
            ['name_ar' => 'هايتي', 'name_en' => 'Haiti', 'code' => 'HT'],
            ['name_ar' => 'هندوراس', 'name_en' => 'Honduras', 'code' => 'HN'],
            ['name_ar' => 'المجر', 'name_en' => 'Hungary', 'code' => 'HU'],
            ['name_ar' => 'آيسلندا', 'name_en' => 'Iceland', 'code' => 'IS'],
            ['name_ar' => 'الهند', 'name_en' => 'India', 'code' => 'IN'],
            ['name_ar' => 'إندونيسيا', 'name_en' => 'Indonesia', 'code' => 'ID'],
            ['name_ar' => 'إيران', 'name_en' => 'Iran', 'code' => 'IR'],
            ['name_ar' => 'إيطاليا', 'name_en' => 'Italy', 'code' => 'IT'],
            ['name_ar' => 'جامايكا', 'name_en' => 'Jamaica', 'code' => 'JM'],
            ['name_ar' => 'اليابان', 'name_en' => 'Japan', 'code' => 'JP'],
            ['name_ar' => 'كازاخستان', 'name_en' => 'Kazakhstan', 'code' => 'KZ'],
            ['name_ar' => 'كينيا', 'name_en' => 'Kenya', 'code' => 'KE'],
            ['name_ar' => 'كيريباتي', 'name_en' => 'Kiribati', 'code' => 'KI'],
            ['name_ar' => 'كوريا الشمالية', 'name_en' => 'North Korea', 'code' => 'KP'],
            ['name_ar' => 'كوريا الجنوبية', 'name_en' => 'South Korea', 'code' => 'KR'],
            ['name_ar' => 'كوسوفو', 'name_en' => 'Kosovo', 'code' => 'XK'],
            ['name_ar' => 'قيرغيزستان', 'name_en' => 'Kyrgyzstan', 'code' => 'KG'],
            ['name_ar' => 'لاوس', 'name_en' => 'Laos', 'code' => 'LA'],
            ['name_ar' => 'لاتفيا', 'name_en' => 'Latvia', 'code' => 'LV'],
            ['name_ar' => 'ليسوتو', 'name_en' => 'Lesotho', 'code' => 'LS'],
            ['name_ar' => 'ليبيريا', 'name_en' => 'Liberia', 'code' => 'LR'],
            ['name_ar' => 'ليختنشتاين', 'name_en' => 'Liechtenstein', 'code' => 'LI'],
            ['name_ar' => 'ليتوانيا', 'name_en' => 'Lithuania', 'code' => 'LT'],
            ['name_ar' => 'لوكسمبورغ', 'name_en' => 'Luxembourg', 'code' => 'LU'],
            ['name_ar' => 'مدغشقر', 'name_en' => 'Madagascar', 'code' => 'MG'],
            ['name_ar' => 'مالاوي', 'name_en' => 'Malawi', 'code' => 'MW'],
            ['name_ar' => 'ماليزيا', 'name_en' => 'Malaysia', 'code' => 'MY'],
            ['name_ar' => 'جزر المالديف', 'name_en' => 'Maldives', 'code' => 'MV'],
            ['name_ar' => 'مالي', 'name_en' => 'Mali', 'code' => 'ML'],
            ['name_ar' => 'مالطا', 'name_en' => 'Malta', 'code' => 'MT'],
            ['name_ar' => 'جزر مارشال', 'name_en' => 'Marshall Islands', 'code' => 'MH'],
            ['name_ar' => 'موريتانيا', 'name_en' => 'Mauritania', 'code' => 'MR'],
            ['name_ar' => 'موريشيوس', 'name_en' => 'Mauritius', 'code' => 'MU'],
            ['name_ar' => 'المكسيك', 'name_en' => 'Mexico', 'code' => 'MX'],
            ['name_ar' => 'ميكرونيزيا', 'name_en' => 'Micronesia', 'code' => 'FM'],
            ['name_ar' => 'مولدوفا', 'name_en' => 'Moldova', 'code' => 'MD'],
            ['name_ar' => 'موناكو', 'name_en' => 'Monaco', 'code' => 'MC'],
            ['name_ar' => 'منغوليا', 'name_en' => 'Mongolia', 'code' => 'MN'],
            ['name_ar' => 'الجبل الأسود', 'name_en' => 'Montenegro', 'code' => 'ME'],
            ['name_ar' => 'المغرب', 'name_en' => 'Morocco', 'code' => 'MA'],
            ['name_ar' => 'موزمبيق', 'name_en' => 'Mozambique', 'code' => 'MZ'],
            ['name_ar' => 'ميانمار', 'name_en' => 'Myanmar', 'code' => 'MM'],
            ['name_ar' => 'ناميبيا', 'name_en' => 'Namibia', 'code' => 'NA'],
            ['name_ar' => 'ناورو', 'name_en' => 'Nauru', 'code' => 'NR'],
            ['name_ar' => 'نيبال', 'name_en' => 'Nepal', 'code' => 'NP'],
            ['name_ar' => 'هولندا', 'name_en' => 'Netherlands', 'code' => 'NL'],
            ['name_ar' => 'نيوزيلندا', 'name_en' => 'New Zealand', 'code' => 'NZ'],
            ['name_ar' => 'نيكاراغوا', 'name_en' => 'Nicaragua', 'code' => 'NI'],
            ['name_ar' => 'النيجر', 'name_en' => 'Niger', 'code' => 'NE'],
            ['name_ar' => 'نيجيريا', 'name_en' => 'Nigeria', 'code' => 'NG'],
            ['name_ar' => 'النرويج', 'name_en' => 'Norway', 'code' => 'NO'],
            ['name_ar' => 'سلطنة عمان', 'name_en' => 'Oman', 'code' => 'OM'],
            ['name_ar' => 'باكستان', 'name_en' => 'Pakistan', 'code' => 'PK'],
            ['name_ar' => 'بالاو', 'name_en' => 'Palau', 'code' => 'PW'],
            ['name_ar' => 'بنما', 'name_en' => 'Panama', 'code' => 'PA'],
            ['name_ar' => 'بابوا غينيا الجديدة', 'name_en' => 'Papua New Guinea', 'code' => 'PG'],
            ['name_ar' => 'باراغواي', 'name_en' => 'Paraguay', 'code' => 'PY'],
            ['name_ar' => 'بيرو', 'name_en' => 'Peru', 'code' => 'PE'],
            ['name_ar' => 'الفلبين', 'name_en' => 'Philippines', 'code' => 'PH'],
            ['name_ar' => 'بولندا', 'name_en' => 'Poland', 'code' => 'PL'],
            ['name_ar' => 'البرتغال', 'name_en' => 'Portugal', 'code' => 'PT'],
            ['name_ar' => 'قطر', 'name_en' => 'Qatar', 'code' => 'QA'],
            ['name_ar' => 'رومانيا', 'name_en' => 'Romania', 'code' => 'RO'],
            ['name_ar' => 'روسيا', 'name_en' => 'Russia', 'code' => 'RU'],
            ['name_ar' => 'رواندا', 'name_en' => 'Rwanda', 'code' => 'RW'],
            ['name_ar' => 'سانت كيتس ونيفيس', 'name_en' => 'Saint Kitts and Nevis', 'code' => 'KN'],
            ['name_ar' => 'سانت لوسيا', 'name_en' => 'Saint Lucia', 'code' => 'LC'],
            ['name_ar' => 'سانت فنسنت وجزر غرينادين', 'name_en' => 'Saint Vincent and the Grenadines', 'code' => 'VC'],
            ['name_ar' => 'ساموا', 'name_en' => 'Samoa', 'code' => 'WS'],
            ['name_ar' => 'سان مارينو', 'name_en' => 'San Marino', 'code' => 'SM'],
            ['name_ar' => 'ساو تومي وبرينسيب', 'name_en' => 'Sao Tome and Principe', 'code' => 'ST'],
            ['name_ar' => 'السنغال', 'name_en' => 'Senegal', 'code' => 'SN'],
            ['name_ar' => 'صربيا', 'name_en' => 'Serbia', 'code' => 'RS'],
            ['name_ar' => 'سيشيل', 'name_en' => 'Seychelles', 'code' => 'SC'],
            ['name_ar' => 'سيراليون', 'name_en' => 'Sierra Leone', 'code' => 'SL'],
            ['name_ar' => 'سنغافورة', 'name_en' => 'Singapore', 'code' => 'SG'],
            ['name_ar' => 'سلوفاكيا', 'name_en' => 'Slovakia', 'code' => 'SK'],
            ['name_ar' => 'سلوفينيا', 'name_en' => 'Slovenia', 'code' => 'SI'],
            ['name_ar' => 'جزر سليمان', 'name_en' => 'Solomon Islands', 'code' => 'SB'],
            ['name_ar' => 'الصومال', 'name_en' => 'Somalia', 'code' => 'SO'],
            ['name_ar' => 'جنوب أفريقيا', 'name_en' => 'South Africa', 'code' => 'ZA'],
            ['name_ar' => 'جنوب السودان', 'name_en' => 'South Sudan', 'code' => 'SS'],
            ['name_ar' => 'إسبانيا', 'name_en' => 'Spain', 'code' => 'ES'],
            ['name_ar' => 'سريلانكا', 'name_en' => 'Sri Lanka', 'code' => 'LK'],
            ['name_ar' => 'السودان', 'name_en' => 'Sudan', 'code' => 'SD'],
            ['name_ar' => 'سورينام', 'name_en' => 'Suriname', 'code' => 'SR'],
            ['name_ar' => 'إسواتيني', 'name_en' => 'Eswatini', 'code' => 'SZ'],
            ['name_ar' => 'السويد', 'name_en' => 'Sweden', 'code' => 'SE'],
            ['name_ar' => 'سويسرا', 'name_en' => 'Switzerland', 'code' => 'CH'],
            ['name_ar' => 'سوريا', 'name_en' => 'Syria', 'code' => 'SY'],
            ['name_ar' => 'طاجيكستان', 'name_en' => 'Tajikistan', 'code' => 'TJ'],
            ['name_ar' => 'تنزانيا', 'name_en' => 'Tanzania', 'code' => 'TZ'],
            ['name_ar' => 'تايلاند', 'name_en' => 'Thailand', 'code' => 'TH'],
            ['name_ar' => 'تيمور الشرقية', 'name_en' => 'East Timor', 'code' => 'TL'],
            ['name_ar' => 'توغو', 'name_en' => 'Togo', 'code' => 'TG'],
            ['name_ar' => 'تونغا', 'name_en' => 'Tonga', 'code' => 'TO'],
            ['name_ar' => 'ترينيداد وتوباغو', 'name_en' => 'Trinidad and Tobago', 'code' => 'TT'],
            ['name_ar' => 'تركيا', 'name_en' => 'Turkey', 'code' => 'TR'],
            ['name_ar' => 'تركمانستان', 'name_en' => 'Turkmenistan', 'code' => 'TM'],
            ['name_ar' => 'توفالو', 'name_en' => 'Tuvalu', 'code' => 'TV'],
            ['name_ar' => 'أوغندا', 'name_en' => 'Uganda', 'code' => 'UG'],
            ['name_ar' => 'أوكرانيا', 'name_en' => 'Ukraine', 'code' => 'UA'],
            ['name_ar' => 'الإمارات العربية المتحدة', 'name_en' => 'United Arab Emirates', 'code' => 'AE'],
            ['name_ar' => 'المملكة المتحدة', 'name_en' => 'United Kingdom', 'code' => 'GB'],
            ['name_ar' => 'الولايات المتحدة الأمريكية', 'name_en' => 'United States', 'code' => 'US'],
            ['name_ar' => 'أوروغواي', 'name_en' => 'Uruguay', 'code' => 'UY'],
            ['name_ar' => 'أوزبكستان', 'name_en' => 'Uzbekistan', 'code' => 'UZ'],
            ['name_ar' => 'فانواتو', 'name_en' => 'Vanuatu', 'code' => 'VU'],
            ['name_ar' => 'فنزويلا', 'name_en' => 'Venezuela', 'code' => 'VE'],
            ['name_ar' => 'فيتنام', 'name_en' => 'Vietnam', 'code' => 'VN'],
            ['name_ar' => 'اليمن', 'name_en' => 'Yemen', 'code' => 'YE'],
            ['name_ar' => 'زامبيا', 'name_en' => 'Zambia', 'code' => 'ZM'],
            ['name_ar' => 'زيمبابوي', 'name_en' => 'Zimbabwe', 'code' => 'ZW'],
        ];
        foreach ($countries as $country) {
            $newCountry = Country::updateOrCreate(
                ['name_en' => $country['name_en']],
                ['name_ar' => $country['name_ar'], 'code' => $country['code']]
            );

            if (array_key_exists('cities', $country)) {
                foreach ($country['cities'] as $city) {
                    City::updateOrCreate(['name_en' => $city['name_en'], 'country_id' => $newCountry->id], [
                        'name_ar' => $city['name_ar'],
                        'country_id' => $newCountry->id,
                    ]);
                }
            }
        }

        TravelInterest::updateOrCreate(['name_ar' => 'مغامرات', 'name_en' => 'Adventure Travel', 'image' => 'adventure.png']);
        TravelInterest::updateOrCreate(['name_ar' => 'ثقافة واستكشاف', 'name_en' => 'Cultural Exploration', 'image' => 'cultural.png']);
        TravelInterest::updateOrCreate(['name_ar' => 'طعام ومأكولات', 'name_en' => 'Food and Culinary Tourism', 'image' => 'food.png']);
        TravelInterest::updateOrCreate(['name_ar' => 'الحياة البرية والطبيعة', 'name_en' => 'Wildlife and Nature', 'image' => 'wildlife.png']);
        TravelInterest::updateOrCreate(['name_ar' => 'شواطئ واستجمام', 'name_en' => 'Beach and Relaxation', 'image' => 'beach.png']);
        TravelInterest::updateOrCreate(['name_ar' => 'تاريخ وثقافة', 'name_en' => 'Historical and Heritage Travel', 'image' => 'historical.png']);
        TravelInterest::updateOrCreate(['name_ar' => 'الفن والعمارة', 'name_en' => 'Art and Architecture', 'image' => 'art.png']);
        TravelInterest::updateOrCreate(['name_ar' => 'سفر اقتصادي', 'name_en' => 'Backpacking and Budget Travel', 'image' => 'backpacking.png']);
        TravelInterest::updateOrCreate(['name_ar' => 'التسوق', 'name_en' => 'Shopping', 'image' => 'shopping.png']);
        TravelInterest::updateOrCreate(['name_ar' => 'رحلات تصوير واستكشاف', 'name_en' => 'Photography and Visual Exploration', 'image' => 'photography.png']);

        FixedPage::updateOrCreate([
            'name_ar' => 'من نحن',
            'name_en' => 'About Us',
            'slug' => 'about-us',
            'content_ar' => 'من نحن',
            'content_en' => 'About Us',
        ]);

        FixedPage::updateOrCreate([
            'name_ar' => 'الشروط والاحكام',
            'name_en' => 'Terms and Conditions',
            'slug' => 'terms-and-conditions',
            'content_ar' => 'الشروط والاحكام',
            'content_en' => 'Terms and Conditions',
        ]);

        FixedPage::updateOrCreate([
            'name_ar' => 'سياسة الخصوصية',
            'name_en' => 'Privacy Policy',
            'slug' => 'privacy-policy',
            'content_ar' => 'سياسة الخصوصية',
            'content_en' => 'Privacy Policy',
        ]);

        // FixedPage::updateOrCreate([
        //     'name_ar' => 'حول التطبيق', 'name_en' => 'About App', 'slug' => 'about-app', 'content_ar' => 'حول التطبيق', 'content_en' => 'About App',
        // ]);

        PaymentSetting::updateOrCreate([
            'name_ar' => 'بنك الرجحي',
            'name_en' => 'Rojeh Bank',
            'code' => 'arjbank',
            'photo' => 'arjbank.png',
        ]);

        PaymentSetting::updateOrCreate([
            'name_ar' => 'بوابة تاب',
            'name_en' => 'Tap Gateway',
            'code' => 'tap',
            'photo' => 'tap.png',
        ]);

        PaymentSetting::updateOrCreate([
            'name_ar' => 'بوابة تابي',
            'name_en' => 'Tabby Gateway',
            'code' => 'tabby',
            'photo' => 'tabby.png',
        ]);

        PaymentSetting::updateOrCreate([
            'name_ar' => 'بوابة تمارا',
            'name_en' => 'Tamara Gateway',
            'code' => 'tamara',
            'photo' => 'tamara.png',
        ]);

        PageContent::updateOrCreate([
            'name_ar' => 'عن الشركة',
            'name_en' => 'About Us',
            'slug' => 'about-us',
            'url' => 'about-us',
            'content_ar' => 'عن الشركة',
            'content_en' => 'About Us',
        ]);

        PageContent::updateOrCreate([
            'name_ar' => 'الرؤية',
            'name_en' => 'Vision',
            'slug' => 'vision',
            'url' => 'about-us',
            'content_ar' => 'الرؤية',
            'content_en' => 'Vision',
        ]);

        PageContent::updateOrCreate([
            'name_ar' => 'المهمة',
            'name_en' => 'Mission',
            'slug' => 'mission',
            'url' => 'about-us',
            'content_ar' => 'المهمة',
            'content_en' => 'Mission',
        ]);

        PageContent::updateOrCreate([
            'name_ar' => 'القيمة',
            'name_en' => 'Value',
            'slug' => 'value',
            'url' => 'about-us',
            'content_ar' => 'القيمة',
            'content_en' => 'Value',
        ]);

        PageContent::updateOrCreate([
            'name_ar' => 'نبذة عن الشركة',
            'name_en' => 'Our Company Overview',
            'slug' => 'our-company-overview',
            'url' => 'about-us',
            'content_ar' => 'نبذة عن الشركة',
            'content_en' => 'Our Company Overview',
        ]);

        PageContent::updateOrCreate([
            'name_ar' => 'اكتشف وجهة أحلامك',
            'name_en' => 'Discover Your Dream Destinations',
            'slug' => 'discover-your-dream-destinations',
            'url' => 'home',
            'content_ar' => ' منصة سفرتك تقدم لك خيارات لا محدودة و توفر عليك عناء التخطيط من خلال استخدام الذكاء الاصطناعي سيكون لديك رحلة متكاملة جاهزة بثواني .. استعد لاطلاق روح المغامرة نحو وجهة احلامك',
            'content_en' => 'Say goodbye to endless research and let our AI-powered algorithm curate a list of destinations that perfectly match your preferences, budget, and travel style.',
        ]);

        PageContent::updateOrCreate([
            'name_ar' => 'رحلة أحلامك سلسلة و سهلة و بدون عناء',
            'name_en' => 'Seamless Booking and Management',
            'slug' => 'seamless-booking-and-management',
            'url' => 'home',
            'content_ar' => 'في سفرتك نوظف الذكاء الاصطناعي ليتيح لك خيارات لا محدودة من الوجهات العالمية لتستمتع بأسلوب سفر يتلائم مع تفضيلاتك و ميزانيتك',
            'content_en' => 'Hassle-free travel starts with SafarTech. Compare prices, book flights, and accommodation, and manage your entire trip from a single, user-friendly interface.',
        ]);

        PageContent::updateOrCreate([
            'name_ar' => 'كن مستعد لرحلة مليئة بالمتعة و الراحة',
            'name_en' => 'Let Us Take the Guesswork Out',
            'slug' => 'let-us-take-the-guesswork-out',
            'url' => 'home',
            'content_ar' => 'احجز رحلة أحلامك مع منصة سفرتك سيمكنك إدارة رحلتك كاملة و مقارنة الأسعار و حجز رحلات الطيران و الإقامة من واجهة واحدة فقط',
            'content_en' => 'Ditch the stress of planning and let our AI-powered algorithm take care of the details. We will find the best deals, book your flights and accommodations, and create an itinerary that will make your next adventure unforgettable',
        ]);
    }
}
