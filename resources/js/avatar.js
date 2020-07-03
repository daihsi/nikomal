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