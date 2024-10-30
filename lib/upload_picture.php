<?php
if (!isset($wpdb)) {
    include_once('../../../../wp-config.php');
    include_once('../../../../wp-includes/wp-db.php');
}
global $wpdb;
if (!defined('AUDIO_DB')) {
    define('AUDIO_DB', $wpdb->prefix . 'hbag_audios');
}

/* load image url */
// define('PICTURE_DIR_URL', site_url() . '/wp-content/plugins/hb-audio-gallery/gallery/images/');
define('PICTURE_DIR_URL', site_url() . '/wp-content/uploads/hb-audio-gallery/images/');

// define('AUDIO_DB', $wpdb->prefix . 'hbag_audios');
if (!empty($_FILES['picture']['name'])) {

define('PLUGIN_PATH', dirname(__DIR__, 1) . '/');

//File upload configuration
    $fileName =  time() . '_' . basename($_FILES['picture']['name']);
    $success = false;
    $uploadDir = HBAG_IMAGE_UPLOAD_DIR;
    $targetPath = $uploadDir . $fileName;
    $aid = $_POST['audio_id'];
    $targetPathUrl = PICTURE_DIR_URL . $fileName;

    //Upload file to server
    if (move_uploaded_file($_FILES['picture']['tmp_name'], $targetPath)) {

        $sql = $wpdb->prepare("UPDATE %s SET picture=%s WHERE aid=%d", AUDIO_DB, $targetPathUrl, $aid);
        $success = $wpdb->query(hbag_db_getCorrectSql($sql)) ? true : false;
    }else{
        echo "not uploaded";
    }

    echo '<script type="text/javascript">window.top.window.completeUpload(' . $success . ',\'' . $targetPathUrl . '\',' . $aid . ');</script>  ';
}
