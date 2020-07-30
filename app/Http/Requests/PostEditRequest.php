<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostEditRequest extends FormRequest
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
            'content' => ['required', 'string', 'max:150'],
            'image' => ['file', 'image', 'mimes:jpeg,png,jpg', 'max:2048', 'nullable'],
            'animals_name' => ['required'],
        ];
    }
    
    public function messages()
    {
        return [
            'animals_name.required' => '動物を選択してください', 
        ];
    }
}
