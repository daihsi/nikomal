var Masonry = require('masonry-layout');
var InfiniteScroll = require('infinite-scroll');
var imagesLoaded = require('imagesloaded');

//トップページ、個別ユーザー投稿一覧、個別ユーザーいいね投稿一覧
var post_card_container = document.getElementById('post_card_container');

InfiniteScroll.imagesLoaded = imagesLoaded;
var msnry = new Masonry( post_card_container, {
        itemSelector: '.post_item',
        columnWidth: '.post_sizer',
        percentPosition: true,
        stagger: 30,
        visibleStyle: { transform: 'translateY(0)', opacity: 1 },
        hiddenStyle: { transform: 'translateY(100px)', opacity: 0 },
    });

var infScroll = new InfiniteScroll( post_card_container, {
    path: '.pagination_next',
    append: '.post_item',
    outlayer: msnry,
    button: '.view_more_button',
    history: false,
    scrollThreshold: false,
    hideNav: '.pagination',
});
