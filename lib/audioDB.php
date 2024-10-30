<?php
global $wpdb;
if(!defined('DB_NAME')){
    define('DB_NAME', $wpdb->dbname);
}
define('AUDIO_DB', $wpdb->prefix . 'hbag_audios');
define('FAVOURITE_AUDIO_DB', $wpdb->prefix . 'hbag_audio_favourites');
define('TRACK_RECORD_AUDIO_DB', $wpdb->prefix . 'hbag_audio_track_record');

function hbag_db_create_table()
{
    global $wpdb;
    $sql =
        "CREATE TABLE IF NOT EXISTS " . AUDIO_DB . " (
            aid BIGINT(20) NOT NULL AUTO_INCREMENT ,
            gid BIGINT(20) DEFAULT '0' NOT NULL ,
            filename VARCHAR(255) NOT NULL ,
            picture VARCHAR(255) DEFAULT NULL,
            audioURL VARCHAR(255) NOT NULL ,
            title VARCHAR(255) NOT NULL ,
            artist VARCHAR(255) DEFAULT '-' ,
			orders BIGINT(20) NOT NULL ,					
            PRIMARY KEY  (aid)
            ) ;";

    $favourite =
        "CREATE TABLE IF NOT EXISTS " . FAVOURITE_AUDIO_DB . " (
                    id BIGINT(20) NOT NULL AUTO_INCREMENT ,
                    audio_id BIGINT(20) NOT NULL ,
                    user_id BIGINT(20) NOT NULL ,
                    PRIMARY KEY  (id)
                    ) ;";
    
    $record =
        "CREATE TABLE IF NOT EXISTS " . TRACK_RECORD_AUDIO_DB . " (
                    id BIGINT(20) NOT NULL AUTO_INCREMENT ,
                    audio_id BIGINT(20) NOT NULL ,
                    audio_title VARCHAR(255) NOT NULL ,
                    play_count BIGINT(20) NOT NULL ,
                    user_id BIGINT(20) NOT NULL ,
                    ip_address VARCHAR(255) NOT NULL ,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
                    PRIMARY KEY (id)
                    ) ;";


    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	dbDelta( $favourite );
	dbDelta( $record );
    
    $column = $wpdb->get_results( $wpdb->prepare(
		"SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",
		DB_NAME, AUDIO_DB, 'picture'
	) );
            
    if(!$column){
        $alter_table = 
            "ALTER TABLE " . AUDIO_DB . " ADD COLUMN picture VARCHAR(255) DEFAULT NULL;";
        $wpdb->query($alter_table);
    }
    
    $column = $wpdb->get_results( $wpdb->prepare(
		"SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",
		DB_NAME, AUDIO_DB, 'artist'
	) );
            
    if(!$column){
        $alter_table = 
            "ALTER TABLE " . AUDIO_DB . " ADD COLUMN artist VARCHAR(255) DEFAULT NULL;";
        $wpdb->query($alter_table);
    }

    $column = $wpdb->get_results( $wpdb->prepare(
		"SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s ",
		DB_NAME, TRACK_RECORD_AUDIO_DB, 'audio_title'
	) );
            
    if(!$column){
        $alter_table = 
            "ALTER TABLE " . TRACK_RECORD_AUDIO_DB . " ADD COLUMN audio_title VARCHAR(255) NOT NULL;";
        $wpdb->query($alter_table);
    }
}

function hbag_db_delete_table()
{
    global $wpdb;
    $sql = "DROP TABLE IF EXISTS " . AUDIO_DB . ", " . FAVOURITE_AUDIO_DB . ", " . TRACK_RECORD_AUDIO_DB;
    $wpdb->query($sql);
}

function hbag_db_getCorrectSql($sql)
{
    $find_str = "'" . AUDIO_DB . "'";
    $sql = str_replace($find_str, AUDIO_DB, $sql);

    $find_str = "'" . FAVOURITE_AUDIO_DB . "'";
    $sql = str_replace($find_str, FAVOURITE_AUDIO_DB, $sql);

    $find_str = "'" . TRACK_RECORD_AUDIO_DB . "'";
    $retsql = str_replace($find_str, TRACK_RECORD_AUDIO_DB, $sql);

    return $retsql;
}

function hbag_db_get_AudioGallery($gallery_id)
{

    global $wpdb;

    $gallery = array();
    $sql = $wpdb->prepare("SELECT * FROM %s WHERE gid=%d ORDER BY orders, title ", AUDIO_DB, $gallery_id);  // For order and title sorting
    $gallery = $wpdb->get_results(hbag_db_getCorrectSql($sql), ARRAY_A);

    return $gallery;
}


function hbag_db_updateGallery_title($gallery_id, $old_title, $new_title)
{

    global $wpdb;

    $gallery = array();

    $sqlExpression = "UPDATE " . AUDIO_DB . "
SET audioURL = REPLACE(audioURL, '" . $old_title . "', '" .  $new_title . "')
WHERE gid = " . $gallery_id;

    $gallery = $wpdb->query(hbag_db_getCorrectSql($sqlExpression), ARRAY_A);

    return $gallery;
}



function hbag_db_insert_audio($gallery_id, $title, $filename, $audioURL, $orders)
{
    global $wpdb;
    $title = ucwords(str_replace('-', ' ', $title));
    $sql = $wpdb->prepare(
        "INSERT INTO %s (gid, filename, audioURL, title, orders) VALUES (%d, %s, %s, %s,%d)",
        AUDIO_DB,
        $gallery_id,
        $filename,
        $audioURL,
        $title,
        $orders
    );

    $wpdb->query(hbag_db_getCorrectSql($sql));

    return true;
}


function hbag_db_get_audio($audio_id)
{
    global $wpdb;
    $audio = array();
    $sql = $wpdb->prepare("SELECT * FROM %s WHERE aid=%d ORDER BY aid", AUDIO_DB, $audio_id);
    $audio = $wpdb->get_row(hbag_db_getCorrectSql($sql), ARRAY_A);
    return $audio;
}


function hbag_db_delete_audio($audio_id)
{
    global $wpdb;
    $sql = $wpdb->prepare("DELETE FROM %s WHERE aid=%d", AUDIO_DB, $audio_id);
    $wpdb->query(hbag_db_getCorrectSql($sql));
}


function hbag_db_update_audio($audio_id, $audio_title)
{
    global $wpdb;
    $sql = $wpdb->prepare("UPDATE %s SET title=%s WHERE aid=%d", AUDIO_DB, $audio_title, $audio_id);
    $wpdb->query(hbag_db_getCorrectSql($sql));
}

function hbag_db_update_artist($audio_id, $audio_artist)
{
    global $wpdb;
    $sql = $wpdb->prepare("UPDATE %s SET artist=%s WHERE aid=%d", AUDIO_DB, $audio_artist, $audio_id);
    $wpdb->query(hbag_db_getCorrectSql($sql));
}

function hbag_db_update_order($audio_id, $audio_order)
{
    global $wpdb;
    $sql = $wpdb->prepare("UPDATE %s SET orders=%d WHERE aid=%d", AUDIO_DB, $audio_order, $audio_id);
    $wpdb->query(hbag_db_getCorrectSql($sql));
}


function hbag_db_delete_audiogallery_post()
{
    $gallery_posts = get_posts(array('post_type' => 'hbag_audio_gallery', 'numberposts' => 300));
    foreach ($gallery_posts as $gpost) {
        wp_delete_post($gpost->ID, true);
    }

    function hbag_delete_custom_terms($taxonomy)
    {
        global $wpdb;

        $query = 'SELECT t.name, t.term_id
            FROM ' . $wpdb->terms . ' AS t
            INNER JOIN ' . $wpdb->term_taxonomy . ' AS tt
            ON t.term_id = tt.term_id
            WHERE tt.taxonomy = "' . $taxonomy . '"';

        $terms = $wpdb->get_results($query);

        foreach ($terms as $term) {
            wp_delete_term($term->term_id, $taxonomy);
        }
    }

    // Delete all custom terms for this taxonomy

}
function hbag_add_order()
{
    global $wpdb;
    $col_name = 'orders';
    //$col = mysql_query("SELECT ".$col_name." FROM ".AUDIO_DB);
    $sql = "SELECT " . $col_name . " FROM " . AUDIO_DB;
    $col = $wpdb->query($sql);
    if (!is_int($col)) {
        $sql = "ALTER TABLE " . AUDIO_DB . " ADD " . $col_name . " BIGINT NOT NULL";
        $out = $wpdb->query($sql);
    }
}

function favourite_song_update($audio_id, $user_id)
{
    global $wpdb;

    $isFavourite = $wpdb->prepare("SELECT id FROM %s WHERE audio_id = %d AND user_id = %d ", FAVOURITE_AUDIO_DB, $audio_id, $user_id);
    $isFavourite = $wpdb->query(hbag_db_getCorrectSql($isFavourite));

    if ($isFavourite) {
        $sql = $wpdb->prepare("DELETE FROM %s WHERE audio_id = %d AND user_id = %d ", FAVOURITE_AUDIO_DB, $audio_id, $user_id);
        $wpdb->query(hbag_db_getCorrectSql($sql));
        return false;
    } else {
        $sql = $wpdb->prepare("INSERT INTO %s (audio_id, user_id) VALUES (%d, %d)", FAVOURITE_AUDIO_DB, $audio_id, $user_id);
        $wpdb->query(hbag_db_getCorrectSql($sql));
        return true;
    }
}

function get_favourite_audio_id_on_page_load($user_id)
{
    global $wpdb;

    $sql = $wpdb->prepare("SELECT audio_id FROM %s WHERE user_id = %d ", FAVOURITE_AUDIO_DB, $user_id);
    $sql = $wpdb->get_results(hbag_db_getCorrectSql($sql), OBJECT);

    $audio_ids = [];
    foreach ($sql as $key => $value) {
        array_push($audio_ids, $value->audio_id);
    };
    return $audio_ids;
}

function get_favourite_audio_gallery($user_id)
{
    global $wpdb;

    $gallery = array();
    $sql = $wpdb->prepare("SELECT * FROM %s a join %s f on a.aid = f.audio_id WHERE f.user_id = %d ORDER BY orders, title ", AUDIO_DB, FAVOURITE_AUDIO_DB, $user_id);  // For order and title sorting
    $gallery = $wpdb->get_results(hbag_db_getCorrectSql($sql), ARRAY_A);

    return $gallery;
}

function add_track_record_audio_gallery($audio_id, $user_id, $client_ip, $audio_title)
{
    global $wpdb;
/*
    // fetching previous play count
    if($user_id == 0){
        // if user is not logged in, then check will be based on ip_address
        $sql = $wpdb->prepare("SELECT play_count FROM %s WHERE audio_id = %d AND user_id = %d AND ip_address = %s", TRACK_RECORD_AUDIO_DB,$audio_id, $user_id, $client_ip);
    }else {
        // if user is not logged in, then check will be based on ip_address
        $sql = $wpdb->prepare("SELECT play_count FROM %s WHERE audio_id = %d AND user_id = %d", TRACK_RECORD_AUDIO_DB,$audio_id, $user_id);
    }
    
    $gallery = $wpdb->get_results(hbag_db_getCorrectSql($sql));

    // adding new audio record 
    if(empty($gallery)){
        $sql = $wpdb->prepare("INSERT INTO %s (audio_id, user_id, ip_address) VALUES (%d, %d, %s)", TRACK_RECORD_AUDIO_DB, $audio_id, $user_id, $client_ip);
        $gallery = $wpdb->get_results(hbag_db_getCorrectSql($sql));
    }
    $play_count = $gallery[0]->play_count;
    
    //incrementing play count by 1
    $play_count++;
    
    // updating play count
    $sql = $wpdb->prepare("UPDATE %s SET play_count = %d WHERE audio_id = %d AND user_id = %d", TRACK_RECORD_AUDIO_DB, $play_count, $audio_id, $user_id);
    */
    $sql = $wpdb->prepare("INSERT INTO %s (audio_id, user_id, ip_address, audio_title) VALUES (%d, %d, %s, %s)", TRACK_RECORD_AUDIO_DB, $audio_id, $user_id, $client_ip, $audio_title);
    $gallery = $wpdb->get_results(hbag_db_getCorrectSql($sql), ARRAY_A);

    return true;
}

// daily top #
function get_daily_top_audios_list($limit)
{
    global $wpdb;

    $gallery = array();
    // $sql = $wpdb->prepare("SELECT * FROM %s a JOIN %s r ON a.aid = r.audio_id WHERE DATE(updated_at) = DATE(NOW()) ORDER BY r.play_count DESC LIMIT %d", AUDIO_DB, TRACK_RECORD_AUDIO_DB, $limit);
    $sql = $wpdb->prepare("SELECT *, COUNT(r.audio_id) total FROM %s a JOIN %s r ON a.aid = r.audio_id WHERE DATE(updated_at) = DATE(NOW()) GROUP BY audio_id ORDER BY total DESC LIMIT %d", AUDIO_DB, TRACK_RECORD_AUDIO_DB, $limit);
    $gallery = $wpdb->get_results(hbag_db_getCorrectSql($sql), ARRAY_A);

    return $gallery;
}

// weekly top #
function get_weekly_top_audios_list($limit)
{
    global $wpdb;

    $gallery = array();
    // $sql = $wpdb->prepare("SELECT * FROM %s a JOIN %s r ON a.aid = r.audio_id WHERE DATE(updated_at) <= DATE(NOW()) AND DATE(updated_at) >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY r.play_count DESC LIMIT %d", AUDIO_DB, TRACK_RECORD_AUDIO_DB, $limit);
    $sql = $wpdb->prepare("SELECT *, COUNT(r.audio_id) total FROM %s a JOIN %s r ON a.aid = r.audio_id WHERE  DATE(updated_at) <= DATE(NOW()) AND DATE(updated_at) >= DATE_SUB(NOW(), INTERVAL 7 DAY) GROUP BY audio_id ORDER BY total DESC LIMIT %d", AUDIO_DB, TRACK_RECORD_AUDIO_DB, $limit);
    $gallery = $wpdb->get_results(hbag_db_getCorrectSql($sql), ARRAY_A);

    return $gallery;
}

// 10 recently played audios
function get_recently_played_audios_list()
{
    global $wpdb;

    $gallery = array();
    // $sql = $wpdb->prepare("SELECT title, ip_address FROM %s a JOIN %s r ON a.aid = r.audio_id WHERE DATE(updated_at) = DATE(NOW()) ORDER BY r.play_count DESC", AUDIO_DB, TRACK_RECORD_AUDIO_DB);
    $sql = $wpdb->prepare("SELECT title, ip_address, COUNT(audio_id) total FROM %s a JOIN %s r ON a.aid = r.audio_id WHERE DATE(updated_at) = DATE(NOW()) GROUP BY audio_id ORDER BY total DESC", AUDIO_DB, TRACK_RECORD_AUDIO_DB);
    $gallery = $wpdb->get_results(hbag_db_getCorrectSql($sql), OBJECT);
    return $gallery;
}

// to show on map
function get_total_played_audios_ip_list()
{
    global $wpdb;

    $gallery = array();
    // $sql = $wpdb->prepare("SELECT title, ip_address FROM %s a JOIN %s r ON a.aid = r.audio_id", AUDIO_DB, TRACK_RECORD_AUDIO_DB);
    $sql = $wpdb->prepare("SELECT ip_address, COUNT(ip_address) FROM %s GROUP BY ip_address", TRACK_RECORD_AUDIO_DB);
    $gallery = $wpdb->get_results(hbag_db_getCorrectSql($sql), OBJECT);

    return $gallery;
}

function get_audio_details($audio_id) {
    global $wpdb;

    $sql = $wpdb->prepare("SELECT title FROM %s a WHERE aid = %d", AUDIO_DB, $audio_id);
    $gallery = $wpdb->get_row(hbag_db_getCorrectSql($sql));

    return $gallery->title;
}

function get_gid_using_aid($aid) {
    global $wpdb;

    $sql = $wpdb->prepare("SELECT gid FROM %s WHERE aid = %d", AUDIO_DB, $aid);
    $gid = $wpdb->get_results(hbag_db_getCorrectSql($sql), OBJECT);

    return $gid;
}
