<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'post_id', 'comment'
    ];

    //Userクラスとのリレーション
    public function user() {
        return $this->belongsTo(User::class);
    }

    //Postクラスとのリレーション
    public function post() {
        return $this->belongsTo(Post::class);
    }
}
