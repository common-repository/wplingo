<?php
/**
 * Core Admin Pages
 * 
 * This file contains function to handle default/core config logic in wp-admin 
 * and config form.
 *
 * @package     Lingo
 * @subpackage  Core
 * @copyright   Copyright (c) 2016, Marek Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

/**
 * Renders default/core config form.
 * 
 * The page is rendered in wp-admin / Classifieds / Options / Core 
 * 
 * @since 0.1
 * @return void
 */
function adext_core_page_options() {
    
    $page_title = __("Core Options", "lingo");
    $button_text = __("Update Options", "lingo");
    
    wp_enqueue_style( 'lingo-admin' );
    $flash = Lingo_Flash::instance();
    
    $scheme = Lingo::instance()->get("form_core_config");
    $form = new Lingo_Form( $scheme );
    
    if(isset($_POST) && !empty($_POST)) {
        $form->bind( stripslashes_deep( $_POST ) );
        $valid = $form->validate();

        if($valid) {
            
            $data = $form->get_values();
            $data["module"] = lingo_config( 'config.module' );
            
            update_option("lingo_config", $data );
            $flash->add_info( __("Settings updated.", "lingo") );
        } else {
            $flash->add_error( __("There are errors in your form.", "lingo") );
        }
    } else {
        $form->bind( lingo_config("config.ALL") );
    }
    
    include LINGO_PATH . 'addons/core/admin/options.php';
}


/**
 * Register <select> input with list of Pages as options.
 * 
 * This is basically a wrapper for wp_dropdown_pages() WordPress function.
 * 
 * @see wp_dropdown_pages()
 * 
 * @param array $field Fields settings
 * @since 0.3
 * @return void
 */
function lingo_dropdown_pages( $field ) {
    
    if(isset($field["value"])) {
        $value = $field["value"];
    } else {
        $value = null;
    }
    
    $args = array(
        'selected' => $value, 
        'echo' => 1,
	'name' => $field["name"], 
        'id' => $field["name"],
	'show_option_none' => ' ',
        'option_none_value' => 0
    );
    
    wp_dropdown_pages( $args );
}

// Register <select> with list of pages 
/** @see lingo_dropdown_pages() */
lingo_form_add_field("lingo_dropdown_pages", array(
    "renderer" => "lingo_dropdown_pages",
    "callback_bind" => "lingo_bind_single",
    "callback_save" => "lingo_save_single",
));

// Core options config form
Lingo::instance()->set("form_core_config", array(
    "name" => "",
    "action" => "",
    "field" => array(
        array(
            "name" => "_pages_and_urls",
            "type" => "lingo_field_header",
            "order" => 10,
            "label" => __( 'Pages & URLs', 'lingo' ),
            "title" => __( 'Pages & URLs', 'lingo' )
        ),
        array(
            "name" => "forum_id",
            "type" => "lingo_dropdown_pages",
            "order" => 10,
            "label" => __("Default Forum Page", "lingo"),
            "hint" => __("Select page on which the main [lingo_list] shortcode is being used.", "lingo")
        ),
        /*array(
            "name" => "add_post_id",
            "type" => "lingo_dropdown_pages",
            "order" => 10,
            "label" => __("Default Add Post Page", "lingo"),
            "hint" => __("Select page on which the add post [lingo_add_post] shortcode is being used.", "lingo")
        ),*/
        array(
            "name" => "user_page_id",
            "type" => "lingo_dropdown_pages",
            "order" => 10,
            "label" => __("Default User Page", "lingo"),
            "hint" => __("Select page on which the user page [lingo_user_page] shortcode is being used.", "lingo")
        ),
        array(
            "name" => "search_page_id",
            "type" => "lingo_dropdown_pages",
            "order" => 10,
            "label" => __("Default Search Page", "lingo"),
            "hint" => __("Select page on which the user page [lingo_search] shortcode is being used.", "lingo")
        ),
        array(
            "name" => "_common_settings",
            "type" => "lingo_field_header",
            "order" => 10,
            "label" => __( 'Common Settings', 'lingo' ),
            "title" => __( 'Common Settings', 'lingo' )
        ),
        array(
            "name" => "topics_on_page",
            "type" => "lingo_field_text",
            "order" => 10,
            "label" => __("Topics on Page", "lingo"),
            "hint" => __("Number of topics on a single page.", "lingo"),
            "validator" => array(
                array("name"=>"is_required"),
                array("name"=>"is_integer")
            )
        ),
        array(
            "name" => "posts_on_page",
            "type" => "lingo_field_text",
            "order" => 10,
            "label" => __("Posts on Page", "lingo"),
            "hint" => __("Number of posts on a single topic page.", "lingo"),
            "validator" => array(
                array("name"=>"is_required"),
                array("name"=>"is_integer")
            )
        ), 
        array(
            "name" => "posts_order",
            "type" => "lingo_field_select",
            "order" => 10,
            "label" => __("Posts order in topic", "lingo"),
            "hint"  => __("Ascending order is defualt order for clasic forum. Descending order is better option for answers forum.", "lingo"),
            "options" => array(
                array( "value"=> "ASC", "text"=> __( "Ascending", "lingo" ) ),
                array( "value"=> "DESC", "text"=> __( "Descending", "lingo" ) ),
            )
        ), 
        array(
            "name" => "only_signed_can_see",
            "type" => "lingo_field_checkbox",
            "order" => 10,
            "label" => __("Only Signed Users Can Read", "lingo"),
            "max_choices" => 1,
            "options" => array(
                array( "value"=> "1", "text"=> __( "If checked - only signed can see forum topics and posts", "lingo" ) ),
            )
        ),
        array(
            "name" => "only_signed_can_post",
            "type" => "lingo_field_checkbox",
            "order" => 10,
            "label" => __("Only Signed Users Can Post", "lingo"),
            "max_choices" => 1,
            "options" => array(
                array( "value"=> "1", "text"=> __( "If checked - only signed users can add new posts and topics", "lingo" ) ),
            )
        ),
        array(
            "name" => "need_approve",
            "type" => "lingo_field_select",
            "order" => 10,
            "label" => __("New Topics Need Approval", "lingo"),
            "options" => array(
                array( "value"=> "0", "text"=> __( "None", "lingo" ) ),
                array( "value"=> "1", "text"=> __( "Only for unsigned users", "lingo" ) ),
                array( "value"=> "2", "text"=> __( "All", "lingo" ) ),
            )
        ), 
        array(
            "name" => "display_breadcrumbs",
            "type" => "lingo_field_checkbox",
            "order" => 10,
            "label" => __("Display breadcrumbs", "lingo"),
            "max_choices" => 1,
            "options" => array(
                array( "value"=> "1", "text"=> __( "If checked - breadcrumbs will be displaied on forum sites", "lingo" ) ),
            )
        ), 
        array(
            "name" => "display_search",
            "type" => "lingo_field_checkbox",
            "order" => 10,
            "label" => __("Display search", "lingo"),
            "max_choices" => 1,
            "options" => array(
                array( "value"=> "1", "text"=> __( "If checked - search will be displaied on forum sites", "lingo" ) ),
            )
        ),
        array(
            "name" => "use_tags",
            "type" => "lingo_field_checkbox",
            "order" => 10,
            "label" => __("Use Tags", "lingo"),
            "max_choices" => 1,
            "options" => array(
                array( "value"=> "1", "text"=> __( "If checked - users can add tags to posts and filter topics by tags", "lingo" ) ),
            )
        ),
    )
));



