var $ = require('jquery');
require('select2');
require('select2/dist/js/i18n/ja.js');

//新規投稿・投稿変更フォーム
$(document).ready(function() {
    $('#animals_select').select2({
        maximumInputLength: 15,
        maximumSelectionLength: 3,
        placeholder: '選択してください', 
        language: 'ja',
        width: '100%',
        closeOnSelect: false,
    });
});

//検索フォーム
$(document).ready(function() {
    $('#animals_search').select2({
        maximumInputLength: 15,
        maximumSelectionLength: 10,
        placeholder: '選択してください', 
        language: 'ja',
        width: '100%',
        closeOnSelect: false,
    });
});