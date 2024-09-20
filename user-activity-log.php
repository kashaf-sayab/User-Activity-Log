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

define('UAL_PLUGIN_PATH', plugin_dir_path(__FILE__));

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

require_once(plugin_dir_path(__FILE__) . 'includes/user-activity-log-table.php');
require_once(plugin_dir_path(__FILE__) . 'admin/log-function.php');
require_once(plugin_dir_path(__FILE__) . 'includes/class-activity-log-managment.php');
require_once(plugin_dir_path(__FILE__) . 'includes/dashboard-widgets.php');