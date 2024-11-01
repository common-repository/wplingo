<?php
/**
 * AJAX Actions
 * 
 * This functions are executed when user is doing an AJAX request.
 *
 * @package     Lingo
 * @copyright   Copyright (c) 2016, Marek Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'wp_ajax_lingo_post_edit_save', 'lingo_post_edit_save' );
add_action( 'wp_ajax_lingo_post_trash', 'lingo_post_trash' );
add_action( 'wp_ajax_lingo_post_preview', 'lingo_post_preview' );
add_action( 'wp_ajax_lingo_post_remove', 'lingo_post_remove' );
add_action('wp_ajax_lingo_post_remove_perm', 'lingo_post_remove_perm');
add_action('wp_ajax_lingo_post_restore', 'lingo_post_restore');
add_action('wp_ajax_lingo_insert_new_post', 'lingo_insert_new_post');
add_action('wp_ajax_lingo_insert_new_forum', 'lingo_insert_new_forum');
add_action('wp_ajax_lingo_change_topic_status', 'lingo_change_topic_status');
add_action('wp_ajax_lingo_topic_trash', 'lingo_topic_trash');
add_action('wp_ajax_lingo_subscription_save', 'lingo_subscription_save');
add_action('wp_ajax_lingo_subscription_remove', 'lingo_subscription_remove');

/**
 * Saves Lingo_Post afert edition.
 * 
 * @return void
 */
function lingo_post_edit_save() {
    //global $wpdb; // this is how you get access to the database

    $post_id = intval(lingo_request("post_id"));
    //$msg = sanitize_text_field(lingo_request("post_message"));
    $msg = strip_tags(trim(lingo_request("post_message")), "<p><br><b><i><u><ul><li>");
    
    $valid = true;
    if(!is_numeric($post_id) || !($post_id > 0)) {
        $valid = false;
    }
    
    if(!is_string($msg) || !(strlen($msg) > 3)) {
        $valid = false;
    }
    
    if($valid) {
        $post = array (
            'ID' => $post_id,
            'post_content' => $msg,
        );

        $post_id = wp_update_post( $post );

        $response = array();
        $response['new_value'] = $post['post_content'];

        if (is_wp_error($post_id)) {
            $response['status'] = 'error';
            $errors = $post_id->get_error_messages();

            foreach ($errors as $error) {
                    $response['error'] .= $error;
            }
        } else {
            $response['status'] = 'ok';
            $response['message'] = __("Changes was saved!", 'lingo');
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = __("There was an error when saving post", 'lingo');
    }

    echo wp_json_encode($response);
    wp_die(); // this is required to terminate immediately and return a proper response
}

/**
 * Moves Lingo_Post into trash
 * 
 * @return void
 */
function lingo_post_trash() {

    $permission = check_ajax_referer( 'lingo-delete-post-nonce', 'nonce', false );
    $response = array();
    
    if( $permission == false ) {
        $response['message'] = __('You do not have permission to remove post', 'lingo');
        $response['status'] = 'error';
    } elseif(isset($_POST['post_id']) && !empty($_POST['post_id'])) {
        wp_trash_post( $_POST['post_id'] );
        $response['message'] = __('Post moved to trash', 'lingo');
        $response['status'] = 'ok';
    } else {
        $response['message'] = __('Post with given id is not exist', 'lingo');
        $response['status'] = 'error';
    }
    
    echo wp_json_encode($response);

    wp_die();
}

/**
 * Shows Lingo_Post preview in thickbox
 * 
 * @return void
 */
function lingo_post_preview() {

    $id = intval(lingo_request('id'));
    if(is_numeric($id) && $id > 0) {
        $post = get_post($id);
        echo esc_html($post->post_content);
    } else {
        _e('Wrong post id!', 'lingo');
    }   
    
    wp_die();
}

/**
 * Show confirmation thickbox on Lingo_Post remove
 * 
 * @return void
 */
function lingo_post_remove() {
    
    $id = intval(lingo_request('id'));
    if(is_numeric($id) && $id > 0) {
        _e("Are you sure that you want to permamently remove this post? This can't be undone!", 'lingo');
        echo esc_html('<br/><br/>');
        echo esc_html('<a href="#" data-id="'.$id.'" class="button button-primary" id="lingo-post-remove-confirmed">Remove</a> ');
    } else {
        _e('Wrong post id!', 'lingo');
    }   
    
    wp_die();
}

/**
 * Removes Ling_Post permamently
 * 
 * @return void
 */
function lingo_post_remove_perm() {
    $id = intval(lingo_request('id'));
    $result = wp_delete_post( $id, true );
    
    $response = array();
    if(!$result) {
        $response['status'] = 'error';
    } else {
        $response['status'] = 'ok';
    }
    
    echo wp_json_encode($response);
    wp_die();
}

/**
 * Restore Lingo_Post from trash
 * 
 * @return void
 */
function lingo_post_restore() {
    $response = array();
    $id = intval(lingo_request('id'));
    
    if(is_numeric($id) && $id > 0) {
        wp_untrash_post($id);
        $response['status'] = 'ok';
    } else {
        $response['status'] = 'error';
    }
    
    echo wp_json_encode($response);
    wp_die();
}

/**
 * Inert new Lingo_Post into database
 * 
 * @return void
 */
function lingo_insert_new_post() {
    $id = intval(lingo_request('parent_id'));
    //$content = sanitize_text_field(lingo_request('post_content'));
    $content = strip_tags(trim(lingo_request("post_content")), "<p><br><b><i><u><ul><li>");
    
    $valid = true;
    if(!is_numeric($id) || !($id > 0)) {
        $valid = false;
    }
    
    if(!is_string($content) || !(strlen($content) > 3)) {
        $valid = false;
    }
    
    if($valid) {
        $attr = array(
            'post_content' => $content,
            'post_parent' => $id,
            'post_type' => 'lingo_post',
            'post_status' => 'publish',
        );

        $response = array();
        $result = wp_insert_post($attr);
        update_post_meta($id, "lingo_last_post_date", time());
        
        $response['effect'] = $result;
    } else {
        $result = null;
    }
    
    if(is_numeric($result) && $result > 0) {
        $response['status'] = 'ok';
    } else {
        $response['status'] = 'error';
    }
    
    echo wp_json_encode($response);
    wp_die();
}

/**
 * Insert new Forum_Group into database
 * 
 * @return void
 */
function lingo_insert_new_forum() {
    $parent_id = intval(lingo_request('parent_id'));
    $name = sanitize_text_field(lingo_request('forum_name'));
    
    $valid = true;
    if(!is_numeric($parent_id) || !($parent_id > 0)) {
        $valid = false;
    }
    
    if(!is_string($name) || !(strlen($name) > 3)) {
        $valid = false;
    }
    
    if($valid) {
        $result = wp_insert_term($name, 'lingo_forum_group', array( 'parent'=> $parent_id ) );
    }
    
    echo wp_json_encode($result);
    wp_die();
}

/**
 * Changes Lingo_Topic status
 * 
 * @return void
 */
function lingo_change_topic_status() {
    $topic_id = intval(lingo_request('topic_id'));
    $status = sanitize_text_field(lingo_request('new_status'));
    
    $valid = true;
    if(!is_numeric($topic_id) || !($topic_id > 0)) {
        $valid = false;
    }
    
    if(!is_string($status) || !(strlen($status) > 3)) {
        $valid = false;
    }
    
    if($valid) {
        $post = array( 'ID' => $topic_id, 'post_status' => $status );
        wp_update_post($post);

        set_transient('lingo_flash_status', 'info', 60 * 5);
        set_transient('lingo_flash_msg', sprintf(__("Topic status was changed to: %s", "lingo"), $status), 60 * 5);
    } else {
        set_transient('lingo_flash_status', 'error', 60 * 5);
        set_transient('lingo_flash_msg', sprintf(__("There was an error when trying to change status %s", "lingo")), 60 * 5);
    }

    //echo wp_json_encode($result);
    wp_die();
}


/**
 * Moves Lingo_Topic into trash
 * 
 * @return void
 */
function lingo_topic_trash() {
    
    $permission = check_ajax_referer( 'lingo-delete-topic-nonce', 'nonce', false );
    
    $response = array();
    if( $permission == false ) {
        $response['message'] = __('You do not have permission to remove topic', 'lingo');
        $response['status'] = 'error';
    } elseif(isset($_POST['topic_id']) && !empty($_POST['topic_id'])) {
        wp_trash_post( $_POST['topic_id'] );
        set_transient('lingo_flash_status', 'ok', 60 * 5);
        set_transient('lingo_flash_msg', __('Topic moved to trash', 'lingo'), 60 * 5);
        $response['redirect'] = $_POST['redirect'];
    } else {
        $response['message'] = __('Topic with given id is not exist', 'lingo');
        $response['status'] = 'error';
    }
    
    echo wp_json_encode($response);

    wp_die();
}

/**
 * Adds subscribtion
 */
function lingo_subscription_save() {
    
    $response = new stdClass();
    $response->status = 200;
    $response->message = __("Subscription saved.", "lingo");
    
    $email = lingo_request("email");
    $topic_id = lingo_request("topic_id");
    $forum_id = lingo_request("forum_id");
    
    $key = 'lingo_subscription';
    
    if(!is_email($email)) {
        $response->status = -1;
        $response->message = __("Invalid e-mail address.", "lingo");
        echo wp_json_encode($response);
        wp_die();
    }
    
    if(isset($topic_id) && $topic_id > 0) {
        // subscribe for topic
        $subscriptions = get_post_meta($topic_id, $key, true);
        
        if(strpos($subscriptions, $email) !== false) {
            $response->status = -1;
            $response->message = __("You already subscribe this topic.", "lingo");
            echo wp_json_encode($response);
            wp_die();
        }
        
        if(strlen($subscriptions) > 0) {
            $subscriptions .= ", ";
        }
        $subscriptions .= $email;
        update_post_meta($topic_id, $key, $subscriptions); 
    }
    
    if(isset($forum_id) && $forum_id > 0) {
        // subscribe for single forum
    }
    
    // subscribe for whole forum

    
    echo wp_json_encode($response);
    wp_die();
}

/**
 * Removes subscribtion
 */
function lingo_subscription_remove() {
    
    $response = new stdClass();
    $response->status = 200;
    $response->message = __("Unsubscribed successfully.", "lingo");
    
    $email = lingo_request("email");
    $topic_id = lingo_request("topic_id");
    $forum_id = lingo_request("forum_id");
    
    $key = 'lingo_subscription';
    
    if(!is_email($email)) {
        $response->status = -1;
        $response->message = __("Invalid e-mail address.", "lingo");
        echo wp_json_encode($response);
        wp_die();
    }
    
    if(isset($topic_id) && $topic_id > 0) {
        // subscribe for topic
        $subscriptions = get_post_meta($topic_id, $key, true);
        
        if(strpos($subscriptions, $email) === false) {
            $response->status = -1;
            $response->message = __("There is no such e-mail.", "lingo");
            echo wp_json_encode($response);
            wp_die();
        }
        
        $subscriptions = str_replace($email, "", $subscriptions);
        $subscriptions = str_replace(", ", "", $subscriptions);
        update_post_meta($topic_id, $key, $subscriptions); 
    }
    
    if(isset($forum_id) && $forum_id > 0) {
        // subscribe for single forum
    }
    
    // subscribe for whole forum

    
    echo wp_json_encode($response);
    wp_die();
}