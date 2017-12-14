<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsersCreateRequest extends FormRequest
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
            'plans_id' => 'required',
            'email' => 'required|email|unique:users,email'.( ! empty($this->id) ? ','.$this->id : ''),
            'firstname' => 'required',
            'password' => 'required_without:id',
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'plans_id.required' => __('Choose Payment Plan first'),
            'password.required_without' => __('Password is required'),
        ];
    }
}
