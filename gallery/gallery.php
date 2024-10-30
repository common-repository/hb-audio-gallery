<?php

use GeoIp2\WebService\Client;

class hbag_wp_gallery
{

    function __construct()
    {
        require_once(dirname(__FILE__) . '/gallery-function.php');
        require_once(dirname(__FILE__) . '/gallery-shortcode.php');

        add_action('init', array(&$this, 'create_gallery_post_type'), 11);
        add_action('admin_init', array(&$this, 'gallery_meta_baox'));
        add_action('save_post',  array(&$this, 'save_gallery_review_detail'), 10, 2);

        add_action('init', array(&$this, 'themes_taxonomy'), 15);

        add_filter('template_include', array(&$this, 'gallery_template_include'), 1);
        add_filter('manage_edit-hbag_audio_gallery_columns',  array(&$this, 'add_columns'));
        add_filter("manage_edit-hb_ag_category_columns", array(&$this, 'theme_columns'));
        add_filter('manage_hb_ag_category_custom_column', array(&$this, 'manage_theme_columns'), 10, 3);
        add_action('manage_posts_custom_column', array(&$this, 'populate_columns'));
        add_filter('manage_edit-hbag_audio_gallery_sortable_columns', array(&$this, 'author_column_sortable'));
        add_filter('request', array(&$this, 'column_ordering'));

        add_action('wp_trash_post', array(&$this, 'delete_gallery'));

        add_shortcode('hb-audio-single-audio', 'hbag_single_audio');

        add_action('wp_head', array(&$this, 'addthis_config_js'));
        add_action('admin_menu', array(&$this, 'hbag_setting_menu'));
        add_action('admin_post_save_hb_ag_option', array(&$this, 'process_ag_options'));
        add_action('admin_post_update_player_theme_color', array(&$this, 'process_theme_color_option'));

        // Recent played audios across the world
        add_action('wp_dashboard_setup', array(&$this, 'hbag_recent_audio_by_country_dasboard_widget'));

        // Recent played audios total count by country
        add_action('wp_dashboard_setup', array(&$this, 'hbag_recent_audio_count_by_country_dasboard_widget'));
    }

    function create_gallery_post_type()
    {

        $icon_path = HBAG_URLPATH . 'gallery/images/music_icon_16.png';

        register_post_type(
            'hbag_audio_gallery',
            array(
                'labels' => array(
                    'name' => 'HB Audio Gallery',
                    'singular_name' => 'Gallery Review',
                    'add_new' => 'Add New',
                    'add_new_item' => 'Add New HB Audio Gallery',
                    'edit' => 'Edit',
                    'edit_item' => 'Edit HB Audio Gallery',
                    'new_item' => 'New HB Audio Gallery',
                    'view' => 'View',
                    'view_item' => 'View HB Audio Gallery',
                    'search_items' => 'Search HB Audio Gallery',
                    'not_found' => 'No HB Audio Gallery found',
                    'not_found_in_trash' => 'No HB Audio Gallery found in Trash',
                    'parent' => 'Parent HB Audio Gallery'
                ),
                'public' => true,
                'menu_position' => 20,
                'taxonomies' => array('hb_ag_category'),
                'supports' => array('title', 'thumbnail'),
                'menu_icon' => $icon_path,
                'has_archive' => true
            )
        );


        remove_post_type_support('hbag_audio_gallery', 'editor');
    }


    function gallery_meta_baox()
    {
        add_meta_box(
            'gallery_review_detail_meta_box',
            'Gallery Detail',
            'display_gallery_review_detail_meta_box',
            'hbag_audio_gallery',
            'normal',
            'high'
        );

        add_meta_box(
            'gallery_review_upload_meta_box',
            'Upload Audio Files ',
            'display_gallery_review_upload_meta_box',
            'hbag_audio_gallery',
            'normal',
            'high'
        );

        add_meta_box(
            'gallery_review_audiolist_meta_box',
            'Audio List',
            'display_gallery_review_audiolist_meta_box',
            'hbag_audio_gallery',
            'normal',
            'high'
        );
    }


    function save_gallery_review_detail($gallery_review_id, $gallery_review)
    {
        if ($gallery_review->post_type == 'hbag_audio_gallery') {
            // Store data in post meta table if present in post data
            if (isset($_POST['gallery_review_author_name']) && $_POST['gallery_review_author_name'] != '') {
                update_post_meta(
                    $gallery_review_id,
                    'gallery_author',
                    sanitize_text_field($_POST['gallery_review_author_name'])
                );
            }

            if (isset($_POST['gallery_review_upload_dir']) && $_POST['gallery_review_upload_dir'] != '') {
                update_post_meta(
                    $gallery_review_id,
                    'gallery_upload_dir',
                    sanitize_text_field($_POST['gallery_review_upload_dir'])
                );
            }
            hbag_addgallery_process();
        }
    }


    function gallery_template_include($template_path)
    {
        if (get_post_type() == 'hbag_audio_gallery') {
            if (is_single()) {
                // checks if the file exists in the theme first,
                // otherwise serve the file from the plugin
                if ($theme_file = locate_template(array('single-ag_reviews.php'))) {
                    $template_path = $theme_file;
                } else {
                    $template_path = plugin_dir_path(__FILE__) .
                        '/single-ag_reviews.php';
                }
            } elseif (is_archive()) {
                if ($theme_file = locate_template(array('archive-book_reviews.php'))) {
                    $template_path = $theme_file;
                } else {
                    $template_path = plugin_dir_path(__FILE__) .
                        '/archive-ag_reviews.php';
                }
            }
        }
        return $template_path;
    }


    function add_columns($columns)
    {
        $columns = array_splice($columns, 0, 1, true) + array('gallery_id' => 'ID') + array_splice($columns, 1, count($colums) - 1, true);
        $columns['gallery_author'] = 'Author';
        $columns['gallery_shortcode'] = 'Shortcode';
        unset($columns['comments']);
        return $columns;
    }


    function populate_columns($column)
    {
        if ('gallery_author' == $column) {
            $gallery_author = esc_html(get_post_meta(get_the_ID(), 'gallery_author', true));
            echo $gallery_author;
        }
        if ('gallery_id' == $column) {
            $gallery_author = esc_html(get_the_ID());
            echo $gallery_author;
        }
        if ('gallery_shortcode' == $column) {
            $gallery_author = '[hb-audio-list-in-gallery gid=' . get_the_ID() . ' autoplay="no"]';
            echo $gallery_author;
        }
    }



    function author_column_sortable($columns)
    {
        $columns['gallery_author'] = 'gallery_author';
        return $columns;
    }


    function column_ordering($vars)
    {

        if (!is_admin())
            return $vars;
        if (isset($vars['orderby']) && 'gallery_author' == $vars['orderby']) {
            //Empty for now
        } else {
            //Empty for now
        }
        return $vars;
    }


    function addthis_config_js($query)
    {
        echo '<meta id="hb-title-meta" property="og:title" content="' . get_the_title() . '" />
        <meta property="og:description" content="' . get_bloginfo('description') . '" />
        <meta id="hb-url-meta" property="og:url" content="' . get_permalink() . '" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <script type="text/javascript">
            if (typeof addthis_config !== "undefined") {
                addthis_config.data_track_addressbar = false,
                addthis_config.ui_show_promo = false,
                addthis_config.data_track_clickback = false,
                addthis_config.services_exclude= "facebook"
            } else {
                var addthis_config = {
                    data_track_addressbar: false,
                    ui_show_promo: false,
                    data_track_clickback: false,
                    services_exclude: "facebook"
                };
            }

            jQuery(document).ready(function(){
                jQuery("a.addthis_button").hover(function(){
                    var audio_title = jQuery(this).attr("addthis:title");
                    var page_url = jQuery("#hb-url-meta").attr("content");
                    var audio_url = page_url + "&title=" + audio_title;
                    jQuery("#hb-title-meta").attr("content", audio_title);
                    jQuery("#hb-url-meta").attr("content", audio_url);
                });
            })

        </script>';
    }


    function hbag_setting_menu()
    {
        add_options_page(
            'HB Audio Gallery Setting',
            'HB Audio Gallery',
            'manage_options',
            'hb-ag-setting',
            'hbag_setting_config_page'
        );
    }



    function process_ag_options()
    {
        if (!current_user_can('manage_options'))
            wp_die('Not allowed');

        check_admin_referer('hb_ag_setting');

        if (!wp_verify_nonce($_POST['_wpnonce'], 'hb_ag_setting')) {
            wp_die('Not allowed');
        }

        $options = get_option(HBAG_OPTIONS);

        foreach (array('hbag_audio_download_enable') as $option_name) {
            if (isset($_POST[$option_name])) {
                $options[$option_name] = true;
            } else {
                $options[$option_name] = false;
            }
        }

        foreach (array('hbag_audio_facebook_sharing') as $option_name) {
            if (isset($_POST[$option_name])) {
                $options[$option_name] = true;
            } else {
                $options[$option_name] = false;
            }
        }

        foreach (array('hbag_audio_addthis_sharing') as $option_name) {
            if (isset($_POST[$option_name])) {
                $options[$option_name] = true;
            } else {
                $options[$option_name] = false;
            }
        }

        foreach (array('addthis_publish_id') as $option_name) {
            if (isset($_POST[$option_name])) {
                $options[$option_name] = sanitize_text_field($_POST[$option_name]);
            }
        }

        foreach (array('hbag_audio_favourites') as $option_name) {
            if (isset($_POST[$option_name])) {
                $options[$option_name] = true;
            } else {
                $options[$option_name] = false;
            }
        }

        update_option(HBAG_OPTIONS, $options);
        
        wp_redirect(add_query_arg(array('page' => 'hb-ag-setting', 'message' => '1'), admin_url('options-general.php')));
        exit;
    }
    
    function process_theme_color_option()
    {
        
        update_option('theme_color', $_POST['theme_color']);
        update_option('theme_secondary_color', $_POST['theme_secondary_color']);
        
        wp_redirect(add_query_arg(array('page' => 'hb-ag-setting', 'message' => '1'), admin_url('options-general.php')));
        exit;
    }


    function delete_gallery($postid)
    {
        global $post_type;

        if ($post_type != 'hbag_audio_gallery') return;

        $gallery_dir = HBAG_UPLOAD_DIR . '/' . get_the_title($postid);
        hbag_remove_dir($gallery_dir);
    }

    function themes_taxonomy()
    {
        register_taxonomy('hb_ag_category', 'hbag_audio_gallery', array(
            // Hierarchical taxonomy (like categories)
            'hierarchical' => true,
            // This array of options controls the labels displayed in the WordPress Admin UI
            'labels' => array(
                'name' => _x('Category', 'taxonomy general name'),
                'singular_name' => _x('Category', 'taxonomy singular name'),
                'search_items' =>  __('Search Categories'),
                'all_items' => __('All Categories'),
                'parent_item' => __('Parent Category'),
                'parent_item_colon' => __('Parent Category:'),
                'edit_item' => __('Edit Category'),
                'update_item' => __('Update Category'),
                'add_new_item' => __('Add New Category'),
                'new_item_name' => __('New Category Name'),
                'menu_name' => __('Categories'),
            ),
            // Control the slugs used for this taxonomy
            'rewrite' => array(
                'slug' => 'hb-ag-category', // This controls the base slug that will display before each term
                'with_front' => false, // Don't display the category base before "/locations/"
                'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
            ),
        ));
    }



    function theme_columns($column)
    {


        $new_columns = array(
            'cb' => '<input type="checkbox" />',
            'name'   => __('Name'),
            'slug'   => __('Slug'),
            'posts'  => __('Posts'),
            //'description' => __('Description'),
            'tax_id' => 'Category ID'
        );

        return $new_columns;
    }

    function manage_theme_columns($value, $name, $id)
    {

        return 'tax_id' === $name ? $id : $value;
    }

    function hbag_recent_audio_by_country_dasboard_widget()
    {
        wp_add_dashboard_widget('hbag_show_recent_audio_names_by_country_widget', 'Today\'s Trending Songs!', array(&$this, 'hbag_show_recent_audio_names_by_country'));
    }

    function hbag_show_recent_audio_names_by_country()
    {
        ob_start(); ?>

        <head>
            <style>
                table {
                    font-family: arial, sans-serif;
                    border-collapse: collapse;
                    width: 100%;
                }

                td,
                th {
                    border: 1px solid #dddddd;
                    text-align: left;
                    padding: 8px;
                }

                tr:nth-child(even) {
                    background-color: #dddddd;
                }
            </style>
        </head>

        <body>
            <table>
                <thead>
                    <tr>
                        <th>Songs</th>
                        <th>Play Count</th>
                    </tr>
                </thead>
                <tbody id='audioCountryResponse'></tbody>
            </table>
            <div class='hbag-dashboard-loader' id='audioCountryResponseLoader' style='margin-top:10px;'></div>
        </body>

    <?php
        echo ob_get_clean();
    }

    function hbag_recent_audio_count_by_country_dasboard_widget()
    {
        wp_add_dashboard_widget('hbag_show_total_audio_count_by_country_widget', 'Audience overview', array(&$this, 'hbag_show_total_audio_count_by_country'));
    }

    function hbag_show_total_audio_count_by_country()
    {
        ob_start(); ?>

        <div id='hbag-map' class='welcome-panel'>
            <div class='hbag-dashboard-loader' id='countryMapLoader'></div>
        </div>

<?php
    echo ob_get_clean();
    }
}
