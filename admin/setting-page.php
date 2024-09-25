<?php
function logging_settings_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }
    if (isset($_POST['logging_settings_nonce']) && wp_verify_nonce($_POST['logging_settings_nonce'], 'save_logging_settings')) {
        //Prepare the settings data to be saved
        $settings = array(
            'log_retention' => intval($_POST['log_retention']),
            'notification_preferences' => isset($_POST['notification_preferences']) ? $_POST['notification_preferences'] : array(),
            'access_control' => isset($_POST['access_control']) ? $_POST['access_control'] : array(),
            'user_access_control' => isset($_POST['user_access_control']) ? $_POST['user_access_control'] : array(), // Saving user selections
        );
        //Update the settings in the WordPress options table
        update_option('logging_settings', $settings);
        echo '<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>';
    }

    $settings = get_option('logging_settings', array(
        'log_retention' => 30,
        'notification_preferences' => array(),
        'access_control' => array(),
        'user_access_control' => array(), 
    ));

    if (!is_array($settings['user_access_control'])) {
        $settings['user_access_control'] = array();
    }

    $users = get_users(array('fields' => array('ID', 'display_name')));
    ?>
    
    <div class="wrap">
        <h1>Logging Settings</h1>
        <form method="post" action="">
            <?php wp_nonce_field('save_logging_settings', 'logging_settings_nonce'); ?>
            <table class="form-table">
                <!-- Log Retention Period Field -->
                <tr valign="top">
                    <th scope="row">Log Retention Period (days)</th>
                    <td>
                        <input type="number" name="log_retention" value="<?php echo esc_attr($settings['log_retention']); ?>" />
                        <p class="description">Number of days to retain logs before deletion.</p>
                    </td>
                </tr>

                <!-- Notification Preferences Field -->
                <tr valign="top">
                    <th scope="row">Notification Preferences</th>
                    <td>
                        <label><input type="checkbox" name="notification_preferences[]" value="content_change" <?php checked(in_array('content_change', $settings['notification_preferences'])); ?> /> Notify on Content Change</label><br>
                        <label><input type="checkbox" name="notification_preferences[]" value="failed_login" <?php checked(in_array('failed_login', $settings['notification_preferences'])); ?> /> Notify on Failed Login Attempt</label><br>
                    </td>
                </tr>

                <!-- Role-Based Access Control Field -->
                <tr valign="top">
                    <th scope="row">Role-Based Access Control</th>
                    <td>
                        <?php
                        // Get all roles in WordPress
                        $roles = get_editable_roles();
                        $access_control = isset($settings['access_control']) && is_array($settings['access_control']) ? $settings['access_control'] : array();
                        
                        foreach ($roles as $role_key => $role) {
                            $checked = in_array($role_key, $access_control) ? 'checked' : '';
                            echo '<label><input type="checkbox" name="access_control[]" value="' . esc_attr($role_key) . '" ' . $checked . ' /> ' . esc_html($role['name']) . '</label><br>';
                        }
                        ?>
                        <p class="description">Select roles that have access to view logs.</p>
                    </td>
                </tr>

                <!-- User-Specific Access Control Field -->
                <tr valign="top">
                    <th scope="row">User-Specific Access Control</th>
                    <td>
                        <?php
                        // Display all users with checkboxes for specific access control
                        foreach ($users as $user) {
                            $checked = in_array($user->ID, $settings['user_access_control']) ? 'checked' : '';
                            echo '<label><input type="checkbox" name="user_access_control[]" value="' . esc_attr($user->ID) . '" ' . $checked . ' /> ' . esc_html($user->display_name) . '</label><br>';
                        }
                        ?>
                        <p class="description">Select specific users who can access logs.</p>
                    </td>
                </tr>
            </table>

            <!-- Submit button to save changes -->
            <p class="submit">
                <input type="submit" class="button-primary" value="Save Changes" />
            </p>
        </form>
    </div>
    <?php
}
function delete_old_logs() {
    $settings = get_option('logging_settings', array('log_retention' => 30));
    $retention_period = intval($settings['log_retention']);
    $date_threshold = date('Y-m-d', strtotime("-$retention_period days"));

    global $wpdb;
    $table_name = $wpdb->prefix . 'user_activity_log';
    $query = $wpdb->prepare("DELETE FROM $table_name WHERE timestamp < %s", $date_threshold);
    $wpdb->query($query);
}
if (!wp_next_scheduled('daily_log_cleanup')) {
    wp_schedule_event(time(), 'daily', 'daily_log_cleanup');
}

add_action('daily_log_cleanup', 'delete_old_logs');
function user_can_view_logs() {
    $settings = get_option('logging_settings', array());
    
    $allowed_roles = isset($settings['access_control']) && is_array($settings['access_control']) ? $settings['access_control'] : array();
    $allowed_users = isset($settings['user_access_control']) && is_array($settings['user_access_control']) ? $settings['user_access_control'] : array();

    // Get current user
    $current_user = wp_get_current_user();
    $user_roles = $current_user->roles;
    $user_id = $current_user->ID;
    if (current_user_can('view_plugin_logs')) {
        return true; 
    }
    if (empty($allowed_roles) && empty($allowed_users)) {
        return true;
    }
    if (array_intersect($user_roles, $allowed_roles)) {
        return true; 
    }

    if (in_array($user_id, $allowed_users)) {
        return true; 
    }

    return false; 
}
function save_logging_settings($input) {
    if (isset($input['access_control']) && is_array($input['access_control'])) {
        $input['access_control'] = array_map('sanitize_text_field', $input['access_control']);
    } else {
        $input['access_control'] = array();
    }

    if (isset($input['user_access_control']) && is_array($input['user_access_control'])) {
        $input['user_access_control'] = array_map('intval', $input['user_access_control']);
    } else {
        $input['user_access_control'] = array();
    }

    return $input;
}

add_filter('pre_update_option_logging_settings', 'save_logging_settings');
