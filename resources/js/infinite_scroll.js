var $ = require('jquery');
var jQueryBridget = require('jquery-bridget');
var InfiniteScroll = require('infinite-scroll');

jQueryBridget('infiniteScroll', InfiniteScroll, $);

$('.card-group').infiniteScroll({
    path: '.pagination_next',
    append: '#user_list',
    history: false,
    button: '.view_more_button',
    scrollThreshold: false,
    hideNav: '.pagination',
});