<?php
function ual_display_activity_log() {

    global $wpdb;
    $table_name = $wpdb->prefix . 'user_activity_log';
    
    echo '<div class="wrap">';
    echo '<h1>User Activity Log</h1>';

    $query = "SELECT * FROM $table_name";
    $logs = $wpdb->get_results($query);

    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Activity Type</th>
                <th>Description</th>
                <th>Timestamp</th>
            </tr>
          </thead>';
    echo '<tbody>';
    
    if ($logs) {
        foreach ($logs as $log) {
            $user_info = get_userdata($log->user_id);
            echo '<tr>';
            echo '<td>' . esc_html($log->id) . '</td>';
            echo '<td>' . esc_html($user_info->display_name) . '</td>';
            echo '<td>' . esc_html($log->activity_type) . '</td>';
            echo '<td>' . esc_html($log->activity_description) . '</td>';
            echo '<td>' . esc_html($log->timestamp) . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="5">No logs found.</td></tr>';
    }

    echo '</div>';
}

?>
