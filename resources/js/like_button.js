var $ = require('jquery');
var toastr = require('toastr');

$(function() {

    //いいね登録・解除処理
    $(document).on('click', '.like_button', function() {

        //全体ではなくクリックされた要素のみ指定
        var $this = $(this);

        //投稿id(文字列のみ)を取得
        var post_id = $this.attr('data-id');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/posts/' + post_id + '/like',
            type: 'POST',
            dataType: 'json',
            data: {'id': post_id},
        })
        .done(function(data) {

            //いいね登録成功時
            if (data['like'] === true) {
                toastr.success('投稿にいいねしました');

                //アイコンの色変更(ピンク色へ)
                $this.attr('class', 'like_button btn btn like_now_icon fas fa-heart fa-lg');
                $this.next('span').text(data['p_count']);

                //投稿詳細ページのナビゲーションタブのいいねカウントがコンテンツに含まれていた場合
                if ($('.p_count_badge').length) {
                    $('.p_count_badge').text(data['p_count']);
                }

                //ユーザー詳細ページのナビゲーションタブのいいねカウントがコンテンツに含まれていた場合
                else if ($('.u_count_badge').length) {
                    $('.u_count_badge').text(data['u_count']);
                }
            }

            //いいね解除成功時
            else if (data['unlike'] === false) {
                toastr.success('投稿のいいねを外しました');

                //アイコンの色変更(白色へ)
                $this.attr('class', 'like_button btn btn like_icon far fa-heart fa-lg');
                $this.next('span').text(data['p_count']);

                //投稿詳細ページのナビゲーションタブのいいねカウントがコンテンツに含まれていた場合
                if ($('.p_count_badge').length) {
                    $('.p_count_badge').text(data['p_count']);
                }

                //ユーザー詳細ページのナビゲーションタブのいいねカウントがコンテンツに含まれていた場合
                else if ($('.u_count_badge').length) {
                    $('.u_count_badge').text(data['u_count']);
                }
            }
        })
        .fail(function(data) {
            toastr.error('失敗しました');
        });
    });
});