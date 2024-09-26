<?php
if (!defined('ABSPATH')) {
    exit;
}
//insert record into table
function ual_log_activity($user_id, $activity_type, $activity_description) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_activity_log';
    
    $wpdb->insert($table_name, [
        'user_id'            => $user_id,
        'activity_type'      => $activity_type,
        'activity_description' => $activity_description,
        'timestamp'          => current_time('mysql'),
    ]);
}


//for login action
function ual_log_login($user_login, $user) {
    ual_log_activity($user->ID, 'Login', "$user_login logged in.");
}


//for logout action
function ual_log_logout() {
    $user = wp_get_current_user();
    if ($user->ID) {
        ual_log_activity($user->ID, 'Logout', "{$user->user_login} logged out.");
    }
}



//for view page action
function ual_log_page_view() {
    if (is_singular()) { 
        global $post;
        $user_id = get_current_user_id();
        
        if ($user_id) {
            ual_log_activity($user_id, 'Page View', "Viewed page/post: " . $post->post_title);
        }
    }
}


//for content creation/modification action
function ual_log_content_changes($post_id, $post, $update) {

    if (wp_is_post_revision($post_id) || defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    $user_id = get_current_user_id();
    $action = $update ? 'Updated' : 'Created';
    
    if ($user_id) {
        ual_log_activity($user_id, 'Content Change', "$action post/page: " . $post->post_title);
    }
}
//for comment action
function ual_log_comment($comment_id, $comment_object) {
    $user_id = $comment_object->user_id;
    if ($user_id) {
        ual_log_activity($user_id, 'Comment', "Posted a comment on: " . get_the_title($comment_object->comment_post_ID));
    }
}

//for media upload action
function ual_log_media_upload($post_id) {
    $user_id = get_current_user_id();
    $attachment = get_post($post_id);
    
    if ($user_id) {
        ual_log_activity($user_id, 'Media Upload', "Uploaded media: " . $attachment->post_title);
    }
}

function ual_log_failed_login($username) {
    $user = get_user_by('login', $username);
    $user_id = $user ? $user->ID : 0; // Get user ID if exists, otherwise set to 0

    ual_log_activity($user_id, 'Failed Login', "Failed login attempt for username: " . esc_html($username));
}

//action hooks
add_action('wp_login', 'ual_log_login', 10, 2);//10 priority,2 argument passed
add_action('clear_auth_cookie', 'ual_log_logout', 10);
add_action('template_redirect', 'ual_log_page_view');
add_action('save_post', 'ual_log_content_changes', 10, 3);
add_action('wp_insert_comment', 'ual_log_comment', 10, 2);
add_action('add_attachment', 'ual_log_media_upload');
add_action('wp_login_failed', 'ual_log_failed_login');