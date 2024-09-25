<?php
/*
Plugin Name: User Activity Log
Plugin URI:  https://example.com/
Description: This plugin records and displays user activity on the site, such as login times, page views, and changes to content. It helps site administrators track user behavior and enhance security.
Version:     1.0
Author:      Kashaf Sayab
Author URI:  https://example.com/
License:     GPL2
*/

if (!defined('ABSPATH')) {
    exit;
}

define( 'ACTIVITY_LOG__FILE__', __FILE__ );
define( 'ACTIVITY_LOG_BASE', plugin_basename( ACTIVITY_LOG__FILE__ ) );

add_action('admin_menu', 'ual_add_admin_menu');

function ual_add_admin_menu() {
    add_menu_page(
        'User Activity Log',           
        'User Activity Log',          
        'manage_options',             
        'user-activity-log',           
        'ual_display_activity_log',    
        'dashicons-clock',        
        2                           
    );
}
function ual_add_dashboard_widgets() {
    wp_add_dashboard_widget(
        'ual_dashboard_widget',         
        'Recent User Activity',          
        'ual_display_dashboard_widget'   
    );
}
add_action('wp_dashboard_setup', 'ual_add_dashboard_widgets');
function logging_settings_page() {
    add_submenu_page(
        'user-activity-log', 
        'Settings', 
        'Settings', 
        'manage_options',   
        'logging-settings',
        'logging_settings_page_html'
    );
}

add_action('admin_menu', 'logging_settings_page');

include('includes/user-activity-log-table.php');
include('admin/log-function.php');
include('includes/class-activity-log-managment.php');
include('includes/dashboard-widgets.php');
include('includes/activity-log-export.php');
include('admin/setting-page.php');