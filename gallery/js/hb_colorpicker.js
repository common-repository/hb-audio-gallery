jQuery(document).ready(function ($) {
    jQuery('.hbag-color-field').wpColorPicker();
    
    jQuery('#hbag-color-picker .hbag-color-field').on('input', (e) => {
        console.log(e)
        // console.log(jQuery('#hbag-color-picker input.hbag-color-field').val());
    })
});

const changeColorpickerColor = () => {
    let $ = jQuery;
    let ht_content = $("#ht_content").val();

    $("#save-hbag-colorpicker").html('Audio player color has been updated.');
    $("#save-hbag-colorpicker").css("display", "block");
}