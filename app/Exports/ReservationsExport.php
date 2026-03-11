<?php

namespace App\Exports;

use App\Traits\PriceCalculationTrait;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReservationsExport implements FromCollection, WithHeadings, WithStyles
{
    protected $reservations;

    use PriceCalculationTrait;

    public function __construct($reservations)
    {
        $this->reservations = $reservations;
    }

    public function collection()
    {
        $rows = collect();
        $index = 0;

        foreach ($this->reservations as $reservation) {
            $isInternal = $reservation->reservation_type === 'inside';
            $tripType = $isInternal ? 'داخلي' : 'خارجي';
            $phone = $reservation->user->phone_code.$reservation->user->phone ?? 'غير محدد';
            $phone = is_numeric($phone) ? (string) $phone : $phone;

            $baseData = [
                'payment_type' => $reservation->payment_type == 'mada' ? 'مدى' : 'فيزا',
                'payment_status' => $reservation->paid_at ? 'تم الدفع' : 'لم يتم الدفع',
                'date_time' => $reservation->created_at->format('Y-m-d H:i:s'),
                'user_name' => $reservation->user->name ?? 'غير محدد',
                'email' => $reservation->user->email ?? 'غير محدد',
                'phone' => $phone,
            ];

            if ($reservation->hotel) {
                $hotelPrices = PriceCalculationTrait::newCalculatePriceAmounts($reservation);
                $hotel = $reservation->hotel;
                $rows->push(array_merge([
                    'index' => ++$index,
                    'invoice_number' => $reservation->uuid,
                    'booking_reference_id' => $reservation->booking_reference_id ?? 'غير محدد',
                    'service' => 'حجز فنادق',
                    'from' => $reservation->from_city ?? 'غير محدد',
                    'to' => $reservation->to_city ?? 'غير محدد',
                    'trip_type' => $tripType,
                    'supplier_cost' => $hotel->price_without_tax,
                    'tax1_rate' => (string) $hotel->first_tax_rate.'%',
                    'tax1_amount' => (string) $hotel->first_tax_amount,
                    'admin_fee_rate' => $hotel->administrative_tax_rate.'%',
                    'admin_fee' => $hotel->administrative_tax_amount,
                    'tax2_rate' => $hotel->second_tax_rate.'%',
                    'tax2_amount' => $hotel->second_tax_amount,
                    'total_including_tax' => $hotel->price_with_tax,
                    'invoice_total' => $hotel->price_with_tax,
                ], $baseData));
            }

            if ($reservation->flights && $reservation->flights->count() > 0) {
                foreach ($reservation->flights as $flight) {
                    $flightPrices = PriceCalculationTrait::newCalculatePriceAmounts($reservation);
                    $rows->push(array_merge([
                        'index' => ++$index,
                        'invoice_number' => $reservation->uuid,
                        'booking_reference_id' => $reservation->booking_reference_id ?? 'غير محدد',
                        'service' => 'رحلة طيران',
                        'from' => $flight->from_city ?? $reservation->from_city ?? 'غير محدد',
                        'to' => $flight->to_city ?? $reservation->to_city ?? 'غير محدد',
                        'trip_type' => $tripType,
                        'supplier_cost' => $flight->price_without_tax,
                        'tax1_rate' => (string) $flight->first_tax_rate.'%',
                        'tax1_amount' => (string) $flight->first_tax_amount,
                        'admin_fee_rate' => $flight->administrative_tax_rate.'%',
                        'admin_fee' => $flight->administrative_tax_amount,
                        'tax2_rate' => $flight->second_tax_rate.'%',
                        'tax2_amount' => $flight->second_tax_amount,
                        'total_including_tax' => $flight->price_with_tax,
                        'invoice_total' => $flight->price_with_tax,
                    ], $baseData));
                }
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'م',
            'رقم الفاتورة',
            'رقم الحجز المرجعي',
            'الخدمة',
            'من',
            'الى',
            'نوع الرحلة',
            'تكلفه المورد سعر الوحدة',
            'معدل الضريبه',
            'مبلغ الضريبه',
            'نسبه الربح الرسوم الاداريه   ',
            'سعر الوحدة',
            'معدل الضريبه',
            'الضريبه',
            'الأجمالي شامل الضريبه',
            'إجمالي الفاتوره',
            'نوع الدفع',
            'حالة الدفع',
            'التاريخ والوقت',
            'اسم المستخدم',
            'البريد الالكتروني',
            'رقم الجوال',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);
        $sheet->getStyle('A1:V1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F81BD'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle('A2:V'.($this->collection()->count() + 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        foreach (range('A', 'V') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }
}
