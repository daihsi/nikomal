<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Notifications\ResetEmail;
use Illuminate\Notifications\Notifiable;

class EmailReset extends Model
{
    use Notifiable;

    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'new_email', 'token',
    ];

    /**
     * メールアドレス確定メールを送信
     *
     * @param  string|null  $token
     * @return void
     */
    public function sendEmailResetNotification($token)
    {
        $this->notify(new ResetEmail($token));
    }

    /**
     * 新しいメールアドレスあてにメールを送信する
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForMail($notification)
    {
        return $this->new_email;
    }
}
