<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRevisionRequest extends FormRequest
{
    public function authorize()
    {
        // Set true dulu. Urusan hak akses (Siloing) kita serahkan ke Middleware/Policy
        return true; 
    }

    public function rules()
    {
        return [
            // File wajib ada, format aman, maksimal 10MB
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            // Catatan opsional, kalau pegawai mau nambahin keterangan ke atasan
            'notes' => 'nullable|string|max:1000' 
        ];
    }

    public function messages()
    {
        // (Opsional) Biar pesan error-nya manusiawi, bukan bahasa robot Inggris
        return [
            'file.required' => 'File revisi wajib diunggah!',
            'file.mimes' => 'Format file harus PDF, Word, atau Excel.',
            'file.max' => 'Ukuran file tidak boleh lebih dari 10MB.',
        ];
    }
}