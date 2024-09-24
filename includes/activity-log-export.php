<?php
function export_user_activity_logs() {
    if (!is_admin()) {
        return;
    }

    // Check if export is triggered
    if (isset($_GET['export']) && ($_GET['export'] === 'csv' || $_GET['export'] === 'excel')) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'user_activity_log';

        // Fetch logs from the database
        $logs = $wpdb->get_results("SELECT * FROM $table_name");

        if ($logs) {
            $export_type = $_GET['export'];

            if ($export_type === 'csv') {
                // Export as CSV
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment;filename=user-activity-log.csv');
                $output = fopen('php://output', 'w');//open file or stream 

                
                fputcsv($output, ['Date', 'ID', 'User', 'Activity Type', 'Description']);

                foreach ($logs as $log) {
                    $user_info = get_userdata($log->user_id);
                    fputcsv($output, [
                        $log->timestamp,
                        $log->id,
                        $user_info->display_name,
                        $log->activity_type,
                        $log->activity_description
                    ]);
                }

                fclose($output);
            } elseif ($export_type === 'excel') {
                // Export as Excel
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename=user-activity-log.xls');
                
                echo "Date\tID\tUser\tActivity Type\tDescription\n";

                foreach ($logs as $log) {
                    $user_info = get_userdata($log->user_id);
                    echo $log->timestamp . "\t" . $log->id . "\t" . $user_info->display_name . "\t" . $log->activity_type . "\t" . $log->activity_description . "\n";
                }
            }

            exit();
        } else {
            wp_die('No logs found to export.');
        }
    }
}

function ual_backup_logs() {
    if (!is_admin()) {
        return;
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'user_activity_log';

    // Handle backup action
    if (isset($_POST['action']) && $_POST['action'] == 'backup_logs') {
        check_admin_referer('backup_restore_nonce');

        // Define the backup directory and file name
        $backup_dir = plugin_dir_path(__FILE__) . 'backups/';
        $backup_filename = $backup_dir . 'activity_log_backup_' . time() . '.sql';

        // Create the backups directory if it does not exist
        if (!file_exists($backup_dir)) {
            if (!mkdir($backup_dir, 0755, true)) {
                echo "<div class='notice notice-error'><p>Failed to create the backups directory.</p></div>";
                return;
            }
        }

        // Open the backup file for writing
        $backup_file = fopen($backup_filename, 'w');

        if ($backup_file) {
            // Fetch logs from the database
            $logs = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);
            foreach ($logs as $log) {
                $log_line = implode(',', array_map('esc_sql', $log)) . "\n";
                fwrite($backup_file, $log_line);
            }
            fclose($backup_file);
            echo "<div class='notice notice-success'><p>Backup created at: $backup_filename</p></div>";
        } else {
            echo "<div class='notice notice-error'><p>Failed to create the backup file.</p></div>";
        }
    }


    // Handle restore action
    if (isset($_POST['action']) && $_POST['action'] == 'restore_backup') {
        check_admin_referer('backup_restore_nonce');

        if (!empty($_FILES['backup_file']['tmp_name'])) {
            $backup_file = $_FILES['backup_file']['tmp_name'];
            $file = fopen($backup_file, 'r');

            if ($file) {
                // First, clear the existing logs in the table
                $wpdb->query("TRUNCATE TABLE $table_name");

                // Read each line from the uploaded file and insert it into the database
                while (($line = fgetcsv($file)) !== false) {
                    $log_data = array(
                        'id' => esc_sql($line[0]),
                        'user_id' => esc_sql($line[1]),
                        'activity_type' => esc_sql($line[2]),
                        'activity_description' => esc_sql($line[3]),
                        'timestamp' => esc_sql($line[4]),
                    );
                    $wpdb->insert($table_name, $log_data);
                }
                fclose($file);
                echo "<div class='notice notice-success'><p>Logs restored from backup.</p></div>";
            } else {
                echo "<div class='notice notice-error'><p>Failed to open the backup file.</p></div>";
            }
        } else {
            echo "<div class='notice notice-error'><p>Please upload a valid backup file.</p></div>";
        }
    }
}
add_action('admin_post_backup_logs', 'ual_backup_logs');
add_action('admin_init', 'export_user_activity_logs');
