var $ = require('jquery');
var jQueryBridget = require('jquery-bridget');
var InfiniteScroll = require('infinite-scroll');

jQueryBridget('infiniteScroll', InfiniteScroll, $);

$('#user_list').infiniteScroll({
    path: '.pagination_next',
    append: '.card-group',
    history: false,
    button: '.view_more_button',
    scrollThreshold: false,
    hideNav: '.pagination',
});