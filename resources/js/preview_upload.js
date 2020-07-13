//新規登録,プロフィール編集
$('#avatarUpload').on('change', function (e) {  
    let reader = new FileReader();  
    reader.onload = function (e) {  
        $("#avatar_preview").attr('src', e.target.result)  
    }  
    reader.readAsDataURL(e.target.files[0]);  
}); 

$('#avatarUploadButton').click(function(){
    $('#avatarUpload').click();
    return false;
});

$('#avatarUpload').on('change', function (e) {  
    let reader = new FileReader();  
    reader.onload = function (e) {  
        $("#avatar_preview").attr('src', e.target.result)  
    }  
    reader.readAsDataURL(e.target.files[0]);  
}); 

$('#avatarUploadButtonBottom').click(function(){
    $('#avatarUpload').click();
    return false;
});