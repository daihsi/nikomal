<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class CommentRequest extends FormRequest
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
            'post_id' => ['required', 'exists:posts,id'],
            'comment' => ['required', 'string', 'max:150']
        ];
    }

    public function messages()
    {
        return [
            'post_id.required' => '投稿IDを入力してください',
            'post_id.exists' => '投稿IDがデータベースに存在しません',
            'comment.required' => 'コメントを入力してください',
            'comment.max' => 'コメントは150字以下で入力してください',
            'comment.string' => 'コメントは文字列で入力してください',
        ];
    }

    //json形式でエラー情報を返す
    protected function failedValidation(Validator $validator)
    {
        $response['data'] = [];
        $response['status'] = 'NG';
        $response['summary'] = 'Failed validation';
        $response['errors'] = $validator->errors()->toArray();

        throw new HttpResponseException(
                response()->json($response, 422)
            );
    }
}
