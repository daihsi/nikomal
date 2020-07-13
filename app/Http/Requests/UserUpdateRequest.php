<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
        'name' => ['required', 'string', 'max:15'],
        'avatar' => ['file', 'image', 'mimes:jpeg,png,jpg', 'max:2048', 'nullable'],
        'self_introduction' => ['string', 'max:150', 'nullable'],
        ];
    }
}
