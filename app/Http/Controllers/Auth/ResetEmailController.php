<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetEmailRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\EmailReset;
use App\User;

class ResetEmailController extends Controller
{

    //メールアドレス変更フォームへ移動
    public function showLinkRequestForm()
    {
        return view('auth.emails.email');
    }

    //トークン生成、データをテーブルへ一時保存
    public function sendResetLinkEmail(ResetEmailRequest $request)
    {
        $new_email = $request->new_email;

        //トークン生成
        $token = hash_hmac(
                'sha256',
                Str::random(40). $new_email,
                config('app.key')
            );

        //DBにトークン保存
        DB::beginTransaction();
        try {
            $param = [];
            $param['user_id'] = \Auth::id();
            $param['new_email'] = $new_email;
            $param['token'] = $token;
            $email_reset = EmailReset::create($param);
            DB::commit();
            $email_reset->sendEmailResetNotification($token);
            return back()->with('msg_success', '確認メールを送信しました');
        }
        catch (Exception $e) {
            DB::rollback();
            return back()->with('msg_error', 'メール再設定に失敗しました');
        }
    }

    //メールアドレス変更処理、一時保存レコード削除
    public function showReset(Request $request, $token)
    {
        //email_resetsテーブルのトークンを取得
        $email_resets = DB::table('email_resets')
            ->where('token', $token)
            ->first();

        //トークンが存在し、かつトークンの有効期限が切れてない場合はこの処理に入る
        if ($email_resets && !$this->tokenExpired($email_resets->created_at)) {

            //ユーザーのメールアドレス更新
            $user = User::find($email_resets->user_id);
            $user->email = $email_resets->new_email;
            $user->save();

            //emais_resetsテーブルのレコード削除
            DB::table('email_resets')
                ->where('token', $token)
                ->delete();
            return redirect('/')->with('msg_success', 'メールアドレスを変更しました');
        }
        else {

            //emais_resetsテーブルに該当トークン存在していればレコード削除
            if ($email_resets) {
                DB::table('email_resets')
                    ->where('token', $token)
                    ->delete();
            }
            return redirect('/')->with('msg_error', 'メールアドレスの変更に失敗しました');
        }
    }

    //トークンの有効期限が切れていないか確認
    public function tokenExpired($created_at)
    {
        //トークンの有効期限60分
        $expires = 60 * 60;
        return Carbon::parse($created_at)->addSeconds($expires)->isPast();
    }
}