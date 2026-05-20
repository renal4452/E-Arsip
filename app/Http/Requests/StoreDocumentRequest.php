<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Izin akses dikontrol di Middleware
    }

    public function rules()
    {
        return [
            // No Dokumen unik agar tidak ada duplikasi data di server
            'no_doc' => 'required|string|unique:documents,no_doc|max:100',
            'title' => 'required|string|max:255',
            'doc_type_id' => 'required|exists:doc_types,id',
            // File wajib ada, maksimal 10MB, format dokumen kantor
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:10240',
            // Deskripsi atau perihal tambahan
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'no_doc.unique' => 'Nomor dokumen ini sudah ada di sistem. Gunakan nomor lain.',
            'file.required' => 'Draf dokumen wajib diunggah.',
            'file.mimes' => 'Hanya menerima format PDF, Word, atau Excel.',
            'doc_type_id.exists' => 'Jenis dokumen yang dipilih tidak valid.',
        ];
    }
}
