<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationUpdateRequest extends FormRequest
{
    public function authorize()
    {
        // tergantung authorisasi kamu. misal: hanya role bendahara/admin.
        return $this->user() !== null;
    }

    public function rules()
    {
        return [
            'name'       => 'required|string|max:191',
            'alias'      => 'nullable|string|max:50',
            'email'      => 'required|email|max:191',
            'phone'      => 'nullable|string|max:40',
            'address'    => 'nullable|string|max:1000',
            'logo'       => 'nullable|image|max:2048', // 2MB
            // jika kamu punya kolom lain, tambahkan di sini
        ];
    }
}
