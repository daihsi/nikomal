var $ = require('jquery');

//検索フォームのリセットボタン
$('.s_reset_button').on('click', function(){
    $('.search_animals').val(null).trigger('change');
    $('.search_text').val('');
}); 