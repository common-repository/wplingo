<?php
/**
 * Lingo Forms
 * 
 * Class handles all forms used in WPLingo
 *
 * @package     Lingo
 * @copyright   Copyright (c) 2016, Mark Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */


// Forum used to add new Topic
Lingo::instance()->set("form_add_new_topic", array(
    "name" => "",
    "action" => "",
    "field" => array(
        array(
            "name" => "_new_topic",
            "type" => "lingo_field_header",
            "order" => 10,
            "label" => __( 'Add New Topic', 'lingo' ),
            "title" => __( 'Add New Topic', 'lingo' )
        ),
        array(
            "name" => "new_topic_title",
            "type" => "lingo_field_text",
            "order" => 10,
            "label" => __("Topic Title", "lingo"),
            "placeholder" => __("New Topic Title...", "lingo"),
            "validator" => array(
                array("name"=>"is_required"),
                array(
                    "name" => "string_length",
                    "params" => array( "min" => 5 ),
                ),                
            )
        ),
        array(
            "name" => "new_topic_message",
            "type" => "lingo_field_textarea",
            "order" => 10,
            "label" => __("Messsage", "lingo"),
            "mode" => "tinymce-mini",
            "validator" => array(
                array("name"=>"is_required"),
            )
        ),

    )
));

Lingo::instance()->set("form_add_new_topic_with_tags", array(
    "name" => "",
    "action" => "",
    "field" => array(
        array(
            "name" => "_new_topic",
            "type" => "lingo_field_header",
            "order" => 10,
            "label" => __( 'Add New Topic', 'lingo' ),
            "title" => __( 'Add New Topic', 'lingo' )
        ),
        array(
            "name" => "new_topic_title",
            "type" => "lingo_field_text",
            "order" => 10,
            "label" => __("Topic Title", "lingo"),
            "placeholder" => __("New Topic Title...", "lingo"),
            "validator" => array(
                array("name"=>"is_required"),
                array(
                    "name" => "string_length",
                    "params" => array( "min" => 5 ),
                ),                
            )
        ),
        array(
            "name" => "new_topic_tags",
            "type" => "lingo_field_text",
            "order" => 10,
            "label" => __("Topic Tags", "lingo"),
            "hint" => __("Tags separated by comma", "lingo"),
        ),
        array(
            "name" => "new_topic_message",
            "type" => "lingo_field_textarea",
            "order" => 10,
            "label" => __("Messsage", "lingo"),
            "mode" => "tinymce-mini",
            "validator" => array(
                array("name"=>"is_required"),
            )
        ),

    )
));

// Form used to edit Topic
Lingo::instance()->set("form_edit_topic", array(
    "name" => "",
    "action" => "",
    "field" => array(
        array(
            "name" => "_edit_topic",
            "type" => "lingo_field_header",
            "order" => 10,
            "label" => __( 'Edit Topic', 'lingo' ),
            "title" => __( 'Edit Topic', 'lingo' )
        ),
        array(
            "name" => "topic_title",
            "type" => "lingo_field_text",
            "order" => 10,
            "label" => __("Topic Title", "lingo"),
            "placeholder" => __("Topic Title...", "lingo"),
            "validator" => array(
                array("name"=>"is_required"),
                array(
                    "name" => "string_length",
                    "params" => array( "min" => 5 ),
                ),                
            )
        ),
    )
));

Lingo::instance()->set("form_edit_topic_with_tags", array(
    "name" => "",
    "action" => "",
    "field" => array(
        array(
            "name" => "_edit_topic",
            "type" => "lingo_field_header",
            "order" => 10,
            "label" => __( 'Edit Topic', 'lingo' ),
            "title" => __( 'Edit Topic', 'lingo' )
        ),
        array(
            "name" => "topic_title",
            "type" => "lingo_field_text",
            "order" => 10,
            "label" => __("Topic Title", "lingo"),
            "placeholder" => __("Topic Title...", "lingo"),
            "validator" => array(
                array("name"=>"is_required"),
                array(
                    "name" => "string_length",
                    "params" => array( "min" => 5 ),
                ),                
            )
        ),
        array(
            "name" => "topic_tags",
            "type" => "lingo_field_text",
            "order" => 10,
            "label" => __("Topic Tags", "lingo"),
            "hint" => __("Tags separated by comma", "lingo"),
            "value" => "",
        ),
    )
));

// Form used to add new post
Lingo::instance()->set("form_add_new_post", array(
    "name" => "",
    "action" => "",
    "field" => array(
        array(
            "name" => "_new_post",
            "type" => "lingo_field_header",
            "order" => 10,
            "label" => __( 'Add New Post', 'lingo' ),
            "title" => __( 'Add New Post', 'lingo' )
        ),
        
        array(
            "name"  => "new_post_nickname",
            "type"  => "lingo_field_text",
            "order" => 10,
            "label" => __("Nickname", "lingo"),
            "value" => "Guest",
            "atribute" => array(
                array("name" => "only_guest"),
            ),
        ),
        array(
            "name" => "new_post_message",
            "type" => "lingo_field_textarea",
            "order" => 10,
            "label" => __("Messsage", "lingo"),
            "mode" => "tinymce-mini",
            "images"    => true,
            "validator" => array(
                array("name" => "is_required"),
            )
        ),
        array(
            "name" => "new_post_topic_id",
            "type" => "lingo_field_hidden",
            "order" => 10,
        ),

    )
));

// E-mail template config form
Lingo::instance()->set("form_email_templates", array(
    "name" => "",
    "action" => "",
    "field" => array(
        array(
            "name" => "_email_template",
            "type" => "lingo_field_header",
            "order" => 10,
            "label" => __( 'Edit Template', 'lingo' ),
            "title" => __( 'Edit Template', 'lingo' )
        ),
        array(
            "name"  => "title",
            "type"  => "lingo_field_text",
            "order" => 10,
            "label" => __("Title", "lingo"),
            "validator" => array(
                array("name" => "is_required"),
            )
        ),
        array(
            "name"  => "message",
            "type"  => "lingo_field_textarea",
            "order" => 10,
            "mode" => "tinymce-mini",
            "label" => __("Message", "lingo"),
            "validator" => array(
                array("name" => "is_required"),
            )
        ),
        array(
            "name" => "id",
            "type" => "lingo_field_hidden",
            "order" => 10,
        ),
        array(
            "name" => "name",
            "type" => "lingo_field_hidden",
            "order" => 10,
        ),
        array(
            "name" => "description",
            "type" => "lingo_field_hidden",
            "order" => 10,
        ),
    )
));