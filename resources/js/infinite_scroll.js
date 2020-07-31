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

var following_list = document.getElementById('following_list');
var infScroll = new InfiniteScroll( following_list, {
    path: '.followings_pagination_next',
    append: '.following_card',
    history: false,
    button: '.following_more_button',
    scrollThreshold: false,
    hideNav: '.pagination',

});

var follower_list = document.getElementById('follower_list');
var infScroll = new InfiniteScroll( follower_list, {
    path: '.follower_pagination_next',
    append: '.following_card',
    history: false,
    button: '.follower_more_button',
    scrollThreshold: false,
    hideNav: '.pagination',

});