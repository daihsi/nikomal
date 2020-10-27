var $ = require('jquery');
var jQueryBridget = require('jquery-bridget');
var Masonry = require('masonry-layout');
var InfiniteScroll = require('infinite-scroll');
var imagesLoaded = require('imagesloaded');
jQueryBridget( 'infiniteScroll', InfiniteScroll, $ );
jQueryBridget( 'masonry', Masonry, $ );
imagesLoaded.makeJQueryPlugin( $ );

//トップページ、個別ユーザー投稿一覧、個別ユーザーいいね投稿一覧
//いいねランキングページ、検索一覧ページ
$(function() {
  if ($('#post_card_container').length) {
    var $post_card_container = $('#post_card_container').masonry({
      itemSelector: 'none',
      columnWidth: '.post_sizer',
      percentPosition: true,
      stagger: 30,
      transitionDuration: '0.6s'
    });
  
    var msnry = $post_card_container.data('masonry');
  
    $post_card_container.imagesLoaded( function() {
      $post_card_container.removeClass('post_card_container');
      $post_card_container.masonry( 'option', { itemSelector: '.post_item' });
      var $items = $post_card_container.find('.post_item');
      $post_card_container.masonry( 'appended', $items );
    });
  
    InfiniteScroll.imagesLoaded = imagesLoaded;
  
    if ($('.pagination_next').length) {
      $post_card_container.infiniteScroll({
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