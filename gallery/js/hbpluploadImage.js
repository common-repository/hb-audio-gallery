jQuery.fn.exists = function () {
    return jQuery(this).length > 0;
}
$ = jQuery

//After completion of image upload process
function completeUpload(success, fileName, aid) {
    if (success) {
        $(`#imagePreview${aid}`).attr("src", "");
        $(`#imagePreview${aid}`).attr("src", fileName);
        $(`#fileInput${aid}`).attr("value", fileName);
        $('.uploadProcess').hide();
    } else {
        $('.uploadProcess').hide();
        alert('There was an error during file upload!');
    }
    return true;
}

function clickeditem(item) {
    var aid = $(item).attr("id");
    $(`#fileInput${aid}:hidden`).trigger('click');
   
    $(`#fileInput${aid}`).on('change', function () {
        var image = $(`#fileInput${aid}`).val();
        var img_ex = /(\.jpg|\.jpeg|\.png|\.gif)$/i;

        if (!img_ex.exec(image)) {
            alert('Please upload only .jpg/.jpeg/.png/.gif file.');
            $(`#fileInput${aid}`).val('');
        } else {
            $('.uploadProcess').show();
            $('#uploadForm').hide();
            $(`#picUploadForm${aid}`).submit();
        }
        });
}
