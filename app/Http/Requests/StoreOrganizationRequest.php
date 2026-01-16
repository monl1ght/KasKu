<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrganizationRequest extends FormRequest
{


    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:191'],
            'short_name' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:191'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'account_number' => ['nullable', 'string', 'max:100'],

            'banks' => ['nullable', 'array'],
            'banks.*.bank_name' => ['nullable', 'string', 'max:100'],
            'banks.*.number' => ['nullable', 'string', 'max:50'],
            'banks.*.owner_name' => ['nullable', 'string', 'max:191'],

            'ewallets' => ['nullable', 'array'],
            'ewallets.*.type' => ['nullable', 'string', 'max:50'],
            'ewallets.*.number' => ['nullable', 'string', 'max:50'],
            'ewallets.*.owner_name' => ['nullable', 'string', 'max:191'],
        ];
    }

    protected function prepareForValidation()
    {
        // Remove empty bank/ewallet entries (avoid empty DB rows)
        if ($this->has('banks')) {
            $banks = array_filter($this->input('banks', []), function ($b) {
                return !empty(trim($b['bank_name'] ?? '')) ||
                    !empty(trim($b['number'] ?? '')) ||
                    !empty(trim($b['owner_name'] ?? ''));
            });
            $this->merge(['banks' => array_values($banks)]);
        }

        if ($this->has('ewallets')) {
            $ew = array_filter($this->input('ewallets', []), function ($e) {
                return !empty(trim($e['type'] ?? '')) ||
                    !empty(trim($e['number'] ?? '')) ||
                    !empty(trim($e['owner_name'] ?? ''));
            });
            $this->merge(['ewallets' => array_values($ew)]);
        }
    }
}
