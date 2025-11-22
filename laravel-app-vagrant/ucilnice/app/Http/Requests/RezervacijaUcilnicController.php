<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RezervacijaUcilnicController extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_ucilnice' => 'required|numeric|min:0|max:255,unique:tabela_ucilnic',
            'kapaciteta' => 'required|numeric|min:1|max:10',
            'vrsta_ucilnice' => 'required|string|max:50',
            'skrbnik' => 'nullable|string|max:50',
        ];
    }
}
