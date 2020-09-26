<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\ResetPasswordRequest;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    //protected $redirectTo = RouteServiceProvider::HOME;

    //リクエストに成功リダイレクト処理の際
    //toastrによる成功フラッシュメッセージを表示する
    protected function redirectTo()
    {
        session()->flash('msg_success', 'パスワードを変更しました');
        return '/';
    }

    //パスワード再設定リクエスト失敗時の処理をオーバーライドし、toastrによる
    //失敗フラッシュメッセージ表示をする
    protected function sendResetFailedResponse(Request $request, $response)
    {
        return redirect()->back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => trans($response)])
                    ->with('msg_error', 'リクエストに失敗しました');
    }

    public function reset(ResetPasswordRequest $request)
    {
        //元々のバリデーションを消去
        //$request->validate($this->rules(), $this->validationErrorMessages());

        //新しいパスワードを保存。エラーの場合は、エラーレスポンスを返す
        $response = $this->broker()->reset(
            $this->credentials($request), function ($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        //成功レスポンスと失敗レスポンス
        return $response == Password::PASSWORD_RESET
                    ? $this->sendResetResponse($request, $response)
                    : $this->sendResetFailedResponse($request, $response);
    }
}
