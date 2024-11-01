<?php
/**
 * List of registered shortcodes
 * 
 * @package     Lingo
 * @copyright   Copyright (c) 2016, Mark Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Register shortcodes
add_shortcode('lingo_forum', 'shortcode_lingo_forum');
add_shortcode('lingo_user_page', 'shortcode_lingo_user_page');
add_shortcode('lingo_search', 'shortcode_lingo_search');


// Shortcode functions

/**
 * Generates HTML for [lingo_forum] shortcode
 * 
 * @param array $atts Shorcode attributes
 * @since 0.1
 * @return string Fully formatted HTML for forum
 */
function shortcode_lingo_forum( $atts ) {

    /*extract(shortcode_atts(array(
        'name' => 'default',
    ), $atts)); */

    $forum_id = intval(lingo_request("forum"));
    
    if(is_numeric($forum_id) && $forum_id > 0) {
        
        $meta = get_option( "taxonomy_$forum_id");
        $forum_status = $meta['lingo_forum_status'];

        $action = sanitize_text_field(lingo_request("action"));
        
        if($action == "add") {
            $scheme = Lingo::instance()->get("form_add_new_topic");
            if(lingo_config('config.use_tags')) {
                $scheme = Lingo::instance()->get("form_add_new_topic_with_tags");
            }
            
            $form = new Lingo_Form( $scheme );
            $btn_label = __("Publish New Topic", "lingo");
            
            if(isset($_POST) && !empty($_POST)) {
                $form->bind( stripslashes_deep( $_POST ) );
                $valid = $form->validate();
            
                if($valid) {
                    $title = sanitize_text_field(lingo_request("new_topic_title"));
                    $description = sanitize_text_field(lingo_request("new_topic_description"));
                    //$message = sanitize_text_field(lingo_request("new_topic_message"));
                    $message = strip_tags(trim(lingo_request("new_topic_message")), "<p><br><b><i><u><ul><li>");
                    $tags = sanitize_text_field(lingo_request("new_topic_tags"));
                    
                    $status = 'publish';
                    if(lingo_config('config.need_approve') == 2) {
                        $status = 'pending';
                    } elseif(lingo_config('config.need_approve') == 1 && !is_user_logged_in()) {
                        $status = 'pending';
                    } 
                    
                    $valid = true;
                    if(!is_string($title) || !(strlen($title) > 3)) {
                        $valid = false;
                    }
                    
                    if(!is_numeric($forum_id) || !($forum_id > 0)) {
                        $valid = false;
                    }
                    
                    // Add New Topic
                    if($valid) {
                        $new_topic = array(
                            'post_title'    => wp_strip_all_tags( $title ),
                            'post_status'   => $status,
                            'post_author'   => get_current_user_id(),
                            'post_type'     => 'lingo_topic',
                            'tax_input'    => array(
                                'lingo_forum_group'     => array($forum_id),
                                'lingo_topic_tag'       => explode(",", $tags),
                            ),
                        );

                        $topic_id = wp_insert_post( $new_topic );
                    
                        wp_set_post_terms( $topic_id, array($forum_id), "lingo_forum_group");
                        wp_set_post_terms( $topic_id, explode(",", $tags), "lingo_topic_tag");
                    }
                    
                    if(!is_string($message) || !(strlen($message) > 3)) {
                        $valid = false;
                    }
                    
                    if(!is_numeric($topic_id) || !($topic_id > 0)) {
                        $valid = false;
                    }
                    
                    if($forum_status == 1) {
                        $valid = false;
                    }
                         
                    // Add New Post
                    if($valid) {
                        $new_post = array(
                            //'post_title'    => wp_strip_all_tags( $title ),
                            'post_content'  => $message,
                            'post_status'   => 'publish',
                            'post_author'   => get_current_user_id(),
                            'post_parent'   => $topic_id,
                            'post_type'     => 'lingo_post',
                        );

                        wp_insert_post( $new_post );

                        update_post_meta($topic_id, "lingo_last_post_date", time());
                    }
                    
                    if(!$valid) {
                        set_transient('lingo_flash_status', 'error', 60 * 5);
                        set_transient('lingo_flash_msg', __('There was an error when creating new topic', 'lingo'), 60 * 5);
                    }
                    
                    // Redirect
                    if($status == 'publish') {
                        $redirect_url = get_permalink($topic_id);
                    } else {
                        set_transient('lingo_flash_status', 'ok', 60 * 5);
                        set_transient('lingo_flash_msg', __('Your topic waiting for approval', 'lingo'), 60 * 5);
                        $redirect_url = get_page_link(lingo_config('config.forum_id')) . "?forum=" . $forum_id;
                    }
                }
            }
        } elseif($action == "edit") {
            $topic = get_post($_GET['topic_id']); 
            
            $tags = array();
            foreach(wp_get_post_terms($topic->ID, 'lingo_topic_tag') as $tag) {
                $tags[] = $tag->name;
            }
            $curData = array("topic_title" => $topic->post_title,
                             "topic_tags" => join(",", $tags)
            );
            
            
            
            $scheme = Lingo::instance()->get("form_edit_topic");
            if(lingo_config('config.use_tags')) {
                $scheme = Lingo::instance()->get("form_edit_topic_with_tags");
            }
            
            $form = new Lingo_Form( $scheme );
            $form->bind( stripslashes_deep( $curData) );
            
            $btn_label = __("Save Topic", "lingo");
            
            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $form->bind( stripslashes_deep( $_POST ) );
                $valid = $form->validate();
                
                if($valid) {
                    $title = sanitize_text_field(lingo_request("topic_title"));
                    $tags = sanitize_text_field(lingo_request("topic_tags"));
                    
                    // Add New Topic
                    $new_topic = array(
                        'ID'            => $topic->ID,
                        'post_title'    => wp_strip_all_tags( $title ),
                        'tax_input'    => array(
                            'lingo_topic_tag' => explode(",", $tags),
                        ),
                    );

                    $topic_id = wp_update_post( $new_topic );
                    if($topic_id > 0) {
                        set_transient('lingo_flash_status', 'ok', 60 * 5);
                        set_transient('lingo_flash_msg', __('Topic was saved', 'lingo'), 60 * 5);
                        $redirect_url = get_permalink($topic_id);
                    } else {
                        set_transient('lingo_flash_status', 'error', 60 * 5);
                        set_transient('lingo_flash_msg', __('Error when saving', 'lingo'), 60 * 5);
                        $redirect_url = get_permalink($_GET["topic_id"]);
                    }
                }
            }
 
        } else {
            $topics = lingo_get_forum_topics($forum_id);
        }
        
        ob_start();
        if($action == "add" || $action == "edit") {
            include apply_filters( "lingo_template_load", LINGO_PATH . 'templates/form.php' );
        } else {
            include apply_filters( "lingo_template_load", LINGO_PATH . 'templates/forum-single.php' );
        }
        return ob_get_clean();
        
    } else {
        $forums = lingo_get_all_forums();
        usort($forums, 'lingo_forum_sort');
        
        foreach($forums as $forum) {
            usort($forum->childs, 'lingo_forum_sort');
        }

        // lingo/templates/forum.php
        ob_start();
            include apply_filters( "lingo_template_load", LINGO_PATH . 'templates/forum.php' );
        return ob_get_clean();
    }
}

/**
 * Generates HTML for [lingo_user_page] shortcode
 * 
 * @param array $atts Shorcode attributes
 * @since 0.1
 * @return string Fully formatted HTML for Lingo user page
 */
function shortcode_lingo_user_page($atts) {
    
    $user = get_user_by('id', intval(lingo_request('id')));
    $user_info = get_userdata($user->ID);
    $pg = intval(lingo_request('pg'));
    
    if(!is_numeric($user->ID)) {
        $user = get_user_by('id', wp_get_current_user()->ID);
    }
    
    $args = apply_filters( "lingo_topic_query", array( 
        'post_type'         => 'lingo_topic', 
        'post_status'       => array('publish', 'closed'),
        'posts_per_page'    => -1, 
        'author'            => $user->ID,
    ));

    $topics = new WP_Query( $args );
    
    $args = apply_filters( "lingo_post_query", array( 
        'post_type'         => 'lingo_post', 
        'post_status'       => array('publish'),
        'posts_per_page'    => lingo_config( 'config.posts_on_page' ), 
        'author'            => $user->ID,
        'paged'             => $pg,
    ));

    $posts = new WP_Query( $args );
    
    $pbase = get_page_link(lingo_config('config.user_page_id'));
    $paginate_base = apply_filters( 'lingo_list_pagination_base', $pbase . '%_%' );
    $paginate_format = stripos( $paginate_base, '?' ) ? '&pg=%#%' : '?pg=%#%';
    
    $result = array(
        'paginate_base' => $paginate_base,
        'paginate_format' => $paginate_format . "#lingo_post_section_header",
        'paged' => $pg,
        'posts' => $posts,        
    );
    
    ob_start();
        include apply_filters( "lingo_template_load", LINGO_PATH . 'templates/user-page.php' );
    return ob_get_clean();
}

/**
 * Generates HTML for [lingo_search] shortcode
 * 
 * @param array $atts Shorcode attributes
 * @since 0.1
 * @return string Fully formatted HTML for Lingo search page
 */
function shortcode_lingo_search($atts) {
    
    /*extract(shortcode_atts(array(
        'name' => 'default',
    ), $atts)); */
    
    if(lingo_config( 'config.only_signed_can_see' ) && !is_user_logged_in()) {
        lingo_display_error(__('Only signed users can see the forum.', 'lingo'));
        return;
    }
    
    $query = sanitize_text_field(lingo_request("query"));
    $topic = sanitize_text_field(lingo_request("topic"));
    $date_from = sanitize_text_field(lingo_request("date_from"), null);
    $date_to = sanitize_text_field(lingo_request("date_to"), null);
    $show_unread = sanitize_text_field(lingo_request("show_unread"));
    $forum = lingo_request("forum");
    $pg = intval(lingo_request("pg", 1));
    
    $allForums = lingo_get_all_forums();
    
    if(!is_user_logged_in()) {
        $show_unread = null;
    }
    
    $ids = array();
    if(isset($show_unread) && $show_unread == 1) {
        $posts_to_read = json_decode(get_transient("lingo_new_posts"), true);
        foreach($posts_to_read as $ptr_forum => $ptr_topics) {
            foreach($ptr_topics as $ptr_topic => $ptr_posts) {
                foreach($ptr_posts as $post_id) {
                    $ids[] = $post_id;
                }
            }
        }
    } 
    
    // Get Topics
    $args = apply_filters( "lingo_topic_query", array( 
        'post_type'         => 'lingo_topic', 
        'post_status'       => array('publish', 'closed'),
        'posts_per_page'    => -1, 
        's'                 => $topic,
    ));
    
    if(count($forum) > 0) {
        $args['tax_query'] = array(
            array (
                'taxonomy'  => 'lingo_forum_group',
                'field'     => 'term_id',
                'terms'     => $forum,
            ),
        );
    } else {
        $forum = $allForums;
    }
    
    $forum = (array)$forum;
    
    $topics = new WP_Query( $args );
    $allowedTopics = array();
    while ( $topics->have_posts() ) {
        $topics->the_post();
        $allowedTopics[] = get_the_ID();
    }
    
    if(!(count($allowedTopics) > 0)) {
        $allowedTopics = array(0);
    }
    
    if((isset($date_from) && !empty($date_from)) || (isset($date_to) && !empty($date_to))) {
        $date = array('inclusive' => true);
    }
    
    if(isset($date_from) && !empty($date_from)){
        $s_date_from = explode("-", $date_from);
        $date["after"] = array(
            'year' => $s_date_from[0],
            'month' => $s_date_from[1],
            'day' => $s_date_from[2],
        );
    }
    
    if(isset($date_to) && !empty($date_to)){
        $s_date_to = explode("-", $date_to);
        $date["before"] = array(
            'year' => $s_date_to[0],
            'month' => $s_date_to[1],
            'day' => $s_date_to[2],
        );
    }
    
    
    
    // Get Posts
    $args = apply_filters( "lingo_post_query", array( 
        'post_type'         => 'lingo_post', 
        'post_status'       => array('publish'),
        'posts_per_page'    => lingo_config( 'config.posts_on_page' ), 
        's'                 => $query,
        'post_parent__in'   => $allowedTopics,
        'paged'             => $pg,
    ));
    
    if(isset($date) && !empty($date)) {
        $args['date_query'] = $date;
    }
    
    if(count($ids) > 0) {
        $args['post__in'] = $ids;
    }
    
    $posts = new WP_Query( $args );
    
    // Pagination
    $pbase = get_page_link(lingo_config('config.search_page_id'));
    $paginate_base = apply_filters( 'lingo_list_pagination_base', $pbase . '%_%' );
    $paginate_format = stripos( $paginate_base, '?' ) ? '&pg=%#%' : '?pg=%#%';
    
    $result = array(
        'paginate_base' => $paginate_base,
        'paginate_format' => $paginate_format . "#lingo_post_section_header",
        'paged' => $pg,
        'posts' => $posts,        
    );
    
    ob_start();
        include apply_filters( "lingo_template_load", LINGO_PATH . 'templates/search.php' );
    return ob_get_clean();
}