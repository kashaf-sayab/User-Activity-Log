<?php
if (!defined('ABSPATH')) {
    exit; 
}
if (!user_can_view_logs()) {
    echo '<h2>You do not have permission to view this page.</h2>';
    exit;
}
echo '<h1>User Activity Log</h1>';
global $wpdb;
$table_name = $wpdb->prefix . 'user_activity_log';
$query = "SELECT * FROM $table_name ORDER BY timestamp DESC";
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
    echo '</tbody>';
        echo '<tfoot>
            <tr>
                <th>Date</th>
                 <th>ID</th>
                <th>User</th>
                <th>Activity Type</th>
                <th>Description</th>
            </tr>
        </tfoot>';
 echo '</table>';