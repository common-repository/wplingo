<?php
/* 
 * WPTalkBoard uninstaller
 */

global $wpdb;

if(!defined("WP_UNINSTALL_PLUGIN")) {
    return;
}

// delete all options with name starting with 'adverts_'
$results = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options WHERE (option_name LIKE 'wptb_%' OR option_name LIKE 'wptb_%') ");

foreach($results as $option) {
    delete_option($option->option_name);
}