var $ = require('jquery');
var jQueryBridget = require('jquery-bridget');
var InfiniteScroll = require('infinite-scroll');

jQueryBridget( 'infiniteScroll', InfiniteScroll, $ );

//ユーザー一覧、フォロー一覧、フォロワー一覧
$(function() {
    $('#user_list').infiniteScroll({
        path: '.pagination_next',
        append: '.user_card',
        history: false,
        button: '.view_more_button',
        scrollThreshold: false,
        hideNav: '.pagination',
        status: '.page_load_status',
    });
});


//コメント一覧
$(function() {
    $('#comment_area').infiniteScroll({
        path: '.comment_next',
        append: '.balloon',
        history: false,
        hideNav: '.pagination',
        status: '.page_load_status',
    });
});