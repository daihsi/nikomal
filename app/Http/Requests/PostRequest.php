<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

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
            'animals_name' => ['required', 'array', 'max:3'],
            'animals_name.*' => ['distinct'],
        ];
    }

    /**
     *  新規投稿画像のリサイズ処理
     *
     * @return string
     */
    public function imageUrl()
    {;
        //画像データのリサイズ
        $file = $this->image;
        $name = $file->getClientOriginalName();
        $image = Image::make($file)
            ->resize(400, null, function ($constraint) {
                $constraint->aspectRatio();
            });

        //s3に画像保存
        $path = Storage::disk('s3')->put('/post_images/'.$name, (string) $image->encode(), 'public');

        //画像URLを返す
        return Storage::disk('s3')->url('post_images/'.$name);
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
            'animals_name.required' => '動物カテゴリーを選択してください', 
            'animals_name.max' => '動物カテゴリーは3個以下で選択してください', 
            'animals_name.*' => '動物カテゴリーが重複しています',
        ];
    }
}
