var InfiniteScroll = require('infinite-scroll');

var user_list = document.getElementById('user_list');
var infScroll = new InfiniteScroll( user_list, {
    path: '.pagination_next',
    append: '.user_card',
    history: false,
    button: '.view_more_button',
    scrollThreshold: false,
    hideNav: '.pagination',
});

var comment_area = document.getElementById('comment_area');
var infScroll = new InfiniteScroll( comment_area, {
    path: '.comment_next',
    append: '.balloon',
    history: false,
    button: '.comment_more_button',
    scrollThreshold: false,
    hideNav: '.pagination',
});