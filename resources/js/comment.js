let $ = require('jquery');
import {formatDate} from './format_date';
import {nl2br} from './nl2br';
import {comment_delete_dialog} from './dialog';
let toastr = require('toastr');

$(function() {

    //新規コメント投稿
    $(document).on('click', '.comment_button', function() {

        //フォームの値を取得
        let comment = $('#comment').val();
        let post_id = $('#post_id').val();

        //コメントが未入力なら処理終了
        if (comment === "") {
            toastr.error('コメントが未入力です');
            return false;
        }

        //コメント入力値があれば
        else {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/comments',
                type: 'POST',
                dataType: 'json',
                data: {
                    'post_id': post_id, 
                    'comment': comment
                },
            })
            .done(function(data) {

                //成功フラッシュメッセージを表示
                toastr.success('コメント投稿しました');

                //入力値をクリア
                $('#comment').val('');

                //コメントがない場合の表示があればそれを消去
                if($('.no_comment').length) {
                    $('.no_comment').remove();
                }

                //ナビゲーションタブのコメントカウント
                if ($('.p_comment_count_badge').length) {
                    $('.p_comment_count_badge').text(data['comment_count']);
                }

                //バリデーションのクラスがあればクラス名を削除
                if ($('.is-invalid').length) {
                    $('#comment').removeClass('is-invalid');
                }

                let html = '';

                //ユーザーのアバター画像がnullの場合は、こちらの画像を使用
                let img_src = "/storage/images/default_icon.png";

                //コメントしたユーザーの情報
                let user_id = data['comment']['user']['id'];
                let name = data['comment']['user']['name'];
                let avatar = data['comment']['user']['avatar'];

                //コメントの情報
                let comment_id = data['comment']['id'];
                let comment = nl2br(data['comment']['comment']); //nl2br関数の使用
                let created_at = formatDate(new Date(data['comment']['created_at']), 'YYYY/MM/DD hh:mm'); //formatDate関数の使用

                //アバター画像がnullならば
                if (!avatar) {
                    html = `
                            <div class="balloon overflow-hidden my-2 w-100" data-comment-id="${comment_id}">
                                <div class="balloon_chatting w-100 text-right">
                                    <a href="/users/${user_id}" class="text-dark mr-auto font-weight-bold"><div class="card-title">${name}<img src="${img_src}" class="rounded-circle ml-1" width="40" height="40" alt="${name}のアバター画像。詳細ぺージへのリンク"></div></a>
                                    <div class="authenticated_user_comment">
                                        <p>${comment}</p>
                                    </div>
                                    <div class="mb-2">
                                        <small>${created_at}</small>
                                        <button type="button" class="btn btn-link comment_trash text-danger comment_delete" data-id="${comment_id}"><i class="far fa-trash-alt"></i>削除</button>
                                    </div>
                                </div>
                            </div>
                        `;
                }

                //アバター画像があれば
                else {
                    html = `
                            <div class="balloon overflow-hidden my-2 w-100" data-comment-id="${comment_id}">
                                <div class="balloon_chatting w-100 text-right">
                                    <a href="/users/${user_id}" class="text-dark mr-auto font-weight-bold"><div class="card-title">${name}<img src="${avatar}" class="rounded-circle ml-1" width="40" height="40" alt="${name}のアバター画像。詳細ぺージへのリンク"></div></a>
                                    <div class="authenticated_user_comment">
                                        <p>${comment}</p>
                                    </div>
                                    <div class="mb-2">
                                        <small>${created_at}</small>
                                        <button type="button" class="btn btn-link comment_trash text-danger comment_delete" data-id="${comment_id}"><i class="far fa-trash-alt"></i>削除</button>
                                    </div>
                                </div>
                            </div>
                            `;
                }

                //コメントエリアに先頭にコメントを追加
                $('#comment_area').prepend(html);

                //一番最後のページのコメントを取得したらここの処理は入らない
                if ($('.balloon').length !== data['comment_count']) {

                    //各ページの最後のコメントをコメントが投稿されるたびにコンテンツから削除
                    //次ページを読みこんだ際重複するため(1ページ11コメント)
                    if ($('.balloon').length % 11 === 1) {
                        $('#comment_area .balloon:last').detach();
                    }
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {

                //もし前のバリデーションエラーメッセージが残っていれば
                //メッセージを削除
                if ($('.invalid-feedback').length) {
                    $('.invalid-feedback').remove();
                }

                //失敗フラッシュメッセージを表示
                toastr.error('コメント投稿に失敗しました');

                let text = $.parseJSON(jqXHR.responseText);

                //バリデーションエラーメッセージ取得
                let errors = text.errors;
                for (let key in errors) {

                    //繰り返してメッセージをコードに挿入
                    let errorMessage = errors[key][0];
                    let error_html = `
                                        <span class="invalid-feedback" role="alert">
                                            <strong>${errorMessage}</strong>
                                        </span>
                                    `;

                    //失敗したことを明示するスタイルを追加
                    //エラーメッセージを追加
                    $('#comment').addClass('is-invalid');
                    $('#comment_msg_error').append(error_html);
                }
            });
        }
    });

    //コメント削除
    $(document).on('click', '.comment_delete', function() {
        let $this = $(this);

        //ダイアログでokが押された場合コメント削除処理に入る
        if(comment_delete_dialog(true)) {

            //二重送信防止用のスタイルを追加
            $this.css('pointer-events','none');

            //コメントidを取得
            let d_comment_id = $this.attr('data-id');

            //投稿idを取得
            let post_id = $('#post_id').val();

            //コメントの要素数を取得
            let comment_length = $('.balloon').length;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/comments/' + d_comment_id,
                type: 'POST',
                dataType: 'json',
                data: {
                    'comment_id': d_comment_id,
                    'comment_length': comment_length,
                    'post_id': post_id,
                    '_method': 'DELETE'
                }
            })
            .done(function(data) {
                let html = '';

                //コメントの所有者ではなかったらメッセージを表示して処理終了
                if (data['message']) {
                    toastr.error(data['message']);
                    return false;
                }

                //成功フラッシュメッセージを表示
                toastr.success('コメントを削除しました');

                //バリデーションエラー明示中であれば一旦バリデーションエラークラス属性を削除
                if ($('.is-invalid').length) {
                    $('#comment').removeClass('is-invalid');
                }

                //ナビゲーションタブのコメントカウント
                if ($('.p_comment_count_badge').length) {
                    $('.p_comment_count_badge').text(data['comment_count']);
                }

                //削除されたコメントをフロント側でも削除
                $this.parents('.balloon').remove();

                //コメントが0になったらコメントが無い時の表示にし処理終了
                if (data['comment_count'] === 0) {
                    let html = `
                                <div class="d-flex justify-content-center align-items-center no_comment" style="font-size:16px; height:200px; color:rgba(0,0,0,0.4);">
                                    まだコメントがありません
                                </div>
                                `;
                    $('#comment_area').append(html);

                    //二重送信防止用のスタイル解除
                    $this.css('pointer-events','');
                    return false;
                }
                /*
                    ここからはコメントを削除した際に出る
                    次ページとのコメント差分をエリアに追加する処理
                */

                //ページが最後であればここの処理は入らない
                if ($('.balloon').length !== data['comment_count']) {

                    //認証ユーザーID取得
                    let auth_id = data['auth_id'];

                    //ユーザーのアバター画像がnullの場合は、こちらの画像を使用
                    let img_src = "/storage/images/default_icon.png";

                    //コメントしたユーザーの情報
                    let user_id = data['comment']['user']['id'];
                    let name = data['comment']['user']['name'];
                    let avatar = data['comment']['user']['avatar'];

                    //コメントの情報
                    let comment_id = data['comment']['id'];
                    let comment = nl2br(data['comment']['comment']); //nl2br関数の使用
                    let created_at = formatDate(new Date(data['comment']['created_at']), 'YYYY/MM/DD hh:mm'); //formatDate関数の使用

                    //認証ユーザーコメントならばこちらのコード生成
                    if (auth_id === user_id) {

                        //アバター画像がnullならば
                        if (!avatar) {
                            html = `
                                    <div class="balloon overflow-hidden my-2 w-100" data-comment-id="${comment_id}">
                                        <div class="balloon_chatting w-100 text-right">
                                            <a href="/users/${user_id}" class="text-dark mr-auto font-weight-bold"><div class="card-title">${name}<img src="${img_src}" class="rounded-circle ml-1" width="40" height="40" alt="${name}のアバター画像。詳細ぺージへのリンク"></div></a>
                                            <div class="authenticated_user_comment">
                                                <p>${comment}</p>
                                            </div>
                                            <div class="mb-2">
                                                <small>${created_at}</small>
                                                <button type="button" class="btn btn-link comment_trash text-danger comment_delete" data-id="${comment_id}"><i class="far fa-trash-alt"></i>削除</button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                        }

                        //アバター画像があれば
                        else {
                            html = `
                                    <div class="balloon overflow-hidden my-2 w-100" data-comment-id="${comment_id}">
                                        <div class="balloon_chatting w-100 text-right">
                                            <a href="/users/${user_id}" class="text-dark mr-auto font-weight-bold"><div class="card-title">${name}<img src="${avatar}" class="rounded-circle ml-1" width="40" height="40" alt="${name}のアバター画像。詳細ぺージへのリンク"></div></a>
                                            <div class="authenticated_user_comment">
                                                <p>${comment}</p>
                                            </div>
                                            <div class="mb-2">
                                                <small>${created_at}</small>
                                                <button type="button" class="btn btn-link comment_trash text-danger comment_delete" data-id="${comment_id}"><i class="far fa-trash-alt"></i>削除</button>
                                            </div>
                                        </div>
                                    </div>
                                    `;
                        }
                    }

                    //認証ユーザー以外のコメントならばこちらのコード生成
                    else {

                        //アバター画像がnullならば
                        if (!avatar) {
                            html = `
                                    <div class="balloon overflow-hidden my-2 w-100" data-comment-id="${comment_id}">
                                        <div class="balloon_chatting overflow-hidden w-100 text-left">
                                            <a href="/users/${user_id}" class="text-dark mr-auto font-weight-bold"><div class="card-title"><img src="${img_src}" class="rounded-circle mr-1" width="40" height="40" alt="${name}のアバター画像。詳細ぺージへのリンク">${name}</div></a>
                                            <div class="user_comment">
                                                <p>${comment}</p>
                                            </div>
                                            <div class="mb-4 ml-4">
                                                <small>${created_at}</small>
                                            </div>
                                        </div>
                                    </div>
                                    `;
                        }

                        //アバター画像があれば
                        else {
                            html = `
                                    <div class="balloon overflow-hidden my-2 w-100" data-comment-id="${comment_id}">
                                        <div class="balloon_chatting overflow-hidden w-100 text-left">
                                            <a href="/users/${user_id}" class="text-dark mr-auto font-weight-bold"><div class="card-title"><img src="${avatar}" class="rounded-circle mr-1" width="40" height="40" alt="${name}のアバター画像。詳細ぺージへのリンク">${name}</div></a>
                                            <div class="user_comment">
                                                <p>${comment}</p>
                                            </div>
                                            <div class="mb-4 ml-4">
                                                <small>${created_at}</small>
                                            </div>
                                        </div>
                                    </div>
                                    `;
                        }
                    }

                    //次ページを読みこんだ際要素が消えているためこの条件式 (1ページ11コメント)
                    if ($('.balloon').length % 11 === 10) {

                        //コメントエリアの最後にコメントを追加
                        $('#comment_area').append(html);
                    }

                    //二重送信防止用のスタイル解除
                    $this.css('pointer-events','');
                }
                /*
                    ここまでがコメントの追加処理
                */
            })
            .fail(function(data) {

                //失敗フラッシュメッセージを表示
                toastr.error('コメント削除に失敗しました');

                //二重送信防止用のスタイル解除
                $this.css('pointer-events','');
            });
        }

        //ダイアログのキャンセルを押したら処理終了
        else {
            return false;
        }
    });
});
