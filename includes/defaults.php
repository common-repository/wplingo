<?php
/**
 * Lingo Defaults
 * 
 * Load class-lingo.php and functions.php before using this file
 * 
 * This file contains default values for frontend "post ad" form structure and currency.
 * 
 * Registers Form fields and validators.
 *
 * @uses Lingo
 * @uses lingo_config
 * 
 * @package     Lingo
 * @copyright   Copyright (c) 2016, Mark Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Set default "Post Ad" form structure and save it in Lingo Singleton
Lingo::instance()->set("form", array(
    "name" => "advert",
    "action" => "",
    "field" => array(
        array(
            "name" => "_post_id",
            "type" => "lingo_field_hidden",
            "order" => 10,
            "label" => ""
        ),
        array(
            "name" => "_lingo_action",
            "type" => "lingo_field_hidden",
            "order" => 10,
            "label" => ""
        ),
        array(
            "name" => "_contact_information",
            "type" => "lingo_field_header",
            "order" => 10,
            "label" => __( 'Contact Information', 'lingo' )
        ),
        array(
            "name" => "_lingo_account",
            "type" => "lingo_field_account",
            "order" => 10,
            "label" => __( "Account", "lingo" ),
        ),
        array(
            "name" => "lingo_person",
            "type" => "lingo_field_text",
            "order" => 10,
            "label" => __( "Contact Person", "lingo" ),
            "is_required" => true,
            "validator" => array( 
                array( "name" => "is_required" ),
            )
        ),
        array(
            "name" => "lingo_email",
            "type" => "lingo_field_text",
            "order" => 10,
            "label" => __( "Email", "lingo" ),
            "is_required" => true,
            "validator" => array( 
                array( "name" => "is_required" ),
                array( "name" => "is_email" )
            )
        ),
        array(
            "name" => "lingo_phone",
            "type" => "lingo_field_text",
            "order" => 10,
            "label" => __( "Phone Number", "lingo"),
            "validator" => array(
                array(
                    "name" => "string_length",
                    "params" => array( "min" => 5 )
                )
            )
        ),
        array(
            "name" => "_item_information",
            "type" => "lingo_field_header",
            "order" => 20,
            "label" => __( 'Item Information', 'lingo' )
        ),
        array(
            "name" => "post_title",
            "type" => "lingo_field_text",
            "order" => 20,
            "label" => __( "Title", "lingo" ),
            "validator" => array(
                array( "name"=> "is_required" )
            )
        ),
        array(
            "name" => "advert_category",
            "type" => "lingo_field_select",
            "order" => 20,
            "label" => __("Category", "lingo"),
            "max_choices" => 10,
            "options" => array(),
            "options_callback" => "lingo_taxonomies"
        ),
        array(
            "name" => "gallery",
            "type" => "lingo_field_gallery",
            "order" => 20,
            "label" => __( "Gallery", "lingo" )
        ),
        array(
            "name" => "post_content",
            "type" => "lingo_field_textarea",
            "order" => 20,
            "label" => __( "Description", "lingo" ),
            "validator" => array(
                array( "name"=> "is_required" )
            ),
            "mode" => "tinymce-mini"
        ),
        array(
            "name" => "lingo_price",
            "type" => "lingo_field_text",
            "order" => 20,
            "label" => __("Price", "lingo"),
            "description" => "",
            "attr" => array(
                "key" => "value"
            ),
            "filter" => array(
                array( "name" => "money" )
            ),
        ),
        array(
            "name" => "lingo_location",
            "type" => "lingo_field_text",
            "order" => 20,
            "label" => __( "Location", "lingo" ),
        ),
    )
));

// Set default search form in [lingo_list] shortcode
Lingo::instance()->set("form_search", array(
    "name" => "search",
    "action" => "",
    "field" => array(
        array(
            "name" => "query",
            "type" => "lingo_field_text",
            "order" => 10,
            "attr" => array(
                "placeholder" => __("Keyword ...", "lingo")
            ),
            "meta" => array(
                "search_group" => "visible",
                "search_type" => "half" 
            )

        ),
        array(
            "name" => "location",
            "type" => "lingo_field_text",
            "order" => 10,
            "attr" => array(
                "placeholder" => __("Location ...", "lingo")
            ),
            "meta" => array(
                "search_group" => "visible",
                "search_type" => "half"
            )
        )
    )
));



/** REGISTER FORM FIELDS */

// Register <span> input
/** @see lingo_field_label() */
lingo_form_add_field("lingo_field_label", array(
    "renderer" => "lingo_field_label",
    "callback_save" => null,
    "callback_bind" => null,
));

// Register <input type="hidden" /> input
/** @see lingo_field_hidden() */
lingo_form_add_field("lingo_field_hidden", array(
    "renderer" => "lingo_field_hidden",
    "callback_save" => "lingo_save_single",
    "callback_bind" => "lingo_bind_single",
));

// Register <input type="text" /> input
/** @see lingo_field_text() */
lingo_form_add_field("lingo_field_text", array(
    "renderer" => "lingo_field_text",
    "callback_save" => "lingo_save_single",
    "callback_bind" => "lingo_bind_single",
));

// Register <textarea></textarea> input
/** @see lingo_field_textarea() */
lingo_form_add_field("lingo_field_textarea", array(
    "renderer" => "lingo_field_textarea",
    "callback_save" => "lingo_save_single",
    "callback_bind" => "lingo_bind_single",
));

// Register <select>...</select> input
/** @see lingo_field_select() */
lingo_form_add_field("lingo_field_select", array(
    "renderer" => "lingo_field_select",
    "callback_save" => "lingo_save_multi",
    "callback_bind" => "lingo_bind_multi",
));

// Register <input type="checkbox" /> input
/** @see lingo_field_checkbox() */
lingo_form_add_field("lingo_field_checkbox", array(
    "renderer" => "lingo_field_checkbox",
    "callback_save" => "lingo_save_multi",
    "callback_bind" => "lingo_bind_multi",
));

// Register <input type="radio" /> input
/** @see lingo_field_radio() */
lingo_form_add_field("lingo_field_radio", array(
    "renderer" => "lingo_field_radio",
    "callback_save" => "lingo_save_single",
    "callback_bind" => "lingo_bind_single",
));

// Register custom image upload field
/** @see lingo_field_gallery() */ 
lingo_form_add_field("lingo_field_gallery", array(
    "renderer" => "lingo_field_gallery",
    "callback_save" => null,
    "callback_bind" => null,
));

// Register <input type="hidden" /> input
/** @see lingo_field_account() */
lingo_form_add_field("lingo_field_account", array(
    "renderer" => "lingo_field_account",
    "callback_save" => "lingo_save_multi",
    "callback_bind" => "lingo_bind_multi",
));


/* REGISTER FORM VALIDATORS */

// Register "is required" validator
/** @see lingo_is_required() */
lingo_form_add_validator("is_required", array(
    "callback" => "lingo_is_required",
    "label" => __( "Is Required", "lingo" ),
    "params" => array(),
    "default_error" => __( "Field cannot be empty.", "lingo" ),
    "on_failure" => "break",
    "validate_empty" => true
));

// Register "is email" validator
/** @see lingo_is_email() */
lingo_form_add_validator("is_email", array(
    "callback" => "lingo_is_email",
    "label" => __( "Email", "lingo" ),
    "params" => array(),
    "default_error" => __( "Provided email address is invalid.", "lingo" ),
    "validate_empty" => false
));

// Register "is integer" validator
/** @see lingo_is_integer() */
lingo_form_add_validator("is_integer", array(
    "callback" => "lingo_is_integer",
    "label" => __( "Is Integer", "lingo" ),
    "params" => array(),
    "default_error" => __( "Provided value is not an integer.", "lingo" ),
    "validate_empty" => false
));

// Register "string length" validator
/** @see lingo_string_length() */
lingo_form_add_validator("string_length", array(
    "callback" => "lingo_string_length",
    "label" => __( "String Length", "lingo" ),
    "params" => array(),
    "default_error" => __( "Incorrect string length.", "lingo" ),
    "message" => array(
        "to_short" => __( "Text needs to be at least %min% characters long.", "lingo" ),
        "to_long" => __( "Text cannot be longer than %max% characters.", "lingo")
    ),
    "validate_empty" => false
));

