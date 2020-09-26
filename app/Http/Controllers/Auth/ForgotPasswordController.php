<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\ForgotPasswordRequest;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        //元々のバリデーションルールを消去
        //$this->validateEmail($request);

        //ユーザーにリンクとメッセージを送信
        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );

        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }

    //リクエスト情報を取得
    protected function credentials(ForgotPasswordRequest $request)
    {
        return $request->only('email');
    }

    //パスワード再設定リクエスト後の処理をオーバーライドし、toastrによる
    //成功フラッシュメッセージ表示をする
    protected function sendResetLinkResponse(ForgotPasswordRequest $request, $response)
    {
        return back()->with('msg_success', trans($response));
    }

    //パスワード再設定リクエスト失敗時の処理をオーバーライドし、toastrによる
    //失敗フラッシュメッセージ表示をする
    protected function sendResetLinkFailedResponse(ForgotPasswordRequest $request, $response)
    {
        return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => trans($response)])
                ->with('msg_error', 'リクエストに失敗しました');
    }
}