<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_perusahaan' => ['required', 'string', 'max:150'],
            'name' => ['required', 'string', 'max:100'],
            'bidang' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'alpha_dash', 'unique:users,username'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'kota' => ['nullable', 'string', 'max:80'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_perusahaan.required' => 'Nama instansi/perusahaan wajib diisi.',
            'name.required' => 'Nama penanggung jawab (PIC/HRD) wajib diisi.',
            'bidang.required' => 'Bidang industri/sektor usaha wajib diisi.',
            'email.required' => 'Alamat email instansi/perusahaan wajib diisi.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'username.required' => 'Username akun instansi wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ];
    }
}
