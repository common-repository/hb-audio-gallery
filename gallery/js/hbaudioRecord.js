jQuery.fn.exists = function () {
    return jQuery(this).length > 0;
}

// click on play button
const clickPlayButton = function (player, id) {
    var aid = '';

    switch (player) {
        case 'gallery':
            jQuery('a.jp-playlist-current').each(function () {
                if (jQuery(this).next().attr('data-name') == 'gallery') {
                    aid = jQuery(this).next().attr('data-aid')
                }
            })
            break;

        case 'category':
            console.log('category1')
            jQuery('a.jp-playlist-current').each(function () {
                console.log('category2')
                if (jQuery(this).next().attr('data-name') == 'category') {
                    aid = jQuery(this).next().attr('data-aid')
                }
            })
            break;

        case 'favourite':
            jQuery('a.jp-playlist-current').each(function () {
                if (jQuery(this).next().attr('data-name') == 'favourite') {
                    aid = jQuery(this).next().attr('data-aid')
                }
            })
            break;

        case 'daily-top':
            jQuery('a.jp-playlist-current').each(function () {
                if (jQuery(this).next().attr('data-name') == 'daily-top') {
                    aid = jQuery(this).next().attr('data-aid')
                }
            })
            break;

        case 'weekly-top':
            jQuery('a.jp-playlist-current').each(function () {
                if (jQuery(this).next().attr('data-name') == 'weekly-top') {
                    aid = jQuery(this).next().attr('data-aid')
                }
            })
            break;
        case 'single':
                    aid = id;
            break;
    }

    jQuery.ajax({
        url: hb_object.ajaxUrl,
        method: 'POST',
        data: {
            action: 'hb_track_record',
            audio_id: aid
        }
    })
}

jQuery(document).ready(function () {

    setTimeout(() => {
        let targetNode = document.querySelectorAll('a.jp-playlist-item')

        targetNode.forEach((i) => {
            let classWatcher = new ClassWatcher(i, 'jp-playlist-current', (aid) => {

                jQuery.ajax({
                    url: hb_object.ajaxUrl,
                    method: 'POST',
                    data: {
                        action: 'hb_track_record',
                        audio_id: aid
                    }
                })
            })
        })

    }, 1000)

});
