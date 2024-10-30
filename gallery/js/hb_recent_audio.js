jQuery(document).ready(function () {
    if (jQuery('#audioCountryResponse').length > 0) {

        // Get Recent played audios across the world
        jQuery.ajax({
            url: hb_object.ajaxUrl,
            method: 'POST',
            dataType: "json",
            beforeSend: function () {
                jQuery('#audioCountryResponseLoader').show();
            },
            data: {
                action: 'get_recent_audios'
            },
            success: function (response) {
                if (response) {

                    response.forEach((audio) => {
                        jQuery('#audioCountryResponse').append(`<tr><td>${audio.title}</td><td>${audio.total}</td></tr>`)

                    })
                } else {
                    jQuery('#audioCountryResponse').html('No Audios played recently!')
                }
                jQuery('#audioCountryResponseLoader').hide();
            }
        })
    }
});