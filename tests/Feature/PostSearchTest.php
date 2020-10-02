<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Http\Requests\PostSearchRequest;
use App\Post;
use App\User;
use App\PostImage;
use App\Animal;

class PostSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */

    public function setUp(): void
    {
        parent::setUp();

        //一つの投稿と関係リレーションデータ同時生成
        $this->factory_user = factory(User::class)->create();
        $posts = factory(Post::class, 5)->create([
                    'user_id' => $this->factory_user->id,
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
        $this->posts = $this->factory_user->posts;

        //一つだけ投稿を取得
        foreach ($this->posts as $post) {
            $this->post = $post;
            break;
        }
    }

    public function tearDown(): void
    {
        Artisan::call('migrate:refresh');
        parent::tearDown();
    }

    //複数条件検索で投稿が表示されているかテスト
    public function testMultipleSearch(): void
    {
        //二つの値をいれて検索
        $data = [
                'keyword' => $this->post->content,
                'animals_name' => Animal::find(1)->name,
            ];
        $this->get(route('posts.search'), $data)
            ->assertSee($data['keyword'])
            ->assertSee($data['animals_name'])
            ->assertOk();
    }

    //一つの条件検索で投稿が表示されているかテスト
    public function testSingleSearch(): void
    {
        $conditions1['keyword'] = Post::find(4)->content;
        $conditions2['animals_name'] = Animal::find(3)->name;

        //キーワードのみ値を入れて検索
        $this->get(route('posts.search'), $conditions1)
            ->assertSee($conditions1['keyword'])
            ->assertOk();

        //動物カテゴリーのみ値を入れて検索
        $this->get(route('posts.search'), $conditions2)
            ->assertSee($conditions2['animals_name'])
            ->assertOk();
    }

    //検索に該当する投稿が表示されていないかテスト
    public function testNotSearch(): void
    {
        $data = [
                'keyword' => 'テストテスト',
                'animals_name' => 'テストテスト',
            ];

        $this->get(route('posts.search'), $data)
            ->assertDontSee($data['keyword'])
            ->assertDontSee($data['animals_name'])
            ->assertOk();
    }

    //桁あふれと選択数過多によるバリデーションテスト
    public function testPostSearchRequestOverFlow(): void
    {
        foreach(config('animals.animals1') as $index => $name) {
            $animals_name[] = $index;
        }
        //keywordが1文字多い・動物カテゴリーも10個以上選択したと仮定
        $data = [
            'keyword' => str_repeat('あ', 151),
            'animals_name' => $animals_name ?? null,
        ];
        $request = new PostSearchRequest;
        $rules = $request->rules();
        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();
        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'keyword' => ['Max' => [150],],
            'animals_name' => ['Max' => [10],],
        ];
        //どこがエラーになったか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //動物カテゴリーの選択が重複した時のバリデーションテスト
    public function testPostSearchRequestDuplication(): void
    {
        //動物カテゴリーを重複選択したと仮定
        $data = [
            'animals_name' => ['イヌ', 'イヌ'],
        ];
        $request = new PostSearchRequest;
        $rules = $request->rules();
        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();
        //データが偽であるか確認
        $this->assertFalse($result);
        $expectedFailed = [
            'animals_name.0' => ['Distinct' => [],],
            'animals_name.1' => ['Distinct' => [],],
        ];
        //どこがエラーになったか検証
        $this->assertEquals($expectedFailed, $validator->failed());
    }

    //バリデーションの通過テスト
    public function testPostSearchRequestNomal(): void
    {
        $animals_name = ['イヌ', 'ネコ', 'オラウータン', 'イノシシ',
                            'クジラ', 'サル', 'モグラ', 'カバ', 
                            'ウマ', 'ワニ',
                        ];

        //リクエスト値が許容内と仮定
        $data = [
            'keyword' => str_repeat('あ', 150),
            'animals_name' => $animals_name,
        ];
        $request = new PostSearchRequest;
        $rules = $request->rules();
        //バリデーションルールとデータを整合性検証
        $validator = Validator::make($data, $rules);
        $result = $validator->passes();
        //データが真であるか確認
        $this->assertTrue($result);
    }

    //カテゴリーリンクのカテゴリー別投稿検索をテスト
    public function testCategoryLink(): void
    {
        $animal = Animal::find(1);
        $animal_name = $animal->name;
        $this->get(route('posts.categorys', $animal->id))
            ->assertSee($animal_name)
            ->assertOk();
    }

    //データが存在しないカテゴリーを検索したが
    //存在しないカテゴリーで、404エラーになったかテスト
    public function testNotCategoryLink(): void
    {
        $this->get('categorys/1000')
            ->assertStatus(404);
    }
}