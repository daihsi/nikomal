//新規登録,プロフィール編集
$('#avatarUpload').on('change', function (e) {  
    let reader = new FileReader();  
    reader.onload = function (e) {  
        $("#avatar_preview").attr('src', e.target.result)  
    };  
    reader.readAsDataURL(e.target.files[0]);  
}); 
$('#avatarUploadButton').click(function() {
    $('#avatarUpload').click();
    return false;
});

$('#avatarUpload').on('change', function (e) {  
    let reader = new FileReader();
    reader.onload = function (e) {
        $("#avatar_preview").attr('src', e.target.result)  
    };
    reader.readAsDataURL(e.target.files[0]);  
}); 
$('#avatarUploadButtonBottom').click(function() {
    $('#avatarUpload').click();
    return false;
});

//投稿編集（styleの適用を除去）
$('#post_upload').on('change', function (e) {  
    let reader = new FileReader();  
    reader.onload = function (e) {  
        $("#edit_post_image_preview").attr('src', e.target.result)  
    };  
    reader.readAsDataURL(e.target.files[0]);
});
$('#edit_post_image_preview').click(function() {
    $('#post_upload').click();
    return false;
});

//新規投稿
$('#post_upload').on('change', function (e) {  
    let reader = new FileReader();  
    reader.onload = function (e) {  
        $("#post_image_preview").attr('src', e.target.result)  
    };  
    reader.readAsDataURL(e.target.files[0]);
    //プレビュー時の画像のスタイル変更
    var post_image_preview = document.getElementById('post_image_preview');
    var post_image_preview_style = post_image_preview.style;
        post_image_preview_style.boxShadow = '0 0.5rem 1rem rgba(0, 0, 0, 0.15)';
        post_image_preview_style.backgroundColor = '#FFFFFF';
        post_image_preview_style.padding = '7px';
        post_image_preview_style.cursor = 'pointer';
        post_image_preview_style.borderRadius = '5px';
});
$('#post_image_preview').click(function() {
    $('#post_upload').click();
    return false;
});