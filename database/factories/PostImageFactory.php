<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use App\PostImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Faker\Generator as Faker;

$factory->define(PostImage::class, function (Faker $faker) {

    Storage::fake('post_images');
    $upload_file = UploadedFile::fake()->image('test.jpg')->size(2048);
    $upload_file->move('storage/framework/testing/disks/post_images');
    $file_name = $upload_file->getFilename();
    
    return [
        'image' => 'storage/framework/testing/disks/post_images/'.$file_name,
    ];
});
