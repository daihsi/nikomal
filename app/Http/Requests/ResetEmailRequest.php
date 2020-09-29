<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetEmailRequest extends FormRequest
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
            'new_email' => [
                'different:guest_login_email', //簡単ログインのメールアドレスであれば通らない
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email'
            ],
        ];
    }

    //フラッシュメッセージのみ追加し、オーバーライド
    protected function failedValidation(Validator $validator)
    {
        //dd($validator);
        $this->merge(['validated' => 'true']);
        // リダイレクト先
        throw new HttpResponseException(
        back()->withInput($this->input)->withErrors($validator)->with('msg_error', 'リクエストに失敗しました')
        );
    }

    public function messages()
    {
        return [
            'new_email.different' => '簡単ログイン用のメールアドレスは変更できません', 
        ];
    }
}
