var Masonry = require('masonry-layout');
var InfiniteScroll = require('infinite-scroll');
var imagesLoaded = require('imagesloaded');

//トップページ、個別ユーザー投稿一覧、個別ユーザーいいね投稿一覧
//いいねランキングページ、検索一覧ページ
document.addEventListener('DOMContentLoaded', function() {

  //投稿自体が存在するかチェック
  if (document.getElementById('post_card_container') != null) {

    //投稿コンテンツ全体の取得
    var post_card_container = document.querySelector('#post_card_container');

    //masonryのオプション記述(1つ1つの投稿はまだ取得しない)
    var msnry = new Masonry( post_card_container, {
      itemSelector: '.post_item',
      columnWidth: '.post_sizer',
      percentPosition: true,
      stagger: 30,
      transitionDuration: '0.7s',
    });

    //次のページがあれば、無限スクロールの処理に入る
    if (document.getElementById('pagination_next') != null) {
      InfiniteScroll.imagesLoaded = imagesLoaded;
      var infScroll = new InfiniteScroll( post_card_container, {
        path: '.pagination_next',
        append: '.post_item',
        outlayer: msnry,
        history: false,
        hideNav: '.pagination',
        status: '.page_load_status',
      });
    }
  }
});