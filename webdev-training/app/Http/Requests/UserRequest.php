<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
        $userID =  $this->route('user') ?  $this->route('user')->id : null;
        return [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users,email,' .$userID,
            'password' => $this->isMethod('post') ? 'required|min:6' : 'nullable|min:6',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Email harus berformat yang valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
        ];
    }
}
