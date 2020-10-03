<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\User;
use App\Post;
use App\PostImage;
use App\Animal;

class PreviewUploadTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();

        //投稿をリレーションデータを含めて生成
        $this->posts = factory(Post::class)->create([
                    'user_id' => $this->user->id,
                    ])
                    ->each(function ($post) {
                        //画像データの生成
                        $post->postImages()
                            ->save(
                                factory(PostImage::class)->make()
                            );
                        //動物カテゴリーデータの生成
                        $post->postCategorys()
                            ->createMany(
                                factory(Animal::class, 3)->make()
                                ->toArray()
                            );
                    });

        // 仮画像作成
        Storage::fake('images');
        $this->upload_file = UploadedFile::fake()->image('test.jpg');
        $this->upload_file->move('storage/framework/testing/disks/images');
        $this->file_name = $this->upload_file->getFilename();
    }

    //新規ユーザー登録・ユーザー編集の選択画像がプレビュー表示されているかテスト
    public function testUserImagePreview(): void
    {
        $this->browse(function ($frist, $second) {

            //ユーザー登録ページ
            $frist->visit('/register')
                    ->click('#avatarUploadButton')
                    ->attach('#avatarUpload', 'storage/framework/testing/disks/images/'.$this->file_name)
                    ->screenshot('users_avatar') //画像が切り替わっているか確認
                    ->assertSourceHas('image/'); //プレビュー用のソースコードに切り替わっているか確認

            //ユーザー編集ページ
            $second->loginAs($this->user)
                    ->visitRoute('users.edit', $this->user->id)
                    ->click('#avatarUploadButton')
                    ->attach('#avatarUpload', 'storage/framework/testing/disks/images/'.$this->file_name)
                    ->screenshot('users_avatar') //画像が切り替わっているか確認
                    ->assertSourceHas('image/'); //プレビュー用のソースコードに切り替わっているか確認
        });
    }

    //新規投稿・投稿編集の選択画像がプレビュー表示されているかテスト
    public function testPostImagePreview(): void
    {
        $post = Post::find(1);
        $this->browse(function ($frist, $second) use ($post){

            //新規投稿ページ
            $frist->loginAs($this->user)
                    ->visitRoute('posts.create')
                    ->click('#post_image_preview')
                    ->attach('#post_upload', 'storage/framework/testing/disks/images/'.$this->file_name)
                    ->screenshot('post_image') //画像が切り替わっているか確認
                    ->assertSourceHas('image/'); //プレビュー用のソースコードに切り替わっているか確認
        
            //投稿編集ページ
            $second->loginAs($this->user)
                    ->visitRoute('posts.edit', $post->id)
                    ->click('#edit_post_image_preview')
                    ->attach('#post_upload', 'storage/framework/testing/disks/images/'.$this->file_name)
                    ->screenshot('post_image') //画像が切り替わっているか確認
                    ->assertSourceHas('image/'); //プレビュー用のソースコードに切り替わっているか確認
        });
    }
}