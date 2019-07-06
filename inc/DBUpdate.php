<?php

/**
 * @package ANFPlugin
*/

include ABSPATH . 'wp-admin/includes/upgrade.php';

class DBUpdate {

    function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->table_name = $wpdb->prefix.'ajax_newsletter_forms'; 
    }

    function create_ANF_table() {

        $charset_collate = $this->db->get_charset_collate();

        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            shortcode text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";
    
        dbDelta( $sql );
    }

    function delete_ANF_table() {

        $this->db->query("DROP TABLE IF EXISTS $this->table_name;");
    
    }
}