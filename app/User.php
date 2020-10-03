<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordJP as ResetPasswordNotificationJP;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'self_introduction',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * パスワードリセットメール日本語化
     * 
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotificationJP($token));
    }

    /**
    * Postモデルとのリレーション
    *
    * @return object
    */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
    * 投稿数、フォロー数、フォロワー数、いいね数のカウント
    *
    * @return void
    */
    public function loadRelationshipCounts() {
        $this->loadCount(['posts', 'followings', 'followers', 'likes']);
    }

    /**
    * Userモデルとのリレーション
    *
    * @return object
    */
    public function followings() {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    /**
    * Userモデルとのリレーション
    *
    * @return object
    */
    public function followers() {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }

    /**
    * フォロー・アンフォロー処理
    *
    * @param  int  $user_id
    * @return array|bool
    */
    public function follow($user_id) {
        $exist = $this->isFollowing($user_id);
        $its_me = $this->id === $user_id;

        if(!$exist && !$its_me) {
            $this->followings()->attach($user_id);
            return [
                'follow' => 'follow',
                ];
        }
        elseif($exist && !$its_me) {
            $this->followings()->detach($user_id);
            return [
                'unfollow' => 'unfollow',
                ];
        }
        else {
            return false;
        }
        
    }

    /**
    * フォロー中であるか調べる処理
    *
    * @param  int  $user_id
    * @return boolean
    */
    public function isFollowing($user_id) {
        return $this->followings()->where('follow_id', $user_id)->exists();
    }

    /**
    * Postモデルとのリレーション
    *
    * @return object
    */
    public function likes() {
        return $this->belongsToMany(Post::class, 'likes', 'user_id', 'post_id')->withTimestamps();
    }

    /**
    * 投稿にいいねする処理、投稿のいいねを外す処理
    *
    * @param  int  $post_id
    * @return boolean
    */
    public function like($post_id) {
        $exist = $this->isLike($post_id);

        if($exist) {
            $this->likes()->detach($post_id);
            return false;
        }
        else {
            $this->likes()->attach($post_id);
            return true;
        }
    }

    /**
    * いいねしているか調べる処理
    *
    * @param  int  $post_id
    * @return boolean
    */
    public function isLike($post_id) {
        //いいねリクエストされた投稿のidが、すでにuser_idと結び中間テーブルに存在するか
        return $this->likes()->where('post_id', $post_id)->exists();
    }

    /**
    * Commentモデルとのリレーション
    *
    * @return object
    */
    public function userComments()
    {
        return $this->hasMany(Comment::class);
    }
}