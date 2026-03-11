<?php

namespace App\Http\Requests\Website;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class PassengerInformationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'first_name' => ['required', 'array'],
            'first_name.*' => [
                'required',
                'string',
                'min:2',                                    // Minimum 2 characters
                'regex:/^[a-zA-Z\s\-\'\.]+$/',             // Only letters, spaces, hyphens, apostrophes, and dots
                'not_regex:/\d/',                           // No numbers allowed
                function ($attribute, $value, $fail) {
                    // Extract the index from the attribute (e.g., "first_name.0" -> 0)
                    preg_match('/first_name\.(\d+)/', $attribute, $matches);
                    $currentIndex = $matches[1] ?? null;

                    if ($currentIndex !== null) {
                        // Get the corresponding last name for this index
                        $currentLastName = $this->last_name[$currentIndex] ?? '';
                        $currentFullName = strtolower(trim($value.' '.$currentLastName));

                        // Check for duplicate full names (first + last name combination)
                        $allFullNames = [];
                        $firstNames = $this->first_name ?? [];
                        $lastNames = $this->last_name ?? [];

                        foreach ($firstNames as $index => $firstName) {
                            $lastName = $lastNames[$index] ?? '';
                            $fullName = strtolower(trim($firstName.' '.$lastName));
                            $allFullNames[$index] = $fullName;
                        }

                        // Count occurrences of the current full name
                        $duplicateCount = 0;
                        foreach ($allFullNames as $index => $fullName) {
                            if ($fullName === $currentFullName) {
                                $duplicateCount++;
                            }
                        }

                        if ($duplicateCount > 1) {
                            $fail(__('dashboard.duplicate_passenger_full_name'));
                        }
                    }
                },
            ],
            'last_name' => ['required', 'array'],
            'last_name.*' => [
                'required',
                'string',
                'min:2',                                    // Minimum 2 characters
                'regex:/^[a-zA-Z\s\-\'\.]+$/',             // Only letters, spaces, hyphens, apostrophes, and dots
                'not_regex:/\d/',                            // No numbers allowed
            ],
            'pax_type' => ['required', 'array'],
            'pax_type.*' => ['required', 'in:1,2,3'],
            'birth_date' => ['required', 'array'],
            'birth_date.*' => [
                'required',
                'date',
                'before:today',
                function ($attribute, $value, $fail) {
                    // Extract the index from the attribute (e.g., "birth_date.0" -> 0)
                    preg_match('/birth_date\.(\d+)/', $attribute, $matches);
                    $currentIndex = $matches[1] ?? null;

                    if ($currentIndex !== null) {
                        // Get the passenger type for this index
                        $passengerType = $this->pax_type[$currentIndex] ?? null;

                        // if ($passengerType == 2) { // Child
                        //     $birthDate = Carbon::parse($value);
                        //     $age = $birthDate->diffInYears(now());

                        //     if (!($age <= 2 && $age >= 12)) {
                        //         $fail(__('dashboard.child_age_validation'));
                        //     }
                        // }
                    }
                },
            ],
            'email' => ['required', 'array'],
            'email.*' => ['required', 'email'],
            'phone' => ['required', 'array'],
            'phone.*' => ['required', 'string', 'digits_between:7,17'],
            'nationality' => ['required', 'array'],
            'nationality.*' => ['required', 'string'],
            'gender' => ['required', 'array'],
            'gender.*' => ['required', 'in:0,1,2'],
            'address' => ['required', 'array'],
            'address.*' => ['required', 'string'],
            'birth_date.0' => ['date', 'before:today', 'before:'.now()->subYears(18)->format('Y-m-d')],

            // Infant-specific validation rules
            'guardian_adult_select' => ['nullable', 'array'],
            'guardian_adult_select.*' => ['nullable', 'string'],
            'infant_seat_select' => ['nullable', 'array'],
            'infant_seat_select.*' => ['nullable', 'in:lap,seat'],
            'infant_notes' => ['nullable', 'array'],
            'infant_notes.*' => ['nullable', 'string', 'max:500'],
            'infant_meal' => ['nullable', 'array'],
            'infant_meal.*' => ['nullable', 'in:none,baby_food,formula_milk'],
            'bassinet_request' => ['nullable', 'array'],
            'bassinet_request.*' => ['nullable', 'in:yes,no'],
        ];

        // Add passport validation only for flight trips (trip_type 1 or 3)
        $tripType = session('preferences.trip_type');
        if (isset($tripType) && in_array($tripType, [1, 3])) {
            $rules['passport_number'] = ['required', 'array'];
            $rules['passport_number.*'] = ['required', 'numeric', 'digits_between:6,15'];
            $rules['passport_expiry_date'] = ['required', 'array'];
            $rules['passport_expiry_date.*'] = [
                'required',
                'date',
                'after:today',
                'after:'.now()->addMonths(6)->format('Y-m-d'),
            ];
        }

        return $rules;
    }

    public function prepareForValidation()
    {
        $preparedData = [
            'birth_date' => collect($this->birth_date)->map(function ($date) {
                return date('Y-m-d', strtotime($date));
            })->toArray(),

            'title' => collect($this->pax_type)->map(function ($pax_type, $key) {
                if ($pax_type == 1 || $pax_type == 2) {
                    $title = $this->gender[$key] == 1 ? 'Mr' : 'Ms';
                }

                if ($pax_type == 3) {
                    $title = $this->gender[$key] == 1 ? 'MSTR' : 'Ms';
                }

                return $title;
            })->toArray(),
        ];

        // Only process passport data for flight trips (trip_type 1 or 3)
        $tripType = session('preferences.trip_type');
        if (isset($tripType) && in_array($tripType, [1, 3]) && $this->passport_expiry_date) {
            $preparedData['passport_expiry_date'] = (array) collect($this->passport_expiry_date)->map(function ($date) {
                return date('Y-m-d', strtotime($date));
            })->toArray();
        }

        $this->merge($preparedData);
    }

    public function passedValidation()
    {
        $this->merge([
            'title' => collect($this->pax_type)->map(function ($pax_type, $key) {
                if ($pax_type == 1 || $pax_type == 2) {
                    $title = $this->gender[$key] == 1 ? 'Mr' : 'Ms';
                }

                if ($pax_type == 3) {
                    $title = $this->gender[$key] == 1 ? 'MSTR' : 'Ms';
                }

                return $title;
            })->toArray(),
        ]);
    }

    public function messages()
    {
        return [
            'birth_date.0.before' => __('dashboard.birth_date_adult_min_age'),
            'first_name.*.min' => __('dashboard.first_name_min_length'),
            'first_name.*.regex' => __('dashboard.first_name_invalid_characters'),
            'first_name.*.not_regex' => __('dashboard.first_name_no_numbers'),
            'last_name.*.min' => __('dashboard.last_name_min_length'),
            'last_name.*.regex' => __('dashboard.last_name_invalid_characters'),
            'last_name.*.not_regex' => __('dashboard.last_name_no_numbers'),
            'passport_expiry_date.*.after' => __('dashboard.passport_expiry_min_months'),
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        // Laravel automatically flashes input to session, we don't need custom handling
        parent::failedValidation($validator);
    }
}
