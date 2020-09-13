var $ = require('jquery');

$(function() {
    var appear = false;
    var pagetop = $('#page_top_button');
    $(window).scroll(function () {

        //500pxスクロールした場合ボタン出現
        if ($(this).scrollTop() > 500) {
            if (appear == false) {
                appear = true;

                //下から10pxの位置に0.3秒かけて出現
                pagetop.stop().animate({
                    'bottom': '10px',
                },300);
            }
        } else {
            if (appear) {
                appear = false;

                //下から-70pxの位置に0.3秒かけて隠れる
                pagetop.stop().animate({
                    'bottom': '-70px',
                }, 300);
            }
        }
    });

    //0.5秒かけてページトップへ戻る
    pagetop.click(function () {
        $('body, html').animate({
            scrollTop: 0
        }, 500);
        return false;
    });
});