var $ = require('jquery');

$(function(){

    //投稿・コメント削除ダイヤログ
    $('.delete_alert').click(function() {
        if(confirm('削除してよろしいですか？')) {
            $('#delete_form').submit();
        }
        else {
            return false;
        }
    });

    //ログアウトダイヤログ
    $('.logout_alert').click(function(event) {
        if(confirm('ログアウトしてよろしいですか？')) {
            event.preventDefault();
            $('#logout-form').submit();
        }
        else {
            return false;
        }
    });
});