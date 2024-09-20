<?php
function ual_display_dashboard_widget() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_activity_log';
    $recent_activities = $wpdb->get_results("
        SELECT * FROM $table_name 
        ORDER BY timestamp DESC 
        LIMIT 10
    ");

    if (!empty($recent_activities)) {
        echo '<ul>';
        foreach ($recent_activities as $activity) {
            echo '<li>';
            echo '<strong>' . esc_html($activity->activity_type) . '</strong> - ';
            echo esc_html($activity->activity_description) . ' ';
            echo '<em>on ' . esc_html(date('F j, Y, g:i a', strtotime($activity->timestamp))) . '</em>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo 'No recent activity found.';
    }
}
