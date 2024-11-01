<?php
/**
 * Admin Pages
 * 
 * Class handles functions to manage core and extensions options of WPLingo 
 *
 * @package     Lingo
 * @copyright   Copyright (c) 2016, Mark Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

/**
 * Adds options link into wp-admin menu
 * 
 * @return void
 */
function lingo_add_options_link() {
    
    $menu_page = apply_filters('lingo_menu_page', array(
        "parent_slug" => "edit.php?post_type=lingo_topic",
        "page_title" => __( 'Lingo Options', 'lingo' ),
        "menu_title" => __( 'Options', 'lingo' ),
        "capability" => "install_plugins",
        "menu_slug" => "lingo-extensions",
        "function" => "lingo_admin_page_extensions"
    ));
    
    $r = add_submenu_page(
        $menu_page["parent_slug"], 
        $menu_page["page_title"], 
        $menu_page["menu_title"], 
        $menu_page["capability"], 
        $menu_page["menu_slug"], 
        $menu_page["function"]
    );
    
    $menu_page = apply_filters('lingo_menu_page', array(
        "parent_slug" => "edit.php?post_type=lingo_topic",
        "page_title" => __( 'Lingo E-mail Templates', 'lingo' ),
        "menu_title" => __( 'E-mail Templates', 'lingo' ),
        "capability" => "install_plugins",
        "menu_slug" => "lingo-email-templates",
        "function" => "lingo_admin_page_email_template"
    ));

    $r = add_submenu_page(
        $menu_page["parent_slug"], 
        $menu_page["page_title"], 
        $menu_page["menu_title"], 
        $menu_page["capability"], 
        $menu_page["menu_slug"], 
        $menu_page["function"]
    );
}

/**
 * Allows to manage extensions of WPLingo in wp-admin / Forums / Options
 * 
 * @return bool
 */
function lingo_admin_page_extensions() {
    
    $module_groups = array(
        array(
            "title" => __( "Modules", "lingo" ),
            "modules" => array(
                "core" => array(
                    "title" => __("Core", "adverts"),
                    "text" => __("Here you can configure most basic WPLingo options, applicable on any site.", "lingo"),
                    "type" => "static",
                    "plugin" => null,
                ),
                /*"payments" => array(
                    "title" => __("Payments", "adverts"),
                    "text" => __("Charge users for posting classified ads on your site.", "adverts"),
                    "type" => "",
                    "plugin" => null
                ),
                "wc-payments" => array(
                    "title" => __("WooCommerce Payments", "adverts"),
                    "text" => __("Use WooCommerce to charge users for posting classifieds.", "adverts"),
                    "type" => "",
                    "plugin" => "wpadverts-wc/wpadverts-wc.php",
                    "purchase_url" => "http://wpadverts.com/extensions/woocommerce-integration/"
                )*/
            )
        ),
        /*array(
            "title" => __( "Features", "adverts" ),
            "modules" => array(
            )
        ),*/
    );
    
    $module_groups = apply_filters('lingo_module_groups', $module_groups);
    
    if(lingo_request('enable')) {
        // User is trying to enable module
        $enable = lingo_request('enable');
        $module[$enable] = 0.5;
        
        // Save config
        lingo_config_set( 'config.module', $module );
        lingo_config_save( 'config' );
        
        wp_redirect( add_query_arg( array( 'enable' => false, 'noheader' => false, 'module' => $enable ) ) );
        
        exit;
    }
    
    if(lingo_request('disable')) {
        // User is trying to disable module
        $disable = lingo_request('disable');
        
        if(isset($module[$disable])) {
            unset($module[$disable]);
        }
        
        // Save config
        lingo_config_set( 'config.module', $module );
        lingo_config_save( 'config' );
        
        wp_redirect( remove_query_arg( array( 'disable', 'noheader' ) ) );
        exit;
    }
    
    if( lingo_request( 'module' ) ) {
        // Load module (based on $_GET[module]) panel
        $name = lingo_request( 'module' );
        $module_current = null;
        $module_key = null;
        
        foreach($module_groups as $group) {
            foreach($group["modules"] as $key => $tmp) {
                if($key == $name) {
                    $module_current = $tmp;
                    $module_key = $key;
                    break 2;
                }
            }
        }
        
        if( $module_current === null ) {
            esc_html_e( sprintf( __( "Module [%s] does not exist.", "adverts" ), $name ) );
            return;
        }

        if($module_current["plugin"]) {
            include_once dirname( LINGO_PATH ) . '/' . dirname( $module_current["plugin"] ) . '/includes/admin-module-options.php';
        } else {
            include_once LINGO_PATH . 'addons/' . $module_key . '/includes/admin-module-options.php';
        }
    } else {
        // Display modules list template
        include LINGO_PATH . 'admin/options.php';
    }
}

/**
 * Displays template for e-mail templates configuration
 */
function lingo_admin_page_email_template() {
    
    $key = lingo_request("key", null);
    
    if(isset($key) && !empty($key)) {
        
        $flash = Lingo_Flash::instance();
        
        $scheme = Lingo::instance()->get("form_email_templates");
        $form = new Lingo_Form( $scheme );
        
        $template = lingo_email_template($key);
        
        if(isset($_POST) && !empty($_POST)) {
            $form->bind( stripslashes_deep( $_POST ) );
            $valid = $form->validate();

            if($valid) {
                $data = $form->get_values();

                foreach($data as $k => $v) {
                    lingo_email_template_set($key, $k, $v);
                }

                lingo_email_template_save();
                
                $flash->add_info( __("Settings updated.", "lingo") );
            } else {
                $flash->add_error( __("There are errors in your form.", "lingo") );
            }
        } else {
            $form->bind( $template );
        }     
        
        include LINGO_PATH . 'admin/email-template_edit.php';
        return;
    }
    
    include LINGO_PATH . 'admin/email-templates.php';
}