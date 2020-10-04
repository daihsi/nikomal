<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostImage extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'post_id', 'image',
    ];

    /**
    * Postモデルとのリレーション
    * 
    * @return object
    */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
