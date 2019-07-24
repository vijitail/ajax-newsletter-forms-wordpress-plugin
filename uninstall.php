<?php

/**
 * Trigger this file on Plugin uninstall
 * 
 * @package ANFPlugin
*/

if(!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}


global $wpdb;

$wpdb->query("DROP TABLE IF EXISTS $this->table_name;");
