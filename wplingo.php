<?php
/*
 * Plugin Name: WPLingo
 * Plugin URI: http://wplingo.com
 * Description: The lightweight WordPress forum plugin. Easy to configure, fun to use! Animate community, provide support, have interesting discussions!
 * Author: Mark Winiarski
 * Author URI: http://wplingo.com
 * Text Domain: lingo
 * Version: 1.1.2
 * 
 * WPLingo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPLingo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WPLingo. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package WPLingo
 * @category Core
 * @author Mark Winiarski 
 * @version 1.1.2
 */
 
 if( !defined("LINGO_FILE") ) {
    define( "LINGO_FILE", __FILE__ );
    define( "LINGO_PATH", plugin_dir_path( LINGO_FILE ) );
    define( "LINGO_URL", plugins_url() . "/" . basename(LINGO_PATH) );
}

// define global $lingo_config variable
$lingo_config = null;
// define global $lingo_email_templates variable
$lingo_email_templates = null;

// define global $lingo_namespace variable
$lingo_namespace = array( 
    
    'config' => array(
        'option_name' => 'lingo_config',
        'default' => array(
            'module'                => array(),
            'license'               => array(),
            'topics_on_page'        => 20,
            'posts_on_page'         => 20,
            'only_signed_can_see'   => 0,
            'only_signed_can_post'  => 0,
            'display_breadcrumbs'   => 0,
            'display_search'        => 0,
            'use_tags'              => 0,
        )
    ),
    'email_templates' => array(
        'subscribtion_alert' => array(
            'id'            => 1,
            'title'         => 'Subscribtion Alert',
            'name'          => 'Subscribtion Alert',
            'description'   => 'E-mail sent to topic subscribers when new message ocures',
            'message'       => 'Greetings! <br/> There is new post in topic, that you subscribing: {$topic_url} <br/><br/> Best regards, <br/> Lingo Support'
        )
    )
);

/**
 * Main Lingo Init Function
 * 
 * Registers: custom post types, additional post statuses, taxonomies, image sizes
 * and Lingo scripts.
 * 
 * @global WP_Rewrite $wp_rewrite
 * @since 0.1
 * @return void
 */
function lingo_init() {
    
    wp_register_style( 'lingo-icons', LINGO_URL . '/assets/css/font-awesome.min.css' );
    wp_enqueue_style( 'lingo-icons' );
    
    // LANGUAGE TRANSLATION
    load_plugin_textdomain("lingo", false, dirname(plugin_basename(__FILE__))."/languages/");
    
    // POST TYPES
    $labels = array(
        'name'               => _x( 'Topic', 'post type general name', 'lingo' ),
        'singular_name'      => _x( 'Topic', 'post type singular name', 'lingo' ),
        'add_new'            => _x( 'Add New', 'classified', 'lingo' ),
        'add_new_item'       => __( 'Add New Topic', 'lingo' ),
        'edit_item'          => __( 'Edit Topic', 'lingo' ),
        'new_item'           => __( 'New Topic', 'lingo' ),
        'all_items'          => __( 'All Topics', 'lingo' ),
        'view_item'          => __( 'View Topic', 'lingo' ),
        'search_items'       => __( 'Search Topic', 'lingo' ),
        'not_found'          => __( 'No Topics found', 'lingo' ),
        'not_found_in_trash' => __( 'No Topics found in the Trash', 'lingo' ), 
        'parent_item_colon'  => '',
        'menu_name'          => __( 'Topics', 'lingo' )
    );
      
    $args = array(
        'labels'              => $labels,
        'description'         => 'Topic with post',
        'public'              => true,
        'menu_icon'           => 'dashicons-format-chat',
        'menu_position'       => 5,
        'supports'            => array( 'title', 'editor', 'author', 'excerpt'),
        'taxonomies'          => array( ),
        'has_archive'         => true,
        'capability_type'     => array('lingo_moderator','lingo_moderators'),
        'map_meta_cap'        => true,
    );
    
    register_post_type( 'lingo_topic', apply_filters( 'lingo_post_type_topic', $args, 'lingo_topic') ); 
    
    $labels = array(
        'name'               => _x( 'Post', 'post type general name', 'lingo' ),
        'singular_name'      => _x( 'Post', 'post type singular name', 'lingo' ),
        'add_new'            => _x( 'Add New', 'classified', 'lingo' ),
        'add_new_item'       => __( 'Add New Post', 'lingo' ),
        'edit_item'          => __( 'Edit Post', 'lingo' ),
        'new_item'           => __( 'New Post', 'lingo' ),
        'all_items'          => __( 'All Posts', 'lingo' ),
        'view_item'          => __( 'View Post', 'lingo' ),
        'search_items'       => __( 'Search Post', 'lingo' ),
        'not_found'          => __( 'No Posts found', 'lingo' ),
        'not_found_in_trash' => __( 'No Posts found in the Trash', 'lingo' ), 
        'parent_item_colon'  => '',
        //'menu_name'          => __( 'WPLingo: Posts', 'lingo' )
    );
      
    $args = array(
        'labels'                => $labels,
        'description'           => 'Singular post in topic',
        'public'                => true,
        //'menu_icon'     => 'dashicons-format-chat',
        'menu_position'         => 5,
        'hierarchical'          => true,
        'supports'              => array( 'editor' ),
        'taxonomies'            => array( ),
        'has_archive'           => true,
        'show_in_menu'          => false,
        'capability_type'       => array('lingo_moderator','lingo_moderators'),
        'map_meta_cap'          => true,
        
        
    );
    
    register_post_type( 'lingo_post', apply_filters( 'lingo_post_type_post', $args, 'lingo_post') ); 
    
    // TAXONOMIES
    $args = array(
        'hierarchical'      => true,
        'query_var'         => true,
        'label'             => "Forum",
        'rewrite'           => array('slug' => 'lingo-forum-group')
    );
    
    register_taxonomy( 'lingo_forum_group', 'lingo_topic', apply_filters('lingo_register_taxonomy', $args, 'lingo_forum_group') );
    
    $args = array(
        'hierarchical'      => false,
        'query_var'         => true,
        'label'             => "Topic Tags",
        'rewrite'           => array('slug' => 'lingo-topic-tag')
    );
    
    register_taxonomy( 'lingo_topic_tag', 'lingo_topic', apply_filters('lingo_register_taxonomy', $args, 'lingo_topic_tag') );
    

    // STATUSES
    register_post_status( 'closed', array(
        'label'                     => _x( 'Closed', 'lingo' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Closed <span class="count">(%s)</span>', 'Closed <span class="count">(%s)</span>' ),
    ));
    
    // ROLES
    add_role(
        'lingo_forum_moderator',
        __( 'Forum Moderator' ),
        apply_filters("lingo_moderator_cap", array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'publish_posts' => false,
            'upload_files' => true,
        ))   
    );
    
    $roles = array('lingo_forum_moderator','editor','administrator');
 
    foreach($roles as $the_role) { 

        $role = get_role($the_role);

        $role->add_cap( 'read' );
        $role->add_cap( 'read_lingo_moderator');
        $role->add_cap( 'read_private_lingo_moderators' );
        $role->add_cap( 'edit_lingo_moderator' );
        $role->add_cap( 'edit_lingo_moderators' );
        $role->add_cap( 'edit_others_lingo_moderators' );
        $role->add_cap( 'edit_published_lingo_moderators' );
        $role->add_cap( 'publish_lingo_moderators' );
        $role->add_cap( 'delete_others_lingo_moderators' );
        $role->add_cap( 'delete_private_lingo_moderators' );
        $role->add_cap( 'delete_published_lingo_moderators' );
    }
 
    
    // INCLUDES
    include_once LINGO_PATH . 'includes/functions.php';
    include_once LINGO_PATH . 'includes/enums.php';
    include_once LINGO_PATH . 'includes/class-lingo.php';
    include_once LINGO_PATH . 'includes/class-flash.php';
    include_once LINGO_PATH . 'includes/class-form.php';
    include_once LINGO_PATH . 'includes/class-html.php';
    include_once LINGO_PATH . 'includes/defaults.php';
    include_once LINGO_PATH . 'includes/forms.php';
    
    
    add_filter('pre_get_posts', 'lingo_hide_posts_media');
    add_filter( 'posts_where', 'lingo_hide_attachments_wpquery_where' );
    
    // MODULES
    $module = lingo_config( 'config.module' );

    foreach((array)$module as $mod => $status) {
        if(!is_file(LINGO_PATH . "addons/$mod/$mod.php")) {
            continue;
        }
        if($status > 0) {
            include_once LINGO_PATH . "addons/$mod/$mod.php";
        }
        if($status == 0.5) {
            add_action("init", "lingo_install_modules", 1000);
        }
    }
    
    
    // FINALIZE
    if( get_option( "lingo_delayed_install" ) == "yes" ) {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
        
        delete_option( "lingo_delayed_install" );
    }
    
    do_action("lingo_core_initiated");
}


/**
 * Frontend Lingo Init Function
 * 
 * Registers frontend: scripts, styles and shortcodes
 * 
 * @since 0.1
 * @return void
 */
function lingo_init_frontend() {
    
    // Data picker
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-datepicker');
    
    // JS & CSS
    wp_register_style( 'lingo-frontend', LINGO_URL . '/assets/css/lingo-frontend.css');
    wp_register_script('lingo-frontend', LINGO_URL . '/assets/js/lingo-frontend.js', array( 'jquery' ) );
    
    wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
    wp_enqueue_style( 'lingo-frontend' );
    wp_enqueue_style( 'lingo-icons' );

    // INCLUDES
    include_once LINGO_PATH . 'includes/shortcodes.php';
    include_once LINGO_PATH . 'includes/frontend-topic.php';
    
    // FILTERS & ACTIONS
    add_filter('the_content', 'lingo_single_topic_template');
    add_filter('the_content', 'lingo_single_topic_tag_page');
    add_action('wp_loaded', 'lingo_single_topic_template_save_post');
    add_filter('wp_login', 'lingo_set_new_posts_token', 10, 2);
    //add_filter('taxonomy_template', 'lingo_taxonomy_template');
    
    wp_localize_script( 'lingo-frontend', 'lingo_lang', array(
        "ajaxurl" => admin_url('admin-ajax.php')
    ) );
    
    wp_enqueue_script( 'lingo-frontend' );
}

/**
 * Admin Lingo Init Function
 * 
 * Registers admin: ajax actions, scripts, styles and meta boxes when adding new advert.
 * 
 * @since 0.1
 * @return void
 */
function lingo_init_admin() {
    
    // JS & CSS
    wp_register_script('lingo-admin', LINGO_URL . '/assets/js/lingo-admin.js', array( 'jquery' ), false, true);
    wp_register_style('lingo-admin', LINGO_URL . '/assets/css/lingo-admin.css');
    
    wp_enqueue_script( 'lingo-admin' );
    wp_enqueue_style( 'lingo-admin' );
    
    
    // INCLUDES
    include_once LINGO_PATH . 'includes/admin-topic.php';
    include_once LINGO_PATH . 'includes/admin-pages.php';
    include_once LINGO_PATH . 'includes/ajax.php';
    
    // POST TYPE SUPPORT
    remove_post_type_support( 'lingo_topic', 'editor' );
    
    // FILTERS & ACTIONS
    add_action('admin_menu', 'lingo_topic_remove_metabox');
    add_action('add_meta_boxes', 'lingo_topic_add_metabox' );

    add_action('admin_footer-post.php', 'lingo_append_post_status_list');

    //add_action('post_submitbox_misc_actions', 'lingo_topic_metabox_forum_group' );
    add_action('save_post', 'lingo_topic_metabox_forum_group_save', 1, 2);
    //add_action('save_post', 'lingo_topic_metabox_forum_group_add_new', 1, 2);
    
    //add_filter( 'redirect_post_location', 'lingo_redirect_after_post_save', 10, 2);
    add_action( 'admin_notices', 'lingo_post_saved_notice' );
    add_filter( 'admin_head', 'lingo_add_back_button' );

    add_filter('manage_edit-lingo_topic_columns', 'lingo_edit_topic_columns_header');
    add_action('manage_lingo_topic_posts_custom_column', 'lingo_edit_topic_columns', 10, 2);
    
    add_filter('manage_edit-lingo_forum_group_columns', 'lingo_add_forum_group_columns_header');
    add_filter('manage_lingo_forum_group_custom_column', 'lingo_edit_forum_group_columns',10,3);
  
    // Adds order field to forum taxonomy
    add_action( 'lingo_forum_group_add_form_fields', 'lingo_add_forum_order', 10, 2);
    add_action( 'lingo_forum_group_edit_form_fields', 'lingo_edit_forum_order', 10, 2 );
    add_action( 'edited_lingo_forum_group', 'lingo_save_forum_order', 10, 2 );  
    add_action( 'create_lingo_forum_group', 'lingo_save_forum_order', 10, 2 );
    
    // ADMIN-PAGES
    add_action( 'admin_menu', 'lingo_add_options_link');
}

/**
 * Registers Lingo Widgets
 * 
 * This function is executed by 'widgets_init' action.
 * 
 * @since 0.3
 * @return void
 */
function lingo_widgets_init() {

    //include_once LINGO_PATH . '/includes/class-widget-categories.php';
    
    //register_widget( "Lingo_Widget_Categories" );
}

/**
 * Install Modules 
 * 
 * Run module installation functions (if any) on modules that were "just" activated. 
 * This function is executed in "init" filter with low priority so all the modules 
 * are initiated before it is run.
 * 
 * @since 0.2
 * @return void
 */
function lingo_install_modules() {
    $module = lingo_config( 'config.module' );
    $install = array();
    
    foreach((array)$module as $mod => $status) {
        if(!is_file(LINGO_PATH . "addons/$mod/$mod.php")) {
            continue;
        }
        if($status > 0) {
            include_once LINGO_PATH . "addons/$mod/$mod.php";
        }
        if($status == 0.5) {
            add_action("init", "lingo_install_modules");
            Lingo_Flash::instance()->add_info( __( "Module activated successfully.", "lingo" ) );
            
            $module[$mod] = 1;
            lingo_config_set( 'config.module', $module );
            lingo_config_save( 'config' );
            
            do_action("lingo_install_module_$mod");
            
        }
    }
}


/**
 * Activation Filter
 * 
 * This function is run when WPLingo is activated.
 * 
 * @since 0.1
 * @return void
 */
function lingo_activate() {
    
    // on activation ALWAYS do this
    global $wp_rewrite;

    add_option( "lingo_delayed_install", "yes");
    
    // on FIRST activation do this.
    if(get_option("lingo_first_run", "1") == "0") {
        return;
    }

    // make sure this will not be ran again.
    add_option("lingo_first_run", "0", '', "yes");
    
    // register post type and taxonomy in order to allow default data insertion.
    register_post_type( 'lingo_topic' ); 
    register_taxonomy( 'lingo_forum_group', 'lingo_topic' );
    register_taxonomy( 'lingo_topic_tag', 'lingo_topic' );
    
    //$lingo_config["config"] = array();
    
    // INSERT POSTS
    $hid = wp_insert_post(array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => 'Lingo Forum',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_content' => "[lingo_forum]"
    ));
    //$lingo_config["config"]["forum_id"] = $hid;
    
    $valid = true;
    if(!is_numeric($hid) || !($hid > 0)) {
        $valid = false;
    }
    
    if($valid) {    
        $uid = wp_insert_post(array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => 'Lingo User Page',
            'post_parent' => $hid,
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_content' => "[lingo_user_page]"
        ));

        $sid = wp_insert_post(array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'post_title' => 'Lingo Forum Search',
            'post_parent' => $hid,
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_content' => "[lingo_search]"
        ));
    }

    
    $parent = wp_insert_term( __('Test Category', 'lingo'), 'lingo_forum_group', $args = array() );
    wp_insert_term( __('First Forum', 'lingo'), 'lingo_forum_group', $args = array('parent' => $parent['term_id']) );
    wp_insert_term( __('Second Forum', 'lingo'), 'lingo_forum_group', $args = array('parent' => $parent['term_id']) );

}

// Register activation function
register_activation_hook( __FILE__, 'lingo_activate' );

// Run Lingo
add_action( 'init', 'lingo_init', 5 );
add_action( 'widgets_init', 'lingo_widgets_init' );

if(is_admin() ) {
    // Run Lingo admin only actions
    add_action( 'init', 'lingo_init_admin' );
} else {
    // Run Lingo frontend only actions
    add_action( 'init', 'lingo_init_frontend' );
}


