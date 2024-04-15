<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|unique:admin,email',
            'password' => 'required|min:4',
            'password_confimation' => 'required|same:password',
            'phone' => 'required|unique:admin,tel',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Bắt buộc phải nhập tên!',
            'email.required' => 'Bắt buộc phải nhập email!',
            'email.unique' => 'Địa chỉ email đã tồn tại!',
            'password.required' => 'Bắt buộc phải nhập mật khẩu!',
            'password.min' => 'Mật khẩu phải trên 4 ký tự!',
            'password_confimation.required' => 'Bắt buộc nhập lại mật khẩu!',
            'password_confimation.same' => 'Nhập lại sai mật khẩu!',
            'phone.required' => 'Bắt buộc nhập số điện thoại!',
            'phone.unique' => 'Số điện thoại đã tồn tại!',
        ];
    }
}
