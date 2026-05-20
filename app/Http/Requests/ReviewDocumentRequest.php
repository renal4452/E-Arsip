<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // Status wajib diisi dan hanya boleh berisi 'ACC' atau 'Revisi'
            'status' => 'required|in:ACC,Revisi',
            
            // Catatan WAJIB kalau statusnya 'Revisi' (biar pegawai tau salahnya di mana)
            'notes' => 'required_if:status,Revisi|nullable|string|max:2000'
        ];
    }

    public function messages()
    {
        return [
            'notes.required_if' => 'Catatan wajib diisi jika Bapak/Ibu meminta dokumen direvisi.',
        ];
    }
}