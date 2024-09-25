<?php
    function ual_display_activity_log() {

        ual_backup_logs();

        global $wpdb;
        $table_name = $wpdb->prefix . 'user_activity_log';
        
        $logs_per_page = 10;

        // Get the current page number from the query parameter, default to 1
        $paged = isset($_GET['paged']) ? absint($_GET['paged']) : 1;

        // Calculate the offset
        $offset = ($paged - 1) * $logs_per_page;
        echo '<div class="wrap">';
        echo '<h1>User Activity Log</h1>';

        // Backup & Restore Forms
        echo '<div style="margin-bottom: 20px; display: flex; justify-content: space-between;">';
        echo '<form method="post" style="display: inline-block;">';
        echo '<input type="hidden" name="action" value="backup_logs" />';
        wp_nonce_field('backup_restore_nonce');
        echo '<button type="submit" style="padding: 7px 15px; background-color: #0073aa; color: #fff; border: none; border-radius: 3px;">Create Backup</button>';
        echo '</form>';
    
        echo '<form method="post" enctype="multipart/form-data" style="display: inline-block;">';
        echo '<input type="hidden" name="action" value="restore_backup" />';
        wp_nonce_field('backup_restore_nonce');
        echo '<label for="backup_file" style="margin-right: 10px;">Restore Backup:</label>';
        echo '<input type="file" name="backup_file" id="backup_file" style="margin-right: 10px;" />';
        echo '<button type="submit" style="padding: 7px 15px; background-color: #0073aa; color: #fff; border: none; border-radius: 3px;">Restore Backup</button>';
        echo '</form>';
        echo '</div>';


        //filter form
        echo '<form method="get" action=""style="margin-bottom: 20px;">';
            echo '<input type="hidden" name="page" value="' . esc_attr($_GET['page']) . '" />';//hidden input ensures that the results show up on the same page
            echo '<div style="margin-bottom: 10px;">';
            echo '<label for="user-filter">User: </label>';
            echo '<select name="user" id="user-filter">';
                echo '<option value="">All Users</option>';

                $users = get_users();
                        foreach ($users as $user) {
                            $selected = (isset($_GET['user']) && $_GET['user'] == $user->ID) ? 'selected' : '';
                            echo '<option value="' . esc_attr($user->ID) . '" ' . $selected . '>' . esc_html($user->display_name) . '</option>';
                        }
                echo '</select>';
            //for action type
            echo '<label for="action-filter">Action Type: </label>';
            echo '<select name="action_type" id="action-filter">';
                    echo '<option value="">All Actions</option>';
                    $action_types = ['login', 'logout', 'page view','content change', 'comment', 'media upload'];
                        foreach ($action_types as $type) {
                                $selected = (isset($_GET['action_type']) && $_GET['action_type'] == $type) ? 'selected' : '';
                                echo '<option value="' . esc_attr($type) . '" ' . $selected . '>' . ucfirst($type) . '</option>';
                            }
                    echo '</select>';
            //for start date
            echo '<label for="start-date">Start Date: </label>';
            echo '<input type="date" name="start_date" id="start-date" value="' . (isset($_GET['start_date']) ? esc_attr($_GET['start_date']) : '') . '" />';
            //for end date
            echo '<label for="end-date">End Date: </label>';
            echo '<input type="date" name="end_date" id="end-date" value="' . (isset($_GET['end_date']) ? esc_attr($_GET['end_date']) : '') . '" />';
            //filter button
            echo '<button type="submit" style="padding: 7px 15px; background-color: #0073aa; color: #fff; border: none; border-radius: 3px;">Filter</button>';
            //for search 
                echo '<div style="float: right; margin-top: 0;">';
                        echo '<input type="text" name="search_term" placeholder="Search..." value="' . (isset($_GET['search_term']) ? esc_attr($_GET['search_term']) : '') . '" style="padding: 2px; border: 1px solid #ccc; border-radius: 3px;" />';
                        echo '<button type="submit" style="padding: 7px 10px; background-color: #0073aa; color: #fff; border: none; border-radius: 3px;">Search</button>';
                echo '</div>';
            echo '</div>';
        echo '</form>';

        $query = "SELECT * FROM $table_name WHERE 1=1";// 1=1 to allow adding further conditions
            // Apply filters
            if (isset($_GET['user']) && !empty($_GET['user'])) {
                $user_id = intval($_GET['user']);
                $query .= " AND user_id = $user_id";
            }
        
            if (isset($_GET['action_type']) && !empty($_GET['action_type'])) {
                $action_type = esc_sql($_GET['action_type']);
                $query .= " AND activity_type = '$action_type'";
            }
        
            if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
                $start_date = esc_sql($_GET['start_date']);
                $query .= " AND timestamp >= '$start_date'";
            }
        
            if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
                $end_date = esc_sql($_GET['end_date']);
                $query .= " AND timestamp <= '$end_date'";
            }
        
            if (isset($_GET['search_term']) && !empty($_GET['search_term'])) {
                $search_term = esc_sql($_GET['search_term']);
                $query .= " AND (activity_description LIKE '%$search_term%' OR activity_type LIKE '%$search_term%')";
                
            }
             // For add link on date to show same date activity logs
            if (isset($_GET['date']) && !empty($_GET['date'])) {
                $date = esc_sql($_GET['date']);
                 $query .= " AND DATE(timestamp) = '$date'";
            }
            // For add link on activity type to show logs of same activity
            if (isset($_GET['activity_type']) && !empty($_GET['activity_type'])) {
                $activity_type = esc_sql($_GET['activity_type']);
                $query .= " AND activity_type = '$activity_type'";
            }
            // Sort by timestamp in descending order
            $query .= " ORDER BY timestamp DESC";

            // Add pagination limits
            $query .= " LIMIT $logs_per_page OFFSET $offset";

            // Execute the query
            $logs = $wpdb->get_results($query);

            // Get total count of logs for pagination 
            $total_logs_query = "SELECT COUNT(*) FROM $table_name WHERE 1=1";
        
                // Apply the same filters for pagination 
                if (isset($_GET['user']) && !empty($_GET['user'])) {
                    $total_logs_query .= " AND user_id = $user_id";
                }

                if (isset($_GET['action_type']) && !empty($_GET['action_type'])) {
                    $total_logs_query .= " AND activity_type = '$action_type'";
                }

                if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
                    $total_logs_query .= " AND timestamp >= '$start_date'";
                }

                if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
                    $total_logs_query .= " AND timestamp <= '$end_date'";
                }

                if (isset($_GET['search_term']) && !empty($_GET['search_term'])) {
                    $total_logs_query .= " AND (activity_description LIKE '%$search_term%' OR activity_type LIKE '%$search_term%')";
                }

                $total_logs = $wpdb->get_var($total_logs_query);


            echo '<table class="wp-list-table widefat fixed striped">';
                    echo '<thead>
                        <tr>
                        <th>Date</th>
                         <th>ID</th>
                        <th>User</th>
                        <th>Activity Type</th>
                        <th>Description</th>
                        </tr>
                </thead>';
                echo '<tbody>';
                
                        if ($logs) {
                            foreach ($logs as $log) {
                                $user_info = get_userdata($log->user_id);
                                $user_roles = implode(', ', array_map('ucfirst', $user_info->roles));
                                $user_activity_url = add_query_arg(['user' => $log->user_id], $_SERVER['REQUEST_URI']);
                                // Get user avatar
                                $user_avatar = get_avatar($user_info->ID, 40); 
        
                                echo '<tr>';
                                    // Format the timestamp
                                    $timestamp = strtotime($log->timestamp);
                                    $time_ago = human_time_diff($timestamp, current_time('timestamp')) . ' ago';
                                    $date_formatted = date('F j, Y', $timestamp);
                                    $time_formatted = date('g:i a', $timestamp);

                                    // Create the URL for the date filter
                                    $date_activity_url = add_query_arg(['date' => date('Y-m-d', $timestamp)], $_SERVER['REQUEST_URI']);
    
                                    // Output the timestamp in three lines with the date as a link
                                    echo '<td>' . esc_html($time_ago) . '<br><a href="' . esc_url($date_activity_url) . '">' . esc_html($date_formatted) . '</a><br>' . esc_html($time_formatted) . '</td>';
                                    echo '<td>' . esc_html($log->id) . '</td>';
                                    echo '<td>' . $user_avatar . ' <a href="' . esc_url($user_activity_url) . '">' . esc_html($user_info->display_name) . '</a><br>' . esc_html($user_roles) . '</td>';
                                    echo '<td><a href="' . esc_url(add_query_arg('activity_type', esc_attr($log->activity_type), $_SERVER['REQUEST_URI'])) . '">' . esc_html($log->activity_type) . '</a></td>';
                                    echo '<td>' . esc_html($log->activity_description) . '</td>';
            
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
      // Add export buttons 
      echo '<div style="margin-top: 20px;">';
      echo '<a href="' . esc_url(add_query_arg('export', 'csv')) . '" class="button button-primary" style="margin-right: 10px;">Export CSV</a>';
      echo '<a href="' . esc_url(add_query_arg('export', 'excel')) . '" class="button button-primary">Export Excel</a>';
      echo '</div>';
          echo '</div>';
        
       // Pagination links
       $total_pages = ceil($total_logs / $logs_per_page);//ceil round-up nearest whole number

        if ($total_pages > 1) {
            echo '<div class="tablenav"><div class="tablenav-pages">';
            echo paginate_links([
            'base'      => add_query_arg('paged', '%#%'),
            'format'    => '?paged=%#%',
            'current'   => max(1, $paged),
            'total'     => $total_pages,
            'prev_text' => __('<< Prev'),
            'next_text' => __('Next >>'),
           ]);
             echo '</div></div>';
        }
    } 
?>
