<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForceUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ✅ UBAH JADI TRUE! Otorisasi udah diurus sama Policy di Controller.
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240'
        ];
    }
}