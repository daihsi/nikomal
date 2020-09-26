<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class RegisterRequest extends FormRequest
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
        return[
        'name' => ['required', 'string', 'max:15'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
        'avatar' => ['file', 'image', 'mimes:jpeg,png,jpg', 'max:2048', 'nullable']
        ];
    }

    //フラッシュメッセージのみ追加し、オーバーライド
    protected function failedValidation(Validator $validator)
    {
        $this->merge(['validated' => 'true']);
        // リダイレクト先
        throw new HttpResponseException(
        back()->withInput($this->input)->withErrors($validator)->with('msg_error', 'ユーザー登録に失敗しました')
        );
    }

}
