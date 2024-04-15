<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillLivestreamRequest extends FormRequest
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
            'url_fb'=>'required',
            'name' => 'required',
            'number_eyes' => 'required',
            'time_action'=>'required'
        ];
    }
    public function messages()
    {
        return [
            'url_fb.required' => 'Bắt buộc phải nhập link facebook',
            'name.required' => 'Bắt buộc phải nhập tên',
            'number_eyes.required' => 'Bắt buộc nhập số mắt',
            'time_action.required' => 'Bắt buộc nhập thời gian'
        ];
    }
}
