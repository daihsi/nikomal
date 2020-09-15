var $ = require('jquery');

$(function(){

    //コメント削除ダイヤログ
    $(document).on('click', '.comment_delete_alert', function() {
        var $this = $(this);
        if(confirm('コメントを削除してよろしいですか？')) {
            $this.submit();
        }
        else {
            return false;
        }
    });

    //投稿削除ダイヤログ
    $(document).on('click', '.post_delete_alert', function() {
        var $this = $(this);
        if(confirm('投稿を削除してよろしいですか？')) {
            $this.submit();
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