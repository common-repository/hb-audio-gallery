<?php

/*

Plugin Name: HB AUDIO GALLERY

Plugin URI: https://hb-audio-gallery.hbwebsol.com

Description: A HTML5 based, simple and responsive audio player plugin which supports custom post type, shortcodes and works on all Browsers & devices for WordPress by Team HB WEBSOL.

Version: 3.0

Author: Team HB WEBSOL

Author URI: https://www.hbwebsol.com

License: GPLv2

*/




use GeoIp2\Database\Reader;
class hbag_wp_Loader
{


    var $gallery;

    var $update;



    function __construct()
    {
        $this->hbag_load_defines();

        $this->hbag_load_files();

        $this->hbag_create_gallery_directory();



        $plugin_name = basename(dirname(__FILE__)) . '/' . basename(__FILE__);


        register_activation_hook($plugin_name, array(&$this, 'hbag_plugin_active'));

        register_uninstall_hook($plugin_name, array(__CLASS__, 'hbag_plugin_uninstall'));

        add_action('admin_init', array(&$this, 'update_tables_and_settings_when_plugin_updates'));

        add_action('init', array(&$this, 'hbag_init'), 11);

        add_action('admin_init', array(&$this, 'hbag_admin_init'));
        
        add_option('count', 0);
    }




    function  hbag_init()
    {

        wp_enqueue_script('jquery-lib');


        wp_register_style('hb-jplayer-style', HBAG_URLPATH . 'lib/jPlayer/skin/blue.monday/jplayer.blue.monday.css', array(), null);

        wp_enqueue_style('hb-jplayer-style');



        wp_register_script('hb-jplayer', HBAG_URLPATH . 'lib/jPlayer/js/jquery.jplayer.js', array('jquery'), null);

        wp_enqueue_script('hb-jplayer');



        wp_register_script('hb-jplayer-playlist', HBAG_URLPATH . 'lib/jPlayer/js/jplayer.playlist.js', array('jquery'), null);

        wp_enqueue_script('hb-jplayer-playlist');


        wp_register_style('hb-style', HBAG_URLPATH . 'css/hb-style.css', array(), null);

        wp_enqueue_style('hb-style');
        
        // Shortcodes css files //
        wp_register_style('hb-slider-style', HBAG_URLPATH . 'gallery/css/slider_view.css', array(), null);

        wp_enqueue_style('hb-slider-style');

        wp_register_style('hb-list-style', HBAG_URLPATH . 'gallery/css/list_view.css', array(), null);

        wp_enqueue_style('hb-list-style');

        wp_register_style('hb-grid-style', HBAG_URLPATH . 'gallery/css/grid_view.css', array(), null);

        wp_enqueue_style('hb-grid-style');

        wp_register_style('hb-category-style', HBAG_URLPATH . 'gallery/css/category_view.css', array(), null);

        wp_enqueue_style('hb-category-style');

        wp_register_style('hb-single-style', HBAG_URLPATH . 'gallery/css/single_view.css', array(), null);

        wp_enqueue_style('hb-single-style');

        // audio play count record
        wp_enqueue_script('hb-audio-audio-record', HBAG_URLPATH . 'gallery/js/hbaudioRecord.js', array('jquery'), null, true);
        wp_enqueue_script('hb-audio-mutation-observer', HBAG_URLPATH . 'lib/class/mutation_observer.js', array('jquery'), null);

        
        // image slider view
        wp_register_script('hb-slick', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', array('jquery'), null, true );
    
        wp_enqueue_script('hb-slick');

        wp_register_style('hb-slick-css', "https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css", array(), null);

        wp_enqueue_style('hb-slick-css');

        wp_register_style('hb-slick-theme-css', "https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css", array(), null);

        wp_enqueue_style('hb-slick-theme-css');

        //helper.js
        wp_enqueue_script('hb-audio-helper-js', HBAG_URLPATH . 'gallery/js/helper.js', array('jquery'), null, true);

        //favourites songs ajax
        wp_enqueue_script('hb-audio-favourites', HBAG_URLPATH . 'gallery/js/hbfavourites.js', array('jquery'), null, true);

        wp_localize_script(
            'hb-audio-favourites',
            'hb_object',

            array(
                'ajaxUrl' => admin_url(
                    'admin-ajax.php'
                ),
                'loginUrl' => get_site_url() . '/login',
                'pluginUrl' => HBAG_URLPATH,
                'userId' => get_current_user_id()
            )
        );

        // add/remove favourites
        add_action('wp_ajax_hb_favourites', array(&$this, 'hbag_favourites_callback'));
        add_action('wp_ajax_nopriv_hb_favourites', array(&$this, 'hbag_favourites_callback'));

        // add play record to database 
        add_action('wp_ajax_hb_track_record', array(&$this, 'hbag_track_record_callback'));
        add_action('wp_ajax_nopriv_hb_track_record', array(&$this, 'hbag_track_record_callback'));


        // download audio
        add_action('wp_ajax_download_audio',  array(&$this, 'hbag_downloadaudiofile'));
        add_action('wp_ajax_nopriv_download_audio', array(&$this, 'hbag_downloadaudiofile'));


        // recent audio across the hbag_get_recent_audios_across_world_callback
        wp_enqueue_script('hb-recent-audio', HBAG_URLPATH . 'gallery/js/hb_recent_audio.js', array('jquery'), null, true);

        // general script
        wp_enqueue_script('hb-gallery-general', HBAG_URLPATH . 'gallery/js/hb_gallery.js', array('jquery'), null, true);
        
        add_action('wp_ajax_get_recent_audios',  array(&$this, 'hbag_get_recent_audios_across_world_callback'));
        add_action('wp_ajax_nopriv_get_recent_audios', array(&$this, 'hbag_get_recent_audios_across_world_callback'));
        
        add_action('wp_ajax_get_total_audios_count',  array(&$this, 'hbag_get_audios_total_count_by_country_callback'));
        add_action('wp_ajax_nopriv_get_total_audios_count', array(&$this, 'hbag_get_audios_total_count_by_country_callback'));

        // wp_enqueue_script('plupload', HBAG_URLPATH . 'lib/plupload.full.min.js', '' , '' , true);
    }

    function  hbag_admin_init()
    {

        // on update/upgrade plugin completed. set transient and let `redirectToUpdatePlugin()` work.
        add_action('upgrader_process_complete', array(&$this, 'updateProcessComplete'), 10, 2);
        // on plugins loaded, background update the plugin with new version.
        add_action('plugins_loaded', array(&$this, 'redirectToUpdatePlugin'));


        add_action('admin_head',  array(&$this, 'hbag_plupload_admin_head'));

        add_action('wp_ajax_plupload_action',  array(&$this, 'hbag_g_plupload_action'));



        wp_enqueue_script('plupload-all');



        wp_register_script('hbplupload', HBAG_URLPATH . 'gallery/js/hbplupload.js', array('jquery'), null);

        wp_enqueue_script('hbplupload');



        wp_register_style('hbplupload', HBAG_URLPATH . 'gallery/css/hbplupload.css');

        wp_enqueue_style('hbplupload');



        // scan audio ajax
        wp_enqueue_script('hb-scan-audiofile', HBAG_URLPATH . 'gallery/js/hbscanfile.js', array('jquery'), null);

        wp_localize_script(
            'hb-scan-audiofile',
            'ajax_object',

            array('ajax_url' => admin_url('admin-ajax.php'))
        );

        add_action('wp_ajax_hb-scanaudio', array(&$this, 'hbag_scanaudio_callback'));



        // upload audio ajax
        wp_enqueue_script('hb-upload-audiofile', HBAG_URLPATH . 'gallery/js/hbaddgallery.js', array('jquery'), null);

        wp_localize_script(
            'hb-upload-audiofile',
            'ajax_object',

            array('ajax_url' => admin_url('admin-ajax.php'))
        );

        add_action('wp_ajax_hb-uploadaudio', array(&$this, 'hbag_uploadaudio_callback'));



        // htaccess ajax
        wp_enqueue_script('hb-htaccess', HBAG_URLPATH . 'gallery/js/hbhtaccess.js', array('jquery'), null);

        wp_localize_script(
            'hb-htaccess',
            'ajax_object',

            array('ajax_url' => admin_url('admin-ajax.php'))
        );

        add_action('wp_ajax_hb-htaccess', array(&$this, 'hbag_htaccess_callback'));

        
        add_action('post_updated', array(&$this, 'hbag_update_gallery_folder_name'), 10, 3);

        // image upload ajax
        wp_enqueue_script('hb-upload-picture', HBAG_URLPATH . 'gallery/js/hbpluploadImage.js', array('jquery'), null, true);


        // Leaflet
        wp_register_style('hb-leaflet', HBAG_URLPATH . 'lib/leaflet/css/leaflet.css');

        wp_enqueue_style('hb-leaflet');

        wp_register_script('hb-leaflet', HBAG_URLPATH . 'lib/leaflet/js/leaflet.js', array('jquery'), null, true);

        wp_enqueue_script('hb-leaflet');
        
        wp_register_style('hb-leaflet-style', HBAG_URLPATH . 'gallery/css/hb_leaflet_style.css');

        wp_enqueue_style('hb-leaflet-style');

        wp_register_script('hb-leaflet-map', HBAG_URLPATH . 'gallery/js/hb_leaflet_map.js', array('jquery'), null, true);

        wp_enqueue_script('hb-leaflet-map');

        wp_register_script('hb-country-geojson', HBAG_URLPATH . 'lib/countriesGeoJson.js', array(), null);

        wp_enqueue_script('hb-country-geojson');

         // color picker
        wp_enqueue_style( 'wp-color-picker' );

        wp_enqueue_script( 'my-script-handle', HBAG_URLPATH . 'gallery/js/hb_colorpicker.js', array( 'wp-color-picker' ), false, true );
    }

    function hbag_load_defines()
    {
        define('HBAG_WINABSPATH', str_replace("\\", "/", ABSPATH));

        define('HBAG_AG_FOLDER', basename(dirname(__FILE__)));

        define('HBAG_ABSPATH', trailingslashit(str_replace("\\", "/", WP_PLUGIN_DIR . '/' . HBAG_AG_FOLDER)));

        define('HBAG_URLPATH', trailingslashit(plugins_url(HBAG_AG_FOLDER)));

        define('HBAG_PLUGIN_DIR', dirname(__FILE__));



        $upload_dir = wp_upload_dir();



        $upload_path = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'hb-audio-gallery';

        $upload_path = str_replace('/', DIRECTORY_SEPARATOR, $upload_path);

        $upload_path = str_replace('\\', DIRECTORY_SEPARATOR, $upload_path);

        define('HBAG_UPLOAD_DIR', $upload_path);



        /*Image upload path*/
        $image_path = HBAG_ABSPATH . 'gallery/images';

        $image_path = str_replace('/', DIRECTORY_SEPARATOR, $image_path);

        $image_path = str_replace('\\', DIRECTORY_SEPARATOR, $image_path);

        define('HBAG_UPLOAD_IMAGE_DIR', $image_path);
        /*-------------*/


        define('HBAG_OPTIONS', 'hb_ag_options');



        global $wpdb;
    }





    function  hbag_load_files()
    {

        require_once(dirname(__FILE__) . '/gallery/gallery.php');

        require_once(dirname(__FILE__) . '/gallery/gallery-content.php');

        $this->gallery = new hbag_wp_gallery();



        require_once(dirname(__FILE__) . '/lib/audioDB.php');

        require_once(dirname(__FILE__) . '/lib/util-functions.php');
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        
        require_once(dirname(__FILE__) . '/lib/upload_picture.php');
        
        //maxmind-db
        require_once(dirname(__FILE__) . '/vendor/autoload.php');

    }



    function hbag_create_gallery_directory()
    {

        require_once(ABSPATH . "wp-admin/includes/class-wp-filesystem-base.php");

        require_once(ABSPATH . "wp-admin/includes/class-wp-filesystem-direct.php");

        $wp_fs_d = new WP_Filesystem_Direct(new StdClass());



        if (!$wp_fs_d->is_dir(HBAG_UPLOAD_DIR) && !$wp_fs_d->mkdir(HBAG_UPLOAD_DIR, 0777))

            wp_die(sprintf(__("Impossible to create %s directory."), HBAG_UPLOAD_DIR));



        $uploads = wp_upload_dir();

        if (!$wp_fs_d->is_dir($uploads['path']) && !$wp_fs_d->mkdir($uploads['path'], 0777))

            wp_die(sprintf(__("Impossible to create %s directory."), $uploads['path']));


            /*Image upload path*/
        $image_path = str_replace('\\', '/', HBAG_UPLOAD_DIR.'/images/');

        $wp_fs_d = new WP_Filesystem_Direct(new StdClass());
        if (!$wp_fs_d->is_dir($image_path) && !$wp_fs_d->mkdir($image_path, 0777)){
           wp_die(sprintf(__("Impossible to create %s directory."), $image_path));
        }

        define('HBAG_IMAGE_UPLOAD_DIR', $image_path);

        //        if (!is_dir($uploads['path'])) {

        //            umask(0);

        //            mkdir($uploads['path'], 0777);

        //        }



    }

    function hbag_plupload_admin_head()
    {



        // place js config array for plupload

        $plupload_init = array(

            'runtimes' => 'html5,silverlight,flash,html4',

            'browse_button' => 'plupload-browse-button', // will be adjusted per uploader

            'container' => 'plupload-upload-ui', // will be adjusted per uploader

            // 'drop_element' => 'drag-drop-area', // will be adjusted per uploader

            'file_data_name' => 'async-upload', // will be adjusted per uploader

            'multiple_queues' => true,

            'max_file_size' => 64000000 . 'b',

            'url' => admin_url('admin-ajax.php'),

            'flash_swf_url' => includes_url('js/plupload/plupload.flash.swf'),

            'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),

            'filters' => array(array('title' => __('Audio Files'), 'extensions' => 'mp3')),

            'multipart' => true,

            'urlstream_upload' => true,

            'multi_selection' => false, // will be added per uploader

            // additional post data to send to our ajax hook

            'multipart_params' => array(

                '_ajax_nonce' => "", // will be added per uploader

                'action' => 'plupload_action', // the ajax action name

                'audioid' => 0 // will be added per uploader

            )

        );

?>

        <script type="text/javascript">
            var base_plupload_config = <?php echo json_encode($plupload_init); ?>;
        </script>

<?php

    }


    function hbag_g_plupload_action()
    {



        // check ajax noonce

        $audioid = sanitize_text_field($_POST['audioid']);



        check_ajax_referer($audioid . 'pluploadan');



        // handle file upload

        $status = wp_handle_upload($_FILES[$audioid . 'async-upload'], array('test_form' => true, 'action' => 'plupload_action'));



        // send the uploaded file url in response

        echo $status['url'];

        exit;
    }







    function hbag_scanaudio_callback()
    {
        $upload_dir = sanitize_text_field($_REQUEST['upload_dirctory']) . '/';

        $gallery_id = sanitize_text_field($_REQUEST['gallery_id']);

        $audio_columns = sanitize_text_field($_REQUEST['audio_columns']);

        $hidden_columns = sanitize_text_field($_REQUEST['hidden_columns']);



        $audio_columns = explode(",", $audio_columns);

        if ($hidden_columns != "no")

            $hidden_columns = explode(",", $hidden_columns);

        else

            $hidden_columns = array();



        global $wpdb;



        $gallerypost = get_post($gallery_id);

        $galleryslug = $gallerypost->post_name;


        $audioList = array();

        $audioList = hbag_db_get_AudioGallery($gallery_id);



        $audio_filename_List = array();

        $count_del = 0;

        foreach ($audioList as $audio) {

            $filepath = hbag_convert_urltopath($audio['audioURL']);

            if (is_file($filepath) == false) {

                hbag_db_delete_audio($audio['aid']);

                $count_del++;

                continue;
            }

            array_push($audio_filename_List, $audio['filename']);
        }



        $count = 0;

        if (is_dir($upload_dir)) {


            if ($dh = opendir($upload_dir)) {

                while (($file = readdir($dh)) !== false) {

                    if ($file == "." || $file == "..")

                        continue;



                    if (!in_array($file, $audio_filename_List)) {

                        $file_path = $upload_dir . $file;

                        $file_url = hbag_convert_pathtourl($file_path);

                        $filepart = pathinfo($file_path);



                        $orders = 0;
                        hbag_db_insert_audio($gallery_id, $filepart['filename'], $filepart['basename'], $file_url, $orders);

                        $count++;
                    }
                }

                closedir($dh);
            }
        }



        $audioList = hbag_db_get_AudioGallery($gallery_id);
        $return_arr['content'] = wp_get_gallery_list_content($audioList, $audio_columns, $hidden_columns);

        $return_arr['message'] = '<p style="margin:5px 0;">Scan Finished!</p>';



        if ($count != 0) {

            $return_arr['message'] .= '<p style="margin:5px 0;">' . $count . ' files are added.</p>';
        } else {

            $return_arr['message'] .= '<p style="margin:5px 0;">No files to be added.</p>';
        }

        if ($count_del != 0) {

            $return_arr['message'] .= '<p style="margin:5px 0;">' . $count_del . ' files are not exist. These Files are removed in gallery.</p>';
        }



        echo json_encode($return_arr);

        die();
    }





    function hbag_uploadaudio_callback()
    {
        if ($_REQUEST['upload_dirctory'] && $_REQUEST['audio_upload'] && $_REQUEST['gallery_id'] && $_REQUEST['audio_columns'] && $_REQUEST['hidden_columns']) {



            $upload_dir = sanitize_text_field($_REQUEST['upload_dirctory']);

            $audioS = sanitize_text_field($_REQUEST['audio_upload']);

            $audio_arr = explode(',', $audioS);





            $gallery_id = sanitize_text_field($_REQUEST['gallery_id']);

            $audio_columns = sanitize_text_field($_REQUEST['audio_columns']);

            $hidden_columns = sanitize_text_field($_REQUEST['hidden_columns']);



            $audio_columns = explode(",", $audio_columns);

            if ($hidden_columns != "no")

                $hidden_columns = explode(",", $hidden_columns);

            else

                $hidden_columns = array();



            $audioList = array();



            $count_success = 0;

            $count_fail = 0;

            $count_exist = 0;

            foreach ($audio_arr as $audio) {

                $filepart = pathinfo($audio);



                $newfile = $upload_dir . '/' . $filepart['basename'];

                $newfile_url = hbag_convert_pathtourl($newfile);

                $oldfile = hbag_convert_urltopath($audio);





                if (file_exists($newfile)) {

                    $count_exist++;

                    unlink($oldfile);

                    continue;
                }



                hbag_copyfile($oldfile, $newfile);

                if (file_exists($newfile)) {

                    $orders = '';

                    hbag_db_insert_audio($gallery_id, $filepart['filename'], $filepart['basename'], $newfile_url, $orders);



                    $count_success++;
                } else {

                    $count_fail++;
                }



                unlink($oldfile);
            }



            $audioList = hbag_db_get_AudioGallery($gallery_id);



            $return_arr['content'] = wp_get_gallery_list_content($audioList, $audio_columns, $hidden_columns);

            $return_arr['message'] = '<p style="margin:5px 0;">Upload Finished!</p>';

            if ($count_success != 0)

                $return_arr['message'] .= '<p style="margin:5px 0;">' . $count_success . ' files are uploaded.</p>';

            if ($count_fail != 0)

                $return_arr['message'] .= '<p style="margin:5px 0;">' . $count_fail . ' files are failed.</p>';

            if ($count_exist != 0)

                $return_arr['message'] .= '<p style="margin:5px 0;">' . $count_exist . ' files alread exist.</p>';



            echo json_encode($return_arr);

            die();
        }
    }





    function hbag_htaccess_callback()
    {

        if ($_REQUEST['ht_content']) {

            $ht_content = sanitize_textarea_field($_REQUEST['ht_content']);

            if (hbag_WriteNewHtaccess($ht_content)) {

                $return_arr['message'] = '<p style="margin:5px 0;">Save Successful!</p>';
            } else {

                $return_arr['message'] = '<p style="margin:5px 0;">The file could not be saved!</p>';
            }

            echo json_encode($return_arr);

            die();
        }
    }



    function hbag_plugin_active()
    {
        hbag_db_create_table();
        hbag_add_order();

        if (get_option(HBAG_OPTIONS) === false) {

            $new_options['hbag_audio_download_enable'] = false;
            
            $new_options['hbag_audio_download_enable21'] = false;

            $new_options['hbag_audio_facebook_sharing'] = false;

            $new_options['hbag_audio_addthis_sharing'] = false;

            $new_options['addthis_publish_id'] = "";

            $new_options['hbag_audio_favourites'] = false;

            add_option(HBAG_OPTIONS, $new_options);
        }
    }



    function hbag_plugin_uninstall()
    {



        if (get_option('hb_ag_options') != false) {

            delete_option('hb_ag_options');
        }



        hbag_db_delete_table();



        hbag_db_delete_audiogallery_post();



        hbag_remove_dir(HBAG_UPLOAD_DIR);

        hbag_delete_custom_terms('hb_ag_category');
    }


    function update_tables_and_settings_when_plugin_updates() {
        $options = get_option(HBAG_OPTIONS);

        if(!isset($options['hbag_audio_favourites']))
        {
            $options['hbag_audio_favourites'] = false;

            update_option(HBAG_OPTIONS, $options);
        }

       hbag_db_create_table();
    }

    function hbag_downloadaudiofile()
    {

        $audioID = sanitize_text_field($_REQUEST['audio']);

        $gid = sanitize_text_field($_REQUEST['gallery']);

        if (!$audioID || !$gid) die();

        $audioRow = hbag_db_get_audio($audioID);

        $filename = basename($audioRow['filename']);

        $gallery_name = get_the_title($gid);

        $base_dir = HBAG_UPLOAD_DIR . DIRECTORY_SEPARATOR . $gallery_name;

        $file = $base_dir . DIRECTORY_SEPARATOR . $filename;

        $file_size = hbag_get_filesize($audioRow['audioURL']);

        if (!$file) {

            die('invalid_audio');
        } else {

            $this->hbag_download_audio($file, $file_size);
        }
    }



    function hbag_download_audio($file, $file_size)
    {

        if ($file_size && $file) {

            $filename = basename($file);



            if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE 6.") or strstr($_SERVER["HTTP_USER_AGENT"], "MSIE 5.5")) {

                Header("Content-type: application/x-msdownload");

                header("Content-type: application/octet-stream");

                header("Cache-Control: private, must-revalidate");

                header("Pragma: no-cache");

                header("Expires: 0");
            } else {

                header("Cache-control: private");

                header("Content-type: file/unknown");

                header('Content-Length: ' . $file_size);

                Header("Content-type: file/unknown");

                Header("Content-Disposition: attachment; filename=\"" . $filename . "\"");

                Header("Content-Description: PHP5 Generated Data");

                header("Pragma: no-cache");

                header("Expires: 0");
            }



            if (is_file("$file")) {

                $fp = fopen("$file", "r");



                if (!fpassthru($fp)) {

                    fclose($fp);
                }
            } else {

                die('invalid_audio');
            }
        } else {

            die('invalid_audio');
        }
    }



    public function hbag_update_gallery_folder_name($post_ID, $post_after, $post_before)

    {

        if ($post_before->post_type == 'hbag_audio_gallery') {

            if ($post_before->post_title != $post_after->post_title) {

                rename(
                    HBAG_UPLOAD_DIR . DIRECTORY_SEPARATOR . $post_before->post_title,

                    HBAG_UPLOAD_DIR . DIRECTORY_SEPARATOR . $post_after->post_title
                );

                hbag_db_updateGallery_title($post_ID, $post_before->post_title, $post_after->post_title);
            }
        }
    }

    public function hbag_favourites_callback()
    {
        $user_id = get_current_user_id();

        // non-registered user
        if ($user_id == 0) {
            echo 'You are not allowed to access';
            wp_die();
        }

        if ($_GET) {
            echo json_encode(get_favourite_audio_id_on_page_load($user_id));
            wp_die();
        }

        $audio_id = $_POST['audio_id'];

        echo favourite_song_update($audio_id, $user_id);

        wp_die();
    }

    public function hbag_track_record_callback()
    {
        $user_id = get_current_user_id();

        if(!is_user_logged_in()){
            $user_id = 0;
        }
        
        $audio_id = $_POST['audio_id'];
        $client_ip = get_client_ip();
        
        $audio_title = get_audio_details($audio_id);

        $response = add_track_record_audio_gallery($audio_id, $user_id, $client_ip, $audio_title);

        // true
        echo ($response);

        wp_die();
    }

    public function hbag_get_recent_audios_across_world_callback()
    {
        $response = get_recently_played_audios_list();
        
        try{
            $reader = new Reader(HBAG_ABSPATH . 'lib/GeoLite2-Country.mmdb');
            $audios = [];
            $i = 0;
            foreach ($response as $audio){
                $audios[$i]->title = $audio->title;

                if($audio->ip_address == '127.0.0.1' || $audio->ip_address == '::1'){
                    $audios[$i]->country = 'Other';
                }else{
                    $record = $reader->country($audio->ip_address);
                    $audios[$i]->country = $record->country->name;
                }
                $i++;
            }
            echo(json_encode($response));
        } catch(Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }

        wp_die();
    }
    
    public function hbag_get_audios_total_count_by_country_callback()
    {
        $response = get_total_played_audios_ip_list();
        try {
            $reader = new Reader(HBAG_ABSPATH . 'lib/GeoLite2-Country.mmdb');
            $countries = [];
            $i = 0;
            foreach ($response as $audio){
                if($audio->ip_address == '127.0.0.1' || $audio->ip_address == '::1'){
                    $countries[$i] = 'Other';
                }else{
                    $record = $reader->country($audio->ip_address);
                    $countries[$i] = $record->country->name;
                }
                $i++;
            }
            
            echo(json_encode(array_count_values($countries)));
        } catch (Exception $e) {
            echo 'Error: '.$e->getMessage();
        }
        wp_die();
    }
}

global $hbag_wp_Loader;

$hbag_wp_Loader = new hbag_wp_Loader();
