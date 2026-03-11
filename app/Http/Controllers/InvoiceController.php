<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Website\Controller;
use App\Models\Panel\Setting;
use App\Models\Reservation;
use App\Traits\PriceCalculationTrait;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf;
use MuktarSayedSaleh\ZakatTlv\Encoder;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvoiceController extends Controller
{
    use PriceCalculationTrait;

    public function generateInvoice($id)
    {
        $reservation = Reservation::find($id);

        if (! $reservation) {
            abort(404);
        }

        if (! auth('admin')->check() && (int) $reservation->user_id !== (int) auth('web')->id()) {
            abort(403);
        }

        $total_price_calculate = PriceCalculationTrait::newCalculatePriceAmounts($reservation);

        // Load all needed settings in a single query
        $settings = Setting::whereIn('code', [
            'phone', 'email', 'whatsapp', 'address', 'twitter', 'facebook',
            'instagram', 'youtube', 'linkedin', 'snapchat', 'company_name', 'commercial_registration',
        ])->pluck('value', 'code');

        $data = [
            'image' => public_path('site/img/logo.png'),
            'phone' => $settings['phone'] ?? '',
            'email' => $settings['email'] ?? '',
            'whatsapp' => $settings['whatsapp'] ?? '',
            'address' => $settings['address'] ?? '',
            'twitter' => $settings['twitter'] ?? '',
            'facebook' => $settings['facebook'] ?? '',
            'instagram' => $settings['instagram'] ?? '',
            'youtube' => $settings['youtube'] ?? '',
            'linkedin' => $settings['linkedin'] ?? '',
            'snapchat' => $settings['snapchat'] ?? '',
            'company_name' => $settings['company_name'] ?? 'سفر تك',
            'commercial_registration' => $settings['commercial_registration'] ?? '112233445566778',
            'from' => $reservation->from_city,
            'to' => $reservation->to_city,

            'invoiceNumber' => $reservation->uuid,
            'booking_reference_id' => $reservation->booking_reference_id,
            'user' => $reservation->user,
            'date' => $reservation->created_at,
            'purchaseOrderNumber' => $reservation->uuid,
            'purchaseOrderDate' => $reservation->created_at,
            'totalWithoutVAT' => (float) number_format($total_price_calculate['base_price'] + $total_price_calculate['unit_administrative_fees'], 2),
            'totalTaxableAmount' => (float) number_format($total_price_calculate['vat'], 2),
            'totalVATAmount' => (float) number_format($total_price_calculate['total_taxes2'] + $total_price_calculate['total_taxes1'], 2),
            'totalIncludingVAT' => (float) number_format($total_price_calculate['total_price'], 2),
            'paidAmount' => (float) number_format($total_price_calculate['total_price'], 2),
            'tax2_amount' => (float) number_format($total_price_calculate['tax2_amount'], 2),

        ];
        $data['items'] = [];
        if (count($reservation->flights) > 0) {
            foreach ($reservation->flights as $flight) {
                $data['items'][] = [
                    'passenger' => ' رحلة طيران - flight ',
                    'service' => 'رحلة طيران - flight',
                    'description' => $reservation->reservation_type == 'inside' ? 'رحلة طيران داخلية - Domestic flight' : 'رحلة طيران خارجية - International flight',
                    'unitPrice' => $flight->price_without_tax,
                    'first_tax_rate' => $flight->first_tax_rate.'%',
                    'first_tax_amount' => (float) number_format($flight->first_tax_amount, 2),

                    'administrative_tax_rate' => $flight->administrative_tax_rate.'%',
                    'administrative_tax_amount' => (float) number_format($flight->administrative_tax_amount, 2),
                    'second_tax_rate' => $flight['second_tax_rate'].'%',
                    'second_tax_amount' => (float) number_format($flight->second_tax_amount, 2),
                    'price_with_tax' => (float) number_format($flight->price_with_tax, 2),
                    'price_without_tax' => (float) number_format($flight->price_without_tax, 2),
                    'tax_amount' => (float) number_format($flight->tax_amount, 2),
                ];
            }
        }

        if ($reservation->hotel) {
            $hotel = $reservation->hotel;
            $data['items'][] = [
                'passenger' => 'حجز فندق - hotel reservation',
                'service' => 'حجز فندق - hotel reservation',
                'description' => $hotel->hotel_name,
                'unitPrice' => $hotel->price_without_tax,

                'first_tax_rate' => $hotel->first_tax_rate.'%',
                'first_tax_amount' => (float) number_format($hotel->first_tax_amount, 2),

                'administrative_tax_rate' => $hotel->administrative_tax_rate.'%',
                'administrative_tax_amount' => (float) number_format($hotel->administrative_tax_amount, 2),
                'second_tax_rate' => $hotel->second_tax_rate.'%',
                'second_tax_amount' => (float) number_format($hotel->second_tax_amount, 2),
                'price_with_tax' => (float) number_format($hotel->price_with_tax, 2),
                'price_without_tax' => (float) number_format($hotel->price_without_tax, 2),
                'tax_amount' => (float) number_format($hotel->tax_amount, 2),
            ];
        }

        // Simple number formatting (removing NumberFormatter dependency)
        $data['arabic'] = number_format(round($data['paidAmount'], 2), 2);
        $data['english'] = number_format(round($data['paidAmount'], 2), 2);

        $encoder = new Encoder;
        $qr_signature = $encoder->encode(
            $data['company_name'],
            $data['commercial_registration'],
            $reservation->created_at,
            (float) number_format($data['paidAmount'], 2),
            (float) number_format($data['totalVATAmount'], 2),
        );
        // $data['qr'] = QrCode::size(90)->generate($data['invoiceNumber']);
        $data['qr'] = QrCode::size(90)->generate($qr_signature);
        $pdf = LaravelMpdf::loadView('invoice', $data);

        return $pdf->stream('invoice.pdf');
    }
}
