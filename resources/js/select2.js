var $ = require('jquery');
require('select2');
require('select2/dist/js/i18n/ja.js');

$(document).ready(function() {
    $('#animals_select').select2({
        maximumInputLength: 15,
        maximumSelectionLength: 3,
        placeholder: 'カタカナで動物を検索できます', 
        language: 'ja',
        width: '100%',
    });
});