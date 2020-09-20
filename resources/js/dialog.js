var $ = require('jquery');

//コメント削除ダイアログ関数
export function comment_delete_dialog() {
    if(confirm('コメントを削除してよろしいですか？')) {
        return true;
    }
    else {
        return false;
    }
}

$(function(){

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