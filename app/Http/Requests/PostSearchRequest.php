<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostSearchRequest extends FormRequest
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
            'keyword' => ['string', 'max:150', 'nullable'],
            'animals_name' => ['array', 'max:10'],
            'animals_name.*' => ['distinct'],
        ];
    }

    public function messages()
    {
        return [
            'animals_name.max' => '動物カテゴリーは10個以下で選択してください', 
            'animals_name.*' => '動物カテゴリーが重複しています',
        ];
    }
}
