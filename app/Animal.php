<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Postモデルとのリレーション
     *
    * @return object
    */
    public function categoryPosts()
    {
        return $this->belongsToMany(Post::class, 'post_category', 'animal_id', 'post_id')->withTimestamps();
    }
    
}
