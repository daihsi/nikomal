<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class PostRequest extends FormRequest
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
            'image' => ['file', 'image', 'mimes:jpeg,png,jpg', 'max:2048', 'required'],
            'animals_name' => ['required'],
        ];
    }

    //フラッシュメッセージのみ追加し、オーバーライド
    protected function failedValidation(Validator $validator)
    {
        $this->merge(['validated' => 'true']);
        // リダイレクト先
        throw new HttpResponseException(
        back()->withInput($this->input)->withErrors($validator)->with('msg_error', '投稿に失敗しました')
        );
    }

    public function messages()
    {
        return [
            'animals_name.required' => '動物を選択してください', 
        ];
    }
}
