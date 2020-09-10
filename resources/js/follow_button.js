var $ = require('jquery');
var toastr = require('toastr');

$(function() {

    //いいね登録・解除処理
    $(document).on('click', '.follow', function() {

        //全体ではなくクリックされた要素のみ指定
        var $this = $(this);

        //現在のファイルパスを取得
        var url = location.pathname;

        //投稿id(文字列のみ)を取得
        var user_id = $this.attr('data-id');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/users/' + user_id + '/follow',
            type: 'POST',
            dataType: 'json',
            data: {'id': user_id},
        })
        .done(function(data) {
            var following_path = '/users/' + data['auth_id'] + '/following';
            var follower_path = '/users/' + data['auth_id'] + '/follower';

            //フォロー成功時
            if (data['follow'] === true) {
                toastr.success('フォローしました');

                //ボタン変更
                $this.attr('class', 'follow btn btn-primary btn-sm rounded-pill follow_button');
                $this.text('フォロー中');

                //現在のURLが認証ユーザーの詳細ページだった場合(フォロー・フォロワーページ)
                if (url === following_path || url === follower_path) {

                    //投稿詳細ページのナビゲーションタブのフォローカウントがコンテンツに含まれていた場合
                    if ($('.follow_count_badge').length) {
                        $('.follow_count_badge').text(data['follow_count']);
                    }

                    //ユーザー詳細ページのナビゲーションタブのフォロワーカウントがコンテンツに含まれていた場合
                    else if ($('.follower_count_badge').length) {
                        $('.follower_count_badge').text(data['follower_count']);
                    }
                }
            }

            //アンフォロー成功時
            else if (data['unfollow'] === false) {
                toastr.success('フォローを外しました');

                //ボタン変更
                $this.attr('class', 'follow btn btn-outline-primary btn-sm rounded-pill');
                $this.text('フォロー');

                //現在のURLが認証ユーザーの詳細ページだった場合(フォロー・フォロワーページ)
                if (url === following_path || url === follower_path) {

                    //ユーザー詳細ページのナビゲーションタブのフォローカウントがコンテンツに含まれていた場合
                    if ($('.follow_count_badge').length) {
                        $('.follow_count_badge').text(data['follow_count']);
                    }

                    //ユーザー詳細ページのナビゲーションタブのフォロワーカウントがコンテンツに含まれていた場合
                    else if ($('.follower_count_badge').length) {
                        $('.follower_count_badge').text(data['follower_count']);
                    }
                }
            }
        })
        .fail(function(data) {
            toastr.error('失敗しました');
        });
    });
});