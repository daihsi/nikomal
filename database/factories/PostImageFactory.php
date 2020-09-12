<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\PostImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Faker\Generator as Faker;

$factory->define(PostImage::class, function (Faker $faker) {

    //画像(縦)
    $height = [200, 250, 300, 350, 400, 450, 500, 550];

    //縦サイズのみリサイズしないのでランダムにサイズ変更する
    //横サイズはs3保存時に400pxでリサイズするので固定
    $key = array_rand($height, 1);

    Storage::fake('post_images');
    $upload_file = UploadedFile::fake()->image('test.jpg', 400, $height[$key])->size(2048);
    $upload_file->move('storage/framework/testing/disks/post_images');
    $file_name = $upload_file->getFilename();
    
    return [
        'image' => 'storage/framework/testing/disks/post_images/'.$file_name,
    ];
});
