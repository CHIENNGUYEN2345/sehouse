<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChagePasswordRequest extends FormRequest
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
            'password_current' => 'required|max:20|min:5',
            'password' => 'required|max:20|min:5',
            're_password' => 'required|max:20|min:5|same:password',
        ];
    }
    public function messages()
    {
        return [
            'password_current.required' => 'Bắt buộc phải nhập mật khẩu hiện tại!',
            'password_current.max' => 'Mật khẩu hiện tại tối đa 20 ký tự!',

            'password.required' => 'Bắt buộc phải nhập mật khẩu!',
            'password.min' => 'Mật khẩu phải trên 5 ký tự!',
            'password.max' => 'Mật khẩu tối đa 20 ký tự!',

            're_password.same' => 'Nhập lại sai mật khẩu!',
            're_password.max' => 'Mật khẩu nhập lại tối đa 20 kí tự',
            're_password.min' => 'Mật khẩu nhập lại phải trên 5 ký tự!',
            're_password.required' => 'Bắt buộc phải nhập lại mật khẩu!',
        ];
    }
}
