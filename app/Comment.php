<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'post_id', 'comment'
    ];

    /**
    * Userモデルとのリレーション
    *
    * @return object
    */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
    * Postモデルとのリレーション
    *
    * @return object
    */
    public function post() {
        return $this->belongsTo(Post::class);
    }
}
