<?php

namespace App\Helpers;

use App\Models\Panel\Setting;

class PriceCalculationHelper
{
    public static function calculatePriceAmounts(float $price, string $reservationType,string $bookingType='flight'): array
    {
        $service_fees_percent=0;
        if($bookingType=='flight'){
            $service_fees_percent = Setting::where('code', 'flight_profit')->first() ? (float)Setting::where('code', 'flight_profit')->first()->value : 7;
        }else{
            $service_fees_percent = Setting::where('code', 'hotel_profit')->first() ? (float)Setting::where('code', 'hotel_profit')->first()->value : 12;
        }

        $total_price = $price;
        $tax1_amount = $reservationType === 'inside' ? 0.15 : 0;
        $tax1_amount=$tax1_amount*$total_price;
        $unit_administrative_fees = $service_fees_percent * $price/100;
        $tax2_amount = 0.15 * $unit_administrative_fees;
        $total_taxes2 = $tax2_amount + $unit_administrative_fees;
        $total_price = $tax1_amount + $price + $total_taxes2;

        return [
            'base_price' => $price,
            'tax1_amount' => $tax1_amount,
            'unit_administrative_fees' => $unit_administrative_fees,
            'tax2_amount' => $tax2_amount,
            'total_taxes2' => $total_taxes2,
            'total_price' => $total_price,
        ];
    }

    public static function newCalculatePriceAmounts($reservation): array
    {
        $hotel_price = $reservation->hotel ? $reservation->hotel->price_without_tax : 0;
        $flights_price = count($reservation->flights) > 0 ? $reservation->flights->sum('price_without_tax') : 0;

        $total_price = $price = $hotel_price + $flights_price;
        $tax1_amount = $reservation->reservation_type === 'inside' ? 0.15 : 0;
        $hotel_unit_administrative_fees = $reservation->hotel ? $reservation->hotel->administrative_tax_amount : 0;
        $flights_unit_administrative_fees = count($reservation->flights) > 0 ? $reservation->flights->sum('administrative_tax_amount') : 0;
        $hotel_tax2_amount = $reservation->hotel ? $reservation->hotel->second_tax_amount : 0;
        $flights_tax2_amount = count($reservation->flights) > 0 ? $reservation->flights->sum('second_tax_amount') : 0;
        $hotel_tax1_amount = $reservation->hotel ? $reservation->hotel->first_tax_amount : 0;
        $flights_tax1_amount = count($reservation->flights) > 0 ? $reservation->flights->sum('first_tax_amount') : 0;
        $hotel_total_taxes2 = $hotel_tax2_amount;
        $flights_total_taxes2 = $flights_tax2_amount;
        $hotel_total_taxes1 = $hotel_tax1_amount;
        $flights_total_taxes1 = $flights_tax1_amount;
        $total_taxes2 = $hotel_total_taxes2 + $flights_total_taxes2;
        $total_taxes1 = $hotel_total_taxes1 + $flights_total_taxes1;
        $tax2_amount = $hotel_tax2_amount + $flights_tax2_amount;
        $unit_administrative_fees = $hotel_unit_administrative_fees + $flights_unit_administrative_fees;
        $hotel_total_price = $reservation->hotel ? $reservation->hotel->price_with_tax : 0;
        $flights_total_price = count($reservation->flights) > 0 ? $reservation->flights->sum('price_with_tax') : 0;
        $total_price = $hotel_total_price + $flights_total_price;

        $vat1 = $total_taxes1 == 0 ? 0 : $price;
        $vat2 = $unit_administrative_fees;

        return [
            'hotel_price' => (float)$hotel_price,
            'hotel_unit_administrative_fees' => (float)$hotel_unit_administrative_fees,
            'hotel_tax2_amount' => (float)number_format($hotel_tax2_amount, 2),
            'hotel_total_taxes2' => (float)number_format($hotel_total_taxes2, 2),
            'hotel_total_price' => (float)$hotel_total_price,

            'flights_price' => (float)number_format($flights_price,2),
            'flights_unit_administrative_fees' => $flights_unit_administrative_fees,
            'flights_tax2_amount' => (float)number_format($flights_tax2_amount, 2),
            'flights_total_taxes2' => (float)number_format($flights_total_taxes2, 2),
            'flights_total_price' => (float)number_format($flights_total_price,2),

            'base_price' => (float)number_format($price,2),
            'tax1_amount' => (float)number_format($tax1_amount,2),
            'total_taxes2' => (float)number_format($total_taxes2, 2),
            'total_taxes1' => (float)number_format($total_taxes1, 2),
            'tax2_amount' => (float)number_format($tax2_amount, 2),
            'unit_administrative_fees' => (float)number_format($unit_administrative_fees,2),

            'total_price' => (float)number_format($total_price,2),

            'vat' => (float)number_format($vat1 + $vat2,2),
        ];
    }
    public static function mergeCalculatePrices($arrays){
        $validArrays = array_filter($arrays, function ($arr) {
            return is_array($arr) && !empty($arr);
        });

// If there's nothing valid, return an empty array or handle gracefully
        if (empty($validArrays)) {
            $merged = [];
        } else {
            // Initialize with zeros using the first valid array
            $merged = array_fill_keys(array_keys(reset($validArrays)), 0);

            foreach ($validArrays as $array) {
                foreach ($array as $key => $value) {
                    $merged[$key] += $value;
                }
            }
        }

        return $merged;
    }
}
