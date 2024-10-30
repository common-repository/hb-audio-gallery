<?php

function hbag_single_audio($atts)
{
    extract(shortcode_atts(array(
        'aid' => '',
        'autoplay' => 'no',
        'view' => 'list',
    ), $atts));
    global $wpdb;
    $audio = hbag_db_get_audio($aid);
    $output = '';

    if (!empty($audio)) {
        $args = [
            'aid' => $aid,
            'autoplay' => $autoplay,
            'view' => $view,
            'audio' => $audio,
            'theme_color'=> get_option('theme_color'),
            'theme_secondary_color'=> get_option('theme_secondary_color'),
        ];
        ob_start();
        load_template(HBAG_PLUGIN_DIR . '/templates/single_audio.php', false, $args);
        $output =  ob_get_clean();
        wp_reset_postdata();

    }
    return $output;
}
