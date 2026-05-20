<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    
    public function rules(): array
    {
        // Tangkap user yang sedang diedit dari Parameter URL (Route Model Binding)
        $user = $this->route('user'); 

        return [
            'name' => 'required|string|max:255',
            // Pengecualian unique email menggunakan Rule Class Laravel yang rapi
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'password' => 'nullable|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'division_id' => 'required|exists:divisions,id',
            'is_active' => 'required|boolean',
        ];
    }
}