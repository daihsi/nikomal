<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar', 'self_introduction',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //Postモデルとのリレーション
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    //投稿数、フォロー数、フォロワー数のカウント
    public function loadRelationshipCounts()
    {
        $this->loadCount(['posts', 'followings', 'followers']);
    }

    //Userモデルとのリレーション
    public function followings() {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'follow_id')->withTimestamps();
    }

    //Userモデルとのリレーション
    public function followers() {
        return $this->belongsToMany(User::class, 'user_follow', 'follow_id', 'user_id')->withTimestamps();
    }

    //フォロー処理
    public function follow($user_id) {
        $exist = $this->isFollowing($user_id);
        $its_me = $this->id === $user_id;

        if($exist || $its_me) {
            return false;
        }
        else {
            $this->followings()->attach($user_id);
            return true;
        }
    }

    //フォローを外す処理
    public function unfollow($user_id) {
        $exist = $this->isFollowing($user_id);
        $its_me = $this->id === $user_id;

        if($exist && !$its_me) {
            $this->followings()->detach($user_id);
            return true;
        }
        else {
            return false;
        }
    }

    //フォロー中であるか調べる処理
    public function isFollowing($user_id) {
        return $this->followings()->where('follow_id', $user_id)->exists();
    }
}
