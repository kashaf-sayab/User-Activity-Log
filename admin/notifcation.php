<?php
function display_admin_notification() {
    if (!current_user_can('manage_options')) {
        return;
    }
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_activity_log';
    // Fetch the latest logs
    $content_change_log = $wpdb->get_row(
        "SELECT * FROM $table_name WHERE activity_type = 'content change' ORDER BY timestamp DESC LIMIT 1"
    );

    $failed_login_log = $wpdb->get_row(
        "SELECT * FROM $table_name WHERE activity_type = 'failed login' ORDER BY timestamp DESC LIMIT 1"
    );

    // Get notification preferences
    $settings = get_option('logging_settings', array('notification_preferences' => array()));
    // Get the last shown notification timestamp
    $last_notification_time = get_option('last_admin_notification_time', 0);
    $current_time = current_time('timestamp');

    // Check if there's a new content change and if we haven't shown it yet
    if ($content_change_log && in_array('content change', $settings['notification_preferences'])) {
        if (strtotime($content_change_log->timestamp) > $last_notification_time) {
            echo '<div class="notice notice-info is-dismissible">';
            echo '<p><strong>Content Change Alert:</strong> A content change was made by user ID ' . esc_html($content_change_log->user_id) . ' on ' . esc_html($content_change_log->timestamp) . '.</p>';
            echo '</div>';
            // Update the last notification time
            update_option('last_admin_notification_time', $current_time);
        }
    }

    // Check if there's a new failed login and if we haven't shown it yet
    if ($failed_login_log && in_array('failed login', $settings['notification_preferences'])) {
        if (strtotime($failed_login_log->timestamp) > $last_notification_time) {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p><strong>Failed Login Alert:</strong> A failed login attempt was made by user ID ' . esc_html($failed_login_log->user_id) . ' on ' . esc_html($failed_login_log->timestamp) . '.</p>';
            echo '</div>';
            // Update the last notification time
            update_option('last_admin_notification_time', $current_time);
        }
    }

    // JavaScript for auto-dismiss of notifications
    echo '
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            setTimeout(function() {
                var notices = document.querySelectorAll(".notice.is-dismissible");
                notices.forEach(function(notice) {
                    notice.style.transition = "opacity 0.5s ease-out";
                    notice.style.opacity = "0";
                    setTimeout(function() {
                        notice.remove();
                    }, 500); // Wait for the fade-out transition before removing
                });
            }, 8000); // 8 seconds
        });
    </script>';
}
add_action('admin_notices', 'display_admin_notification');


// notifacation for content change
function notify_content_change($post_id) {
    $settings = get_option('logging_settings', array());
    if (isset($settings['notification_preferences']) && in_array('content change', $settings['notification_preferences'])) {
        $post = get_post($post_id);
        $to = get_option('admin_email');
        $subject = 'Content Updated: ' . $post->post_title;
        $message = 'The content has been updated for post ID ' . $post_id . '.';
        
        wp_mail($to, $subject, $message);
    }
}

// notifcation for failed logged in
function notify_failed_login( $username, $password) {
    $settings = get_option('logging_settings', array());
    if (isset($settings['notification_preferences']) && in_array('failed login', $settings['notification_preferences'])) {
        $to = get_option('admin_email');
        $subject = 'Failed Login Attempt';
        $message = 'Failed login attempt with username: ' . $username . '.';
        
        wp_mail($to, $subject, $message);
    }
}
add_action('save_post', 'notify_content_change');
add_action('wp_login_failed', 'notify_failed_login', 10, 3);