<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'content', 
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
    * PostImageモデルとのリレーション
    *
    * @return object
    */
    public function postImages() {
        return $this->hasMany(PostImage::class);
    }

    /**
    * 投稿画像、ある一つの投稿にいいねしているユーザーのカウント
    *
    * @return void
    */
    public function loadRelationshipCounts() {
        $this->loadCount(['postImages', 'likes']);
    }

    /**
    * Animalモデルとのリレーション
    *
    * @return object
    */
    public function postCategorys() {
        return $this->belongsToMany(Animal::class, 'post_category', 'post_id', 'animal_id')->withTimestamps();
    }

    /**
    * animal_idで指定されたカテゴリーに属する
    *
    * @param int $animal_id
    * @return bool
    */
    public function belongsToCategory($animal_id) {
        $exist = $this->isBelongsToCategory($animal_id);
        
        if ($exist) {
            return false;
        }
        else {
            $this->postCategorys()->attach($animal_id);
            return true;
        }
    }

    /**
    * animal_idで指定されたカテゴリーから外れる
    *
    * @param int $animal_id
    * @return bool
    */
    public function removeBelngsToCategory($animal_id) {
        $exist = $this->isBelongsToCategory($animal_id);
        
        if($exist) {
            $this->postCategorys()->detach($animal_id);
            return true;
        }
        else {
            return false;
        }
    }

    /**
    * animal_idで指定されたカテゴリーにこの投稿が属しているか調べる
    *
    * @param int $animal_id
    * @return bool
    */
    public function isBelongsToCategory($animal_id) {
        return $this->postCategorys()->where('animal_id',$animal_id)->exists();
    }

    /**
    * Userモデルとのリレーション
    *
    * @return object
    */
    public function likes() {
        return $this->belongsToMany(User::class, 'likes', 'post_id', 'user_id')->withTimestamps();
    }

    /**
    * Commentモデルとのリレーション
    *
    * @return object
    */
    public function postComments() {
        return $this->hasMany(Comment::class);
    }

    /**
     * 投稿検索
     * 
     * @param array $data
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePostSearch($query, $data)
    {
            $animals = $data['animals_name'] ?? null;
            $keyword = $data['keyword'];

            //どちらにも値が入っている場合の検索
            if (filled($animals) && filled($keyword)) {
                $query->where('content', 'LIKE', '%'.$keyword.'%')
                    ->whereHas('postCategorys', function($query) use($animals) {
                        $query->whereIn('name', $animals);
                    });
            }
    
            //キーワードのみ値が入っている場合の検索
            elseif (filled($keyword) && empty($animals)) {
                $query->where('content', 'LIKE', '%'.$keyword.'%');
            }
    
            //動物カテゴリーのみ値が入っている場合の検索
            elseif (filled($animals) && empty($keyword)) {
                $query->whereHas('postCategorys', function($query) use($animals) {
                        $query->whereIn('name', $animals);
                    });
            }
            return $query;
    }
}