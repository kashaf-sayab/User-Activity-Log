<?php
// Ensure this file is only executed when uninstalling the plugin
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

global $wpdb;

// Delete the custom user activity log table
$wpdb->query( "DROP TABLE IF EXISTS `{$wpdb->prefix}user_activity_log`;" );

// Remove any options or settings created by the plugin
delete_option( 'activity_log_db_version' );
