<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
            'name'=>'required',
            'address'=>'required',
            'phone'=>'required|min:10',
//            'email'=>'email',
        ];
    }
    public function messages()
    {
        return [
//            'email.email'=>'Email Không đúng định dạng!',
            'name.required'=>'Họ tên Không được để trống!',
            'address.required'=>'Địa chỉ Không được để trống!',
            'phone.required'=>'số điện thoại Không được để trống!',
            'phone.min'=>'Số điện thoại Không được nhỏ hơn 10 ký tự!',
        ];
    }
}
