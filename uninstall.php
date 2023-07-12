<?php
// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

// drop database tables
global $wpdb;
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}nf_entries" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}nf_entry_meta" );

?>