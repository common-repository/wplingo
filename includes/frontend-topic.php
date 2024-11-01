<?php
/**
 * Topic Frontend
 * 
 * Class handles functions to manage Lingo Topics in frontend
 *
 * @package     Lingo
 * @copyright   Copyright (c) 2016, Mark Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

/**
 * Replace template for lingo_topic post type
 * 
 * @global WP_Query $wp_query
 * @global WP_Post $post
 * @param string $single
 * @return string
 */
function lingo_single_topic_template($content) {
    global $wp_query;

    /* Checks for single template by post type */
    if (is_singular('lingo_topic') && in_the_loop()) {
        
        if(isset($_POST) && !empty($_POST)) {
            global $post;
            $topic_id = $post->ID;


            $scheme = Lingo::instance()->get("form_add_new_post");
            $form = new Lingo_Form( $scheme );
            $form->bind( stripslashes_deep( $_POST ) );
            $form->set_value("new_post_topic_id", $topic_id);
            $valid = $form->validate();

            if($valid) {

                $status = 'publish';
                if(lingo_config('config.need_approve') == 2) {
                    $status = 'pending';
                } elseif(lingo_config('config.need_approve') == 1 && !is_user_logged_in()) {
                    $status = 'pending';
                } 
                
                $msg = strip_tags(trim(lingo_request("new_post_message")), "<p><br><b><i><u><ul><li><img>");
                
                $valid = true;
                if(!is_string($msg) || !(strlen($msg) > 3)) {
                    $valid = false;
                }
                
                if(!is_numeric($topic_id) || !($topic_id > 0)) {
                    $valid = false;
                }

                if(!$valid) {
                    set_transient('lingo_flash_status', 'error', 60 * 5);
                    set_transient('lingo_flash_msg', __("There are some errors when trying to add new post.", "lingo"), 60 * 5);  
                } else {
                    $new_post = array(
                        //'post_title'    => wp_strip_all_tags( $title ),
                        'post_content'  => $msg,
                        'post_status'   => $status,
                        'post_author'   => get_current_user_id(),
                        'post_parent'   => $topic_id,
                        'post_type'     => 'lingo_post',
                    );

                    $post_id = wp_insert_post( $new_post );

                    if(!is_user_logged_in()) {
                        update_post_meta($post_id, "lingo_guest_nickname", sanitize_text_field(lingo_request("new_post_nickname")));
                    }

                    update_post_meta($topic_id, "lingo_last_post_date", time());
                
                    if($status != 'publish') {
                        set_transient('lingo_flash_status', 'info', 60 * 5);
                        set_transient('lingo_flash_msg', __("Your post is waiting for approval.", "lingo"), 60 * 5);
                    }
                
                    // Send e-mail to subscribers
                    $subscribers = explode(",", get_post_meta($topic_id, "lingo_subscription", true));
                    foreach($subscribers as $send_to) {
                        $template = lingo_email_template('subscribtion_alert');
                        $topic = get_post($topic_id);
                        
                        $values = array (
                            '{$topic_url}'     => get_permalink($topic_id),
                            '{$topic_title}'   => $topic->post_title,
                            '{$message}'       => $msg,
                        );
                        
                        if(!is_user_logged_in() || (is_user_logged_in() && $send_to != wp_get_current_user()->user_email)) {
                            wp_mail( $send_to, $template['title'], strtr($template['message'], $values));
                        }
                    }

                    // Redirect
                    $count = lingo_get_posts_count($topic_id);
                    $page = ceil($count / lingo_config('config.posts_on_page'));

                    if($page > 1) {
                        $redirect_url = get_permalink($topic_id) . "?pg=" . $page;
                        if(isset($post_id) && $post_id > 0) {
                            $redirect_url .= "#lingo-post-" . $post_id;
                        }

                        ob_start();
                        ?>
                        <script type="text/javascript">
                            jQuery(document).ready(function() {
                                window.location.href= '<?php echo $redirect_url; ?>';
                            });
                        </script>
                        <?php
                        $content = ob_get_clean();
                        //return $content;
                    }
                }
            }
        }
        
        if(file_exists(LINGO_PATH . 'templates/topic-single.php')) {
            ob_start();
            $post_id = get_the_ID();
            $post_content = $content;
            include apply_filters( "lingo_template_load", LINGO_PATH . 'templates/topic-single.php' );
            $content .= ob_get_clean();
        }
        
    
    }    
    
    return $content;
}

function lingo_single_topic_template_save_post() {
    
}


/**
 * Returns pagination & query of posts for given topic
 * 
 * @param int $topic_id
 * @return \WP_Query
 */
function lingo_get_topic_posts($topic_id) {
    
    $posts_per_page = lingo_config( 'config.posts_on_page' );
    
    $pbase = get_the_permalink();
    $paginate_base = apply_filters( 'lingo_list_pagination_base', $pbase . '%_%' );
    $paginate_format = stripos( $paginate_base, '?' ) ? '&pg=%#%' : '?pg=%#%';
    $paged = intval(lingo_request('pg', 1));

    $args = apply_filters( "lingo_post_query", array( 
        'post_type'         => 'lingo_post', 
        'post_status'       => 'publish',
        'posts_per_page'    => $posts_per_page, 
        'paged'             => $paged,
        'post_parent'       => $topic_id,
        /*'s'                 => $query,*/
        'orderby'           => array( 'menu_order' => 'DESC', 'date' => lingo_config( 'config.posts_order' ) )
    ));
    
    $result = array(
        'paginate_base' => $paginate_base,
        'paginate_format' => $paginate_format,
        'paged' => $paged,
        'posts' => new WP_Query( $args ),        
    );
    
    return $result;
}

/**
 * Creates token with IDs of all new posts from user last visit
 * 
 * @param string $user_login
 * @param WP_User $user
 * @return string
 */
function lingo_set_new_posts_token($user_login, $user) {
    
    $last_login = get_user_meta($user->ID, 'lingo_last_login', true);
    if($last_login == '') {
        update_user_meta( $user->ID, 'lingo_last_login', time() );
        return $user_login;
    }
    
    $post_cookie = array();
    if(get_transient("lingo_new_posts") !== null) {
        $post_cookie = json_decode(get_transient("lingo_new_posts"));
    } 
        
    // Get posts from last visit
    $args = apply_filters( "lingo_post_query", array(
        'posts_per_page' => -1,
        'post_type' => 'lingo_post',
        'date_query' => array(
            array(
                'after'     => date("Y-m-d H:i:s", $last_login),
                'inclusive' => true,
            ),
        ),
    ));
    $posts = new WP_Query( $args );
    
    $new_posts = array();
    if($posts->have_posts()) {
        while($posts->have_posts()) {
            $posts->the_post();
            
            $topic = wp_get_post_parent_id( get_the_ID() );
            $forum = wp_get_post_terms($topic, 'lingo_forum_group', array("fields" => "ids"));
            $new_posts[$forum[0]][$topic][] = get_the_ID();
        }
    }
    
    update_user_meta( $user->ID, 'lingo_last_login', time() );
    //setcookie( $cookie_name, json_encode($all_new_posts), 30 * DAYS_IN_SECONDS);
    set_transient( "lingo_new_posts", json_encode($new_posts), 30 * DAYS_IN_SECONDS);
    //set_transient( "lingo_new_topics", json_encode($all_new_topics), 30 * DAYS_IN_SECONDS);
    return $user_login;
}

/**
 * Function returns list of all topics for selected forum
 * 
 * @param int $forum_id
 * @return array
 */
function lingo_get_forum_topics($forum_id) {
    
    $posts_per_page = lingo_config( 'config.topics_on_page' );

    $pbase = get_the_permalink();
    $paginate_base = apply_filters( 'lingo_list_pagination_base', $pbase . '%_%' );
    $paginate_format = stripos( $paginate_base, '?' ) ? '&pg=%#%' : '?pg=%#%';
    $paged = intval(lingo_request('pg', 1));
    
    $args = apply_filters( "lingo_topic_query", array( 
        'post_type'         => 'lingo_topic', 
        'post_status'       => array('publish', 'closed'),
        'posts_per_page'    => $posts_per_page, 
        'paged'             => $paged,
        /*'s'                 => $query,*/
        'tax_query'         => array(
            array(
                'taxonomy'  => 'lingo_forum_group',
                'field'     => 'term_id',
                'terms'     => $forum_id,
            )
        ),
        /*'meta_query'        => array(
            'relation'      => 'OR',
            array(
                'key'       => 'lingo_last_post_date',
                'compare'   => 'EXISTS'
            ),
            array(
                'key'       => 'lingo_last_post_date',
                'compare'   => 'NOT EXISTS'
            )
        ),*/
        'meta_key'  => 'lingo_last_post_date',
        'orderby'   => 'meta_value_num',
        'order'     => 'DESC'
    ));
    
    $result = array(
        'paginate_base' => $paginate_base,
        'paginate_format' => $paginate_format,
        'paged' => $paged,
        'posts' => new WP_Query( $args ),        
    );
    
    return $result;
}

/**
 * Displays page for sigle tag
 * @param string $content
 * @return string
 */
function lingo_single_topic_tag_page($content) {
    
    if(is_tax("lingo_topic_tag")) {
        if(file_exists(LINGO_PATH . 'templates/topic-single.php')) {
            ob_start();
            include apply_filters( "lingo_template_load", LINGO_PATH . 'templates/topic-item.php' );
            $content = ob_get_clean();
        }
    }
    
    return $content;
}