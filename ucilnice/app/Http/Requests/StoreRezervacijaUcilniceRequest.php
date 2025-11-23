<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRezervacijaUcilniceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Spremenjeno na true za avtorizirane uporabnike
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ucilnica_id' => 'required|exists:tabela_ucilnice,id',
            'datum_od' => 'required|date|after_or_equal:today',
            'datum_do' => 'required|date|after:datum_od',
            'namen' => 'required|string|max:255',
            'opombe' => 'nullable|string|max:500',
        ];
    }
    
    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'ucilnica_id.required' => 'Prosimo izberite učilnico.',
            'ucilnica_id.exists' => 'Izbrana učilnica ne obstaja.',
            'datum_od.required' => 'Datum začetka je obvezen.',
            'datum_od.after_or_equal' => 'Datum začetka mora biti danes ali v prihodnosti.',
            'datum_do.required' => 'Datum konca je obvezen.',
            'datum_do.after' => 'Datum konca mora biti po datumu začetka.',
            'namen.required' => 'Namen rezervacije je obvezen.',
        ];
    }
}