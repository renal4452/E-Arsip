<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Pengecekan Admin kita pindah ke Constructor Controller
    }

    public function rules(): array
    {
        // Trik Pro: Tentukan tabel dan kolom berdasarkan input 'module'
        $table = $this->input('module') === 'audit' ? 'doc_types' : 'shared_types';
        $column = $this->input('module') === 'audit' ? 'name_types' : 'name';

        return [
            'module' => ['required', 'in:audit,shared'],
            'name' => ['required', 'string', 'max:255', "unique:{$table},{$column}"],
            'description' => ['nullable', 'string']
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Nama kategori ini sudah digunakan, silakan pilih nama lain.',
        ];
    }
}