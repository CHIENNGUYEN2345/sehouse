<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required',
            'password' => 'required|min:4'
        ];
    }
    public function messages()
    {
        return [
            'email.required' => 'Bắt buộc phải nhập email',
            'password.required' => 'Bắt buộc phải nhập mật khẩu',
            'password.min' => 'Mật khẩu phải trên 4 ký tự'
        ];
    }
}
