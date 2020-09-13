<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ValueResetTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A Dusk test example.
     *
     * @return void
     */

    //検索フォーム内の非同期リセットボタンが機能するかテスト
    public function testValueReset()
    {
        $keyword = str_repeat('あ', 150);
        $animals_name = ['イヌ', 'ネコ', 'ウシ'];
        $this->browse(function ($browser) use ($keyword, $animals_name) {
            $browser->visitRoute('posts.search')
                    ->type('keyword', $keyword)
                    ->select('animals_name[]', $animals_name[0])
                    ->select('animals_name[]', $animals_name[1])
                    ->select('animals_name[]', $animals_name[2])
                    ->assertInputValue('keyword', $keyword)
                    ->assertSelected('animals_name[]', $animals_name[0])
                    ->assertSelected('animals_name[]', $animals_name[1])
                    ->assertSelected('animals_name[]', $animals_name[2])
                    ->press('リセット')
                    ->pause(1000)
                    ->assertInputValueIsNot('keyword', $keyword)
                    ->assertNotSelected('animals_name[]', $animals_name[0])
                    ->assertNotSelected('animals_name[]', $animals_name[1])
                    ->assertNotSelected('animals_name[]', $animals_name[2]);
        });
    }
}
