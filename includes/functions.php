<?php
/**
 * List of main lingo functions
 * 
 * @package     Lingo
 * @copyright   Copyright (c) 2016, Mark Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


function lingo_email_template_set($template, $param, $value) {
    global $lingo_email_templates;
    
    $lingo_email_templates[$template][$param] = $value;
}

function lingo_email_template_save() {
    global $lingo_email_templates;
 
    $option_name = 'lingo_email_templates';
    
    update_option( $option_name, $lingo_email_templates );
}

function lingo_email_template($param = null) {
    global $lingo_email_templates, $lingo_namespace;
    
    if(stripos($param, '.') !== false) {
        list($template, $field) = explode(".", $param);
    } else {
        $template = $param;
    }
    
    $default = $lingo_namespace['email_templates'];
    $option_name = 'lingo_email_templates';
    
    if(!isset($lingo_email_templates)) {
        $lingo_email_templates = get_option( $option_name );
    }
    
    if(!is_array($lingo_email_templates)) {
        $lingo_email_templates = array();
    }
 
    if($lingo_email_templates === false) {
        $lingo_email_templates = array();
        add_option( $option_name, $lingo_email_templates);
    }
    
    $lingo_email_templates = array_merge( $default, $lingo_email_templates );
    foreach($lingo_email_templates as $k => $a) {
        $lingo_email_templates[$k] = array_merge($default[$k], $a);
    }

    if( empty($param) ) {
        return $lingo_email_templates;
    }
    
    if( empty($field) || $field == "ALL" ) {
        return $lingo_email_templates[$template];
    }

    if(isset($lingo_email_templates[$template][$template]) && 
        (!empty($lingo_email_templates[$template][$field]) || is_numeric($lingo_email_templates[$template][$field]) || is_array($lingo_email_templates[$template][$field]) || $lingo_email_templates[$template][$field] === "")) {
        return $lingo_email_templates[$template][$field];
    } else {
        return $default;
    }
}

/**
 * Returns config value
 * 
 * @global array $lingo_config
 * @global array $lingo_namespace
 * @param string $param Should be module_name.param_name
 * @return mixed
 */
function lingo_config($param = null) {
    global $lingo_config, $lingo_namespace;

    if(stripos($param, '.') === false) {
        $module = 'config';
    } else {
        list($module, $param) = explode(".", $param);
    }
    
    if( !isset($lingo_namespace[$module]) ) {
        trigger_error('Incorrect module name ['.$module.']', E_USER_ERROR);
    }
    
    $default = $lingo_namespace[$module]['default'];
    $option_name = $lingo_namespace[$module]['option_name'];
    
    if($lingo_config === null) {
        $lingo_config = array();
    }
    
    if(!isset($lingo_config[$module])) {
        $lingo_config[$module] = get_option( $option_name );
    }

    if($lingo_config[$module] === false) {
        $lingo_config[$module] = array();
        add_option( $option_name, $lingo_config[$module]);
    }
    // merge with defaults
    $lingo_config[$module] = array_merge( $default, $lingo_config[$module] );

    if( empty($param) || $param == "ALL" ) {
        return $lingo_config[$module];
    }

    if(isset($lingo_config[$module][$param]) && 
        (!empty($lingo_config[$module][$param]) || is_numeric($lingo_config[$module][$param]) || is_array($lingo_config[$module][$param]) || $lingo_config[$module][$param] === "")) {
        return $lingo_config[$module][$param];
    } else {
        return $default;
    }
}

/**
 * Return config default values
 * 
 * @global array $lingo_namespace
 * @param string $param
 * @since 0.1
 * @return array
 */
function lingo_config_default($param = null) {
    global $lingo_namespace;

    if(stripos($param, '.') === false) {
        $module = 'config';
    } else {
        list($module, $param) = explode(".", $param);
    }
    
    if( !isset($lingo_namespace[$module]) ) {
        trigger_error('Incorrect module name ['.$module.']', E_USER_ERROR);
    }
    
    if( !empty($param) ) {
        return $lingo_namespace[$module]['default'][$param];
    } else {
        return $lingo_namespace[$module]['default'];
    }
}

/**
 * Sets config value
 * 
 * Note this function does NOT save config in DB.
 * 
 * @global array $lingo_config
 * @global array $lingo_namespace
 * @param string $param
 * @param mixed $value
 * @since 0.1
 * @return void
 */
function lingo_config_set($param, $value) {
    global $lingo_config, $lingo_namespace;
    
    if(stripos($param, '.') === false) {
        $module = 'config';
    } else {
        list($module, $param) = explode(".", $param);
    }
    
    if( !isset($lingo_namespace[$module]) ) {
        trigger_error('Incorrect module name ['.$module.']', E_USER_ERROR);
    }
    
    $default = $lingo_namespace[$module]['default'];
    $option_name = $lingo_namespace[$module]['option_name'];
    
    $lingo_config[$module][$param] = $value;
}

/**
 * Saves config in DB 
 * 
 * @uses update_option()
 * 
 * @global array $lingo_config
 * @global array $lingo_namespace
 * @param string $module
 * @since 0.1
 * @return void
 */
function lingo_config_save( $module = null ) {
    global $lingo_config, $lingo_namespace;
    
    if( $module === null ) {
        $module = "config";
    }
    
    if( !isset($lingo_namespace[$module]) ) {
        trigger_error('Incorrect module name ['.$module.']', E_USER_ERROR);
    }
    
    $default = $lingo_namespace[$module]['default'];
    $option_name = $lingo_namespace[$module]['option_name'];
    
    update_option( $option_name, $lingo_config[$module] );
}

/**
 * Renders flash messages
 * 
 * @param array $data
 * @since 0.1
 * @return void
 */
function lingo_flash( $data ) {

    ?>

    <?php if(isset($data["error"]) && is_array($data["error"]) && !empty($data["error"])): ?>
    <div class="lingo-flash-error">
    <?php foreach( $data["error"] as $key => $error): ?>
        <span><?php echo esc_html($error); ?></span>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if(isset($data["info"]) && is_array($data["info"]) && !empty($data["info"])): ?>
    <div class="lingo-flash-info">
    <?php foreach( $data["info"] as $key => $info): ?>
        <span><?php echo esc_html($info) ?></span>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php
}

/**
 * Returns value from $_POST or $_GET table by $key.
 * 
 * If the $key does not exist in neither of global tables $default value
 * is returned instead.
 * 
 * @param string $key
 * @param mixed $default
 * @since 0.1
 * @return mixed Array or string
 */
function lingo_request($key, $default = null) {
    if(isset($_POST[$key])) {
        return stripslashes_deep($_POST[$key]);
    } elseif(isset($_GET[$key])) {
        return stripslashes_deep($_GET[$key]);
    } else {
        return $default;
    }
}

/**
 * Check if is edit/add page.
 * @global WP_Page $pagenow
 * @param string $new_edit
 * @return boolean
 */
function is_edit_page($new_edit = null){
    global $pagenow;
    //make sure we are on the backend
    if (!is_admin()) return false;


    if($new_edit == "edit")
        return in_array( $pagenow, array( 'post.php',  ) );
    elseif($new_edit == "new") //check for new post page
        return in_array( $pagenow, array( 'post-new.php' ) );
    else //check for either new or edit
        return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
}

/**
 * Compare two posts to sort array by post_date
 * 
 * @param WP_Post $a
 * @param WP_Post $b
 * @return int
 */
function lingo_compare_posts($a, $b)
{
    $at = strtotime($a->post_date);
    $bt = strtotime($b->post_date);
    
    if($at < $bt) {
        return 1;
    } elseif ($at > $bt) {
        return -1;
    } else {
        return 0;
    }
}

/**
 * Checks if plugin is uploaded to wp-content/plugins directory.
 * 
 * This functions checks if plugin is uploaded to plugins directory, note that
 * as a $basename you need to pass plugin-dir/plugin-file-name.php
 * 
 * @access public
 * @since 1.0
 * @param string $basename Plugin basename
 * @return boolean
 * 
 */
function lingo_plugin_uploaded( $basename ) {
    return is_file( dirname( LINGO_PATH ) . "/" . ltrim( $basename, "/") );
}


/**
 * Layout for forms generated by Lingo in wp-admin panel.
 * 
 * @param Lingo_Form $form
 * @param array $options
 * @since 0.1
 * @return void
 */
function lingo_form_layout_config(Lingo_Form $form, $options = array()) {
   
    $a = array();
    
?>

    <?php foreach($form->get_fields( array( "type" => array( "lingo_field_hidden" ) ) ) as $field): ?>
    <?php call_user_func( lingo_field_get_renderer($field), $field) ?>
    <?php endforeach; ?>
    
    <?php foreach($form->get_fields( $options ) as $field): ?>
        <?php if($field["type"] == "lingo_field_header"): ?>
        <tr valign="top">
            <th colspan="2" style="padding-bottom:0px">
                <h3 style="border-bottom:1px solid #dfdfdf; line-height:1.4em; font-size:15px"><?php esc_html_e($field["title"]) ?></h3>
            </th>
        </tr>
        <?php else: ?>
        <tr valign="top" class="<?php if(lingo_field_has_errors($field)): ?>lingo-field-error<?php endif; ?>">
            <th scope="row">
                <label <?php if(!in_array($field['type'], $a)): ?>for="<?php esc_attr_e($field["name"]) ?>"<?php endif; ?>>
                    <?php esc_html_e($field["label"]) ?>
                    <?php if(lingo_field_has_validator($field, "is_required")): ?><span class="lingo-red">&nbsp;*</span><?php endif; ?>
                </label>
            </th>
            <td class="">
                
                <?php
                    switch($field["type"]) {
                        case "lingo_field_text": 
                            $field["class"] = (isset($field["class"]) ? $field["class"] : '') . ' regular-text';
                            break;
                    }
                ?>
                
                <?php call_user_func( lingo_field_get_renderer($field), $field) ?>

                <?php if(isset($field['hint']) && !empty($field['hint'])): ?>
                <br/><span class="description"><?php echo esc_html($field['hint']); ?></span>
                <?php endif; ?>

                <?php if(lingo_field_has_errors($field)): ?>
                <ul class="updated lingo-error-list">
                    <?php foreach($field["error"] as $k => $v): ?>
                    <li><?php esc_html_e($v) ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </td>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>

<?php
}

/**
 * Registers form field
 * 
 * This function is mainly used in templates when generating form layout.
 * 
 * @param string $name
 * @param mixed $params
 * @since 0.1
 * @return void
 */
function lingo_form_add_field( $name, $params ) {
    $field = Lingo::instance()->get("form_field", array());
    $field[$name] = $params;
    
    Lingo::instance()->set("form_field", $field);
}

/**
 * Display flash messages in wp-admin
 * 
 * This function is being used mainly in Lingo wp-admin template files
 * 
 * @since 0.1
 * @return void
 */
function lingo_admin_flash() {
    $flash = Lingo_Flash::instance();
    ?>

    <?php foreach($flash->get_info() as $info): ?>
    <div class="updated fade">
        <p><?php echo esc_html($info); ?></p>
    </div>
    <?php endforeach; ?>

    <?php foreach($flash->get_error() as $error): ?>
    <div class="error">
        <p><?php echo esc_html($error); ?></p>
    </div>
    <?php endforeach; ?>

    <?php $flash->dispose() ?>
    <?php $flash->save() ?>
<?php
}


/**
 * Check if field has errors
 * 
 * This function is mainly used in templates when generating form layout.
 * 
 * @param array $field
 * @since 0.1
 * @return boolean
 */
function lingo_field_has_errors( $field ) {
    if( isset($field["error"]) && is_array($field["error"]) ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if Lingo_Form field has $validator
 * 
 * This function is mainly used in templates when generating form layout.
 * 
 * @param array $field
 * @param string $validator
 * @since 0.1
 * @return boolean
 */
function lingo_field_has_validator( $field, $validator ) {
    if( !isset($field["validator"]) || !is_array($field["validator"]) ) {
        return false;
    }
    
    foreach($field["validator"] as $v) {
        if($v["name"] == $validator) {
            return true;
        }
    }
    
    return false;
}

/**
 * Checks if Lingo_Form field has $atribute
 * @param array $field
 * @param string $atribute
 * @return boolean
 */
function lingo_field_has_atribute( $field, $atribute ) {
    if( !isset($field["atribute"]) || !is_array($field["atribute"]) ) {
        return false;
    }
    
    foreach($field["atribute"] as $a) {
        if($a["name"] == $atribute) {
            return true;
        }
    }
    
    return false;
}

/**
 * Returns form field rendering function
 * 
 * This function is mainly used in templates when generating form layout.
 * 
 * @param array $field
 * @since 0.1
 * @return string
 */
function lingo_field_get_renderer( $field ) {
    $f = Lingo::instance()->get("form_field");
    $f = $f[$field["type"]];

    return $f["renderer"];
}

/**
 * Form hidden input renderer
 * 
 * Prints (to browser) HTML for <input type="hidden" /> input
 * 
 * $field params:
 * - name: string
 * - value: mixed (scalar or array)
 * 
 * @param array $field
 * @since 0.1
 * @return void
 */
function lingo_field_hidden( $field ) {
    $html = new Lingo_Html("input", array(
        "type" => "hidden",
        "name" => $field["name"],
        "id" => $field["name"],
        "class" => isset($field["class"]) ? $field["class"] : null,
        "value" => isset($field["value"]) ? $field["value"] : "",
    ));
    
    echo $html->render();
}

/**
 * Form text/paragraph renderer
 * 
 * Prints (to browser) HTML for <span></span> input
 * 
 * $field params:
 * - content: string (text to display)
 * 
 * @param array $field
 * @since 0.1
 * @return void
 */
function lingo_field_label( $field ) {
    $html = new Lingo_Html("span", array(
        "class" => "lingo-flash lingo-flash-info"
    ), $field["content"]);
    
    echo esc_html($html->render());
}

/**
 * Form input text renderer
 * 
 * Prints (to browser) HTML for <input type="text" /> input
 * 
 * $field params:
 * - name: string
 * - value: mixed (scalar or array)
 * - class: string (HTML class attribute)
 * - placeholder: string
 * 
 * @param array $field
 * @since 0.1
 * @return void
 */
function lingo_field_text( $field ) {
    
    $attr = array(
        "type" => "text",
        "name" => $field["name"],
        "id" => $field["name"],
        "value" => isset($field["value"]) ? $field["value"] : "",
        "placeholder" => isset($field["placeholder"]) ? $field["placeholder"] : null,
        "class" => isset($field["class"]) ? $field["class"] : null
    );
    
    if( isset( $field["attr"] ) && is_array( $field["attr"] ) ) {
        foreach( $field["attr"] as $key => $value ) {
            if( $value !== null && is_scalar( $value ) ) {
                $attr[$key] = $value;
            }
        }
    }
    
    $html = new Lingo_Html( "input", $attr );
    
    echo $html->render();
}

/**
 * Form dropdown renderer
 * 
 * Prints (to browser) HTML for <select>...</select> input
 * 
 * $field params:
 * - name: string
 * - value: mixed (scalar or array)
 * - class: string (HTML class attribute)
 * - max_choices: integer
 * - attr: array (list of additional HTML attributes)
 * - empty_option: boolean (true if you want to add epty option at the beginning)
 * - empty_option_text: string
 * - options_callback: mixed
 * - options: array (for example array(array("value"=>1, "text"=>"title")) )
 * 
 * @param array $field
 * @since 0.1
 * @return void
 */
function lingo_field_select( $field ) {
    
    $html = "";
    $name = $field["name"];
    $multiple = false;
    
    if(isset($field["class"]) && $field["class"]) {
        $classes = $field["class"];
    } else {
        $classes = null;
    }

    if(isset($field["max_choices"]) && $field["max_choices"]>1) {
        $max = $field["max_choices"];
        $name .= "[]";
        $multiple = "multiple";
        $classes = "$classes lingo-multiselect lingo-max-choices[$max]";
        
        wp_enqueue_script( 'lingo-multiselect' );
    }

    $options = array(
        "id" => $field["name"],
        "name" => $name,
        "class" => $classes,
        "multiple" => $multiple
    );

    if(isset($field["attr"])) {
        $options += $field["attr"];
    }

    if(isset($field["empty_option"]) && $field["empty_option"]) {
        if(isset($field["empty_option_text"]) && !empty($field["empty_option_text"])) {
            $html .= '<option value="">'.esc_html($field["empty_option_text"]).'</options>';
        } else {
            $html .= '<option value="">&nbsp;</option>'; 
        }
    }

    if(isset($field["options_callback"])) {
        $opt = call_user_func( $field["options_callback"] );
    } elseif(isset($field["options"])) {
        $opt = $field["options"];
    } else {
        trigger_error("You need to specify options source for field [{$field['name']}].", E_USER_ERROR);
        $opt = array();
    }
    
    foreach($opt as $k => $v) {
        $selected = null;
        $depth = null;
        
        if(in_array($v["value"], (array)$field["value"])) {
            $selected = "selected";
        }
        
        if(isset($v["depth"])) {
            $depth = $v["depth"];
        }
        
        if(!$multiple) {
            $padding = str_repeat("&nbsp;", $depth * 2);
        } else {
            $padding = "";
        }
        
        $o = new Lingo_Html("option", array(
            "value" => $v["value"],
            "data-depth" => $depth,
            "selected" => $selected,
        ), $padding . $v["text"]);

        $html .= $o->render();
    }

    $input = new Lingo_Html("select", $options, $html);
    $input->forceLongClosing();
    
    echo $input->render();
}

/**
 * Form textarea renderer
 * 
 * Prints (to browser) HTML for <textarea></textarea> input
 * 
 * $field params:
 * - value: string
 * - mode: plain-text | tinymce-mini | tinymce-full
 * - placeholder: string (for plain-text only)
 * - name: string
 * 
 * @param array $field
 * @since 0.1
 * @return void
 */
function lingo_field_textarea( $field ) {
    
    $value = '';
    
    if(isset($field["value"])) {
        $value = $field["value"];
    }
    
    if($field["mode"] == "plain-text") {
        $html = new Lingo_Html("textarea", array(
            "name" => $field["name"],
            "rows" => 10,
            "cols" => 50,
            "placeholder" => isset($field["placeholder"]) ? $field["placeholder"] : null,
        ), $value);
        $html->forceLongClosing();
        
        echo esc_html($html->render());
        
    } elseif($field["mode"] == "tinymce-mini") {
    
        $params = array(
            "quicktags"=>false, 
            "media_buttons"=>false, 
            "teeny"=>false,
            "textarea_rows" => 8,
            'tinymce' => array(
                'toolbar1' => 'bold,italic,strikethrough,bullist,numlist,blockquote,justifyleft,justifycenter,justifyright,link,unlink,spellchecker,wp_adv',
                'theme_advanced_buttons2' => 'formatselect,justifyfull,forecolor,pastetext,pasteword,removeformat,charmap,outdent,indent,undo,redo',

                'theme_advanced_buttons1' => 'bold,italic,strikethrough,bullist,numlist,blockquote,justifyleft,justifycenter,justifyright,link,unlink,spellchecker,wp_adv',
                'theme_advanced_buttons2' => 'formatselect,justifyfull,forecolor,pastetext,pasteword,removeformat,charmap,outdent,indent,undo,redo',
             )
        );
        
        if($field["images"]) {
            $params["media_buttons"] = true;
        }

        wp_editor($field["value"], $field["name"], $params);
    } elseif($field["mode"] == "tinymce-full") {
        wp_editor($field["value"], $field["name"]);
    } else {
        _e("Parameter [mode] is missing in the form!", "lingo");
    }
}

/**
 * Form checkbox input(s) renderer
 * 
 * Prints (to browser) HTML for <input type="checkox" /> input
 * 
 * $field params:
 * - name: string
 * - value: mixed (scalar or array)
 * - options: array (for example array(array("value"=>1, "text"=>"title")) )
 * 
 * @param array $field
 * @since 0.1
 * @return void
 */
function lingo_field_checkbox( $field ) {
    
    $opts = "";
    $i = 1;
    
    if( !isset( $field["value"] ) ) {
        $value = array();
    } elseif( !is_array( $field["value"] ) ) {
        $value = (array)$field["value"];
    } else {
        $value = $field["value"];
    }
    
    foreach($field["options"] as $opt) {
        $checkbox = new Lingo_Html("input", array(
            "type" => "checkbox",
            "name" => $field["name"].'[]',
            "id" => $field["name"].'_'.$i,
            "value" => $opt["value"],
            "checked" => in_array($opt["value"], $value) ? "checked" : null
        ));

        $label = new Lingo_Html("label", array(
            "for" => $field["name"].'_'.$i
        ), $checkbox->render() . ' ' . $opt["text"]);
        
        if( isset( $field["class"] ) ) {
            $class = $field["class"];
        } else {
            $class = null;
        }
        
        $wrap = new Lingo_Html("div", array(
            "class" => $class
        ), $label->render() );
        
        $opts .= $wrap->render();
        
        $i++;
    }
    
    echo Lingo_Html::build("div", array("class"=>"lingo-form-input-group"), $opts);
}

/**
 * Form radio input(s) renderer
 * 
 * Prints (to browser) HTML for <input type="radio" /> input
 * 
 * $field params:
 * - name: string
 * - value: mixed (scalar or array)
 * - options: array (for example array(array("value"=>1, "text"=>"title")) )
 * 
 * @param array $field
 * @since 0.1
 * @return void
 */
function lingo_field_radio( $field ) {
    
    $opts = "";
    $i = 1;
    
    if( !isset( $field["value"] ) ) {
        $value = null;
    } else {
        $value = $field["value"];
    }
    
    foreach($field["options"] as $opt) {
        $checkbox = new Lingo_Html("input", array(
            "type" => "radio",
            "name" => $field["name"],
            "id" => $field["name"].'_'.$i,
            "value" => $opt["value"],
            "checked" => $opt["value"] == $value ? "checked" : null
        ));

        $label = new Lingo_Html("label", array(
            "for" => $field["name"].'_'.$i
        ), $checkbox->render() . ' ' . $opt["text"]);
        
        $opts .= "<div>".$label->render()."</div>";
        
        $i++;
    }
    
    echo Lingo_Html::build("div", array("class"=>"lingo-form-input-group"), $opts);
}

/**
 * Saves single Lingo_Form value in post meta table.
 * 
 * This function is used on scalar form elements, that is elements that return only
 * one value (<input type="text" />, <textarea />, <input type="radio" />)
 * 
 * @uses delete_post_meta()
 * @uses add_post_meta()
 * 
 * @since 1.0
 * @access public
 * @param int $post_id Topic ID
 * @param string $key Meta name
 * @param string $value Meta value
 * @return void
 */
function lingo_save_single( $post_id, $key, $value ) {
    if( $value == '' ) {
        delete_post_meta( $post_id, $key );
    } else {
        update_post_meta( $post_id, $key, $value );
    }
}

/**
 * Saves single Lingo_Form value in post meta table.
 * 
 * This function is used on scalar form elements, that is elements that return
 * array of values (<input type="checkbox" />, <select />)
 * 
 * @uses delete_post_meta()
 * @uses add_post_meta()
 * 
 * @since 1.0
 * @access public
 * @param int $post_id Advert ID
 * @param string $key Meta name
 * @param string $value Meta value
 * @return void
 */
function lingo_save_multi( $post_id, $key, $value ) {
    if( !is_array( $value ) ) {
        $value = array( $value );
    }

    $post_meta = get_post_meta( $post_id, $key, false);

    $to_insert = array_diff($value, $post_meta);
    $to_delete = array_diff($post_meta, $value);

    foreach( $to_delete as $meta_value ) {
        delete_post_meta( $post_id, $key, $meta_value );
    }
    foreach( $to_insert as $meta_value ) {
        add_post_meta( $post_id, $key, $meta_value );
    } 
}

/**
 * Binding function for scalar values
 * 
 * This function is used in Lingo_Form class filter and set values
 * for form fields which are using this function for binding.
 * 
 * @see Lingo_Form
 * @see lingo_form_add_field()
 * @see includes/default.php
 * 
 * @since 1.0
 * @access public
 * @param array $field Information about form field
 * @param string $value Value submitted via form
 * @return string Filtered value
 */
function lingo_bind_single($field, $value) {
    
    $filters = Lingo::instance()->get("field_filter", array());

    if( isset( $field["filter"] ) ) {
        foreach( $field["filter"] as $filter ) {
            if( isset( $filters[$filter["name"]] ) ) {
                $f = $filters[$filter["name"]];
                $value = call_user_func_array( $f["callback"], array($value) );
            } // end if;
        } // end foreach;
    } // end if;
    
    return $value;
}

/**
 * Binding function for array values
 * 
 * This function is used in Lingo_Form class filter and set values
 * for form fields which are using this function for binding (by default 
 * <select> and <input type="checkbox" /> are using it).
 * 
 * @see Lingo_Form
 * @see lingo_form_add_field()
 * @see includes/default.php
 * 
 * @since 1.0
 * @access public
 * @param array $field Information about form field
 * @param mixed $value Array or NULL value submitted via form
 * @return mixed
 */
function lingo_bind_multi($field, $value) {
    
    $filters = Lingo::instance()->get("field_filter", array());
    $key = $field["name"];
    
    if( $value === NULL ) {
        $value = array();
    } elseif( ! is_array( $value ) ) {
        $value = array( $value );
    }
    
    $result = array();
    
    foreach( $value as $v ) {
        $result[] = lingo_bind_single( $field, $v );
    }
    
    if( !isset( $field["max_choices"] ) || $field["max_choices"] == 1) {
        if( isset( $result[0] ) ) {
            return $result[0];
        } else {
            return "";
        }
    } else {
        return $result;
    }
}

/**
 * Registers form filter
 * 
 * @param string $name
 * @param array $params
 * @since 0.1
 * @return void
 */
function lingo_form_add_filter( $name, $params ) {
    $field_filter = Lingo::instance()->get("field_filter", array());
    $field_filter[$name] = $params;
    
    Lingo::instance()->set("field_filter", $field_filter);
}

/**
 * Registers form validator
 * 
 * @param string $name
 * @param array $params
 * @since 0.1
 * @return void
 */
function lingo_form_add_validator( $name, $params ) {
    $field_validator = Lingo::instance()->get("field_validator", array());
    $field_validator[$name] = $params;
    
    Lingo::instance()->set("field_validator", $field_validator);
}

/**
 * Is Required VALIDATOR
 * 
 * The function checks if $data is empty
 * 
 * @param mixed $data
 * @return string|boolean
 */
function lingo_is_required( $data ) {

    if( empty($data) && !is_numeric($data) ) {
        return "empty";
    } else {
        return true;
    }
}

/**
 * Is Email VALIDATOR
 * 
 * Checks if $email is valid email address
 * 
 * @uses is_email()
 * @param string $email
 * @return boolean|string
 */
function lingo_is_email( $email ) {
    if( is_email( $email ) ) {
        return true;
    } else {
        return "invalid";
    }
}

/**
 * Is Integer VALIDATOR
 * 
 * Checks if $value is integer 0 or greater.
 * 
 * @param string $value
 * @since 0.1
 * @return boolean|string
 */
function lingo_is_integer( $value ) {
    
    if( filter_var( $value, FILTER_VALIDATE_INT ) !== false ) {
        return true;
    } else {
        return "invalid";
    }
}

/**
 * String Length VALIDATOR
 * 
 * @param mixed $data
 * @param array $params Validation parameters (min and max length values)
 * @since 0.1
 * @return string|boolean
 */
function lingo_string_length( $data, $params = null ) {

    if( isset( $params["min"] ) && strlen( $data ) < $params["min"] ) {
        return "to_short";
    } 
    
    if( isset( $params["max"] ) && strlen( $data ) > $params["max"] ) {
        return "to_long";
    } 
    
    return true;
}

/**
 * Echo forum stats (post & topic number)
 * @param int $forum_id
 */
function lingo_get_forum_stats($forum_id) {
    
    $topics = get_posts(
        apply_filters( "lingo_topic_query", array(
            'posts_per_page' => -1,
            'post_type' => 'lingo_topic',
            'post_status'   => array('publish', 'closed'),
            'tax_query' => array(
                array(
                    'taxonomy' => 'lingo_forum_group',
                    'field' => 'term_id',
                    'terms' => $forum_id,
                )
            )
        ))
    );
    
    $posts = 0;
    foreach($topics as $topic) {
        $args = apply_filters( "lingo_post_query", array(
            'post_parent' => $topic->ID,
            'post_type'   => 'lingo_post', 
            'numberposts' => -1,
            'post_status' => 'publish' 
        )); 
        
        $children_array = get_children($args);
        $posts += count($children_array);
    }
    
    
    printf( __('Topics: %d', 'lingo'), count($topics));
    echo "<br/>";
    printf( __('Posts: %d', 'lingo'), $posts); 
}

/**
 * Echo data of last post in forum
 * @param int $forum_id
 */
function lingo_get_forum_last_post($forum_id) {
    
    $topics = get_posts(
        apply_filters( "lingo_topic_query", array(
            'posts_per_page' => -1,
            'post_type' => 'lingo_topic',
            'post_status' => array('publish', 'closed'),
            'tax_query' => array(
                array(
                    'taxonomy' => 'lingo_forum_group',
                    'field' => 'term_id',
                    'terms' => $forum_id,
                )
            )
        ))
    );
    wp_reset_query();
    
    
    $lastPost = null;
    $lastPostTopic = null;
    foreach($topics as $topic) {
        $args = apply_filters( "lingo_post_query", array(
            'post_parent' => $topic->ID,
            'post_type'   => 'lingo_post', 
            'numberposts' => 1,
            'post_status' => 'publish' 
        )); 
        
        $posts = get_children($args);
        $posts = array_values($posts)[0];
        
        if($lastPost == null) {
            $lastPost = $posts;
            $lastPostTopic = $topic;
        } elseif(strtotime($posts->post_date) > strtotime($lastPost->post_date)) {
            $lastPost = $posts;
            $lastPostTopic = $topic;
        }
    }

    $user = get_user_by('id', $lastPost->post_author);
    $username = lingo_get_post_author($lastPost->ID);
    
    if(isset($lastPost)) {
        /*printf( __('<a href="%s"><i class="fa fa-comments fa-lg" aria-hidden="true"></i> %s</a>', 'lingo'), esc_url(get_post_permalink($lastPostTopic->ID)), esc_html($lastPostTopic->post_title)); 
        echo "<br/>";   
        printf( __('<a href="%s"><img src="%s" class="lingo-user-avatar-small" />  %s</a>', 'lingo'), get_page_link(lingo_config('config.user_page_id')). "?id=" . $user->ID, get_avatar_url($user->ID, array('size' => 16)), $username);
        echo "<br/>";
        printf( __('<i class="fa fa-calendar fa-lg" aria-hidden="true" title="%s"></i> %s ago', 'lingo'), $lastPost->post_date, human_time_diff(strtotime($lastPost->post_date), time())); */
        
        
        
        
        printf( __('<a href="%s"> %s</a>', 'lingo'), esc_url(get_post_permalink($lastPostTopic->ID)), esc_html($lastPostTopic->post_title)); 
        echo "<br/>";
        printf( __('by: <a href="%s">%s</a> ', 'lingo'), get_page_link(lingo_config('config.user_page_id')). "?id=" . $user->ID, $username);
        echo "<br/>";   
        printf( __('%s ago', 'lingo'), human_time_diff(strtotime($lastPost->post_date), time())); 
        
    } else {
        _e('This forum is empty', 'lingo');
    }
    
}

/**
 * Echo data of last post in topic
 * @param int $topic_id
 */
function lingo_get_topic_last_post($topic_id) {
    

    $args = apply_filters( "lingo_post_query", array(
        'post_parent' => $topic_id,
        'post_type'   => 'lingo_post', 
        'numberposts' => 1,
        'post_status' => array('publish', 'closed'),
    )); 
    
    $post = array_values(get_children($args))[0];    
    $user = get_user_by('id', $post->post_author);
    $username = lingo_get_post_author($post->ID);
    
    if(isset($post)) {
        /*printf( __('<a href="%s"><img src="%s" class="lingo-user-avatar-small" />  %s</a>', 'lingo'), get_page_link(lingo_config('config.user_page_id')) . "?id=" . $user->ID, get_avatar_url($user->ID, array('size' => 16)), $username); 
        echo "<br/>";
        printf( __('<i class="fa fa-calendar fa-lg" aria-hidden="true" title="%s"></i> %s ago', 'lingo'), date("H:i:s - d-m-Y"), human_time_diff(strtotime($post->post_date), time())); */
        
        printf( __('<a href="%s"><img src="%s" class="lingo-user-avatar-small" />  %s</a>', 'lingo'), get_page_link(lingo_config('config.user_page_id')) . "?id=" . $user->ID, get_avatar_url($user->ID, array('size' => 16)), $username); 
        echo "<br/>";
        printf( __('%s ago', 'lingo'), human_time_diff(strtotime($post->post_date), time())); 
    } else {
        _e('This topic is empty', 'lingo');
    }
}

/**
 * Sorts forum
 * @param lingo_forum_group $a
 * @param lingo_forum_group $b
 * @return bool
 */
function lingo_forum_sort($a, $b) {
  
    if($a->order == null) {
        $a->order = 99999;
    }
    
    if($b->order == null) {
        $b->order = 99999;
    }
    
    return $a->order > $b->order;  
}


/*
 * Returns number of post for topic
 * @param int $topic_id
 * @return int 
 */
function lingo_get_topic_posts_num($topic_id) {
    $args = apply_filters( "lingo_post_query", array(
        'post_parent' => $topic_id,
        'post_type'   => 'lingo_post', 
        'numberposts' => -1,
        'post_status' => array('publish', 'closed'), 
    )); 
    
    $posts = get_children($args);   
    return count($posts);
}

/**
 * Displays warning message
 * @param string $warning
 */
function lingo_display_warning($warning) {
    
    ?>
        <div class="lingo-warning lingo-flash">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            <?php esc_attr_e($warning, 'lingo'); ?>
        </div>    
    <?php
    
}

/**
 * Displays info message
 * @param string $info
 */
function lingo_display_info($info) {
    
    ?>
        <div class="lingo-info lingo-flash">
            <i class="fa fa-info-circle" aria-hidden="true"></i>
            <?php esc_attr_e($info, 'lingo'); ?>
        </div>    
    <?php
    
}

/**
 * Displays error message
 * @param string $error
 */
function lingo_display_error($error) {
    
    ?>
        <div class="lingo-error lingo-flash">
            <i class="fa fa-times-circle" aria-hidden="true"></i>
            <?php esc_attr_e($error, 'lingo'); ?>
        </div>    
    <?php
    
}

/**
 * Displays success message
 * @param string $success
 */
function lingo_display_success($success) {
    
    ?>
        <div class="lingo-success lingo-flash">
            <i class="fa fa-check-circle" aria-hidden="true"></i>
            <?php esc_attr_e($success, 'lingo'); ?>
        </div>    
    <?php
    
}

/**
 * Displays flash message based on transient
 */
function lingo_display_flash() {

    $status = get_transient('lingo_flash_status');
    $msg = get_transient('lingo_flash_msg');
    
    switch($status) {
        case 'ok':
            lingo_display_success($msg);
            break;
        case 'error':
            lingo_display_error($msg);
            break;
        case 'info':
            lingo_display_info($msg);
            break;
        case 'warning':
            lingo_display_warning($msg);
            break;
    }
        
    delete_transient("lingo_flash_status");
    delete_transient("lingo_flash_msg");
}

/**
 * Return array of all forums
 * 
 * @return array
 */
function lingo_get_all_forums() {
    
    $allForums = get_terms( array(
        'taxonomy' => 'lingo_forum_group',
        'hide_empty' => false,
    ) );

    $forums = new stdClass();

    foreach($allForums as $f) {
        if($f->parent == 0) {
            $meta = get_option( "taxonomy_$f->term_id");

            $node = new stdClass();
            $node->name = $f->name;
            $node->slug = $f->slug;
            $node->term_id = $f->term_id;
            $node->order = $meta['lingo_forum_order'];
            $node->childs = array();

            $forums->{$f->term_id} = $node;
        }
    }

    foreach($allForums as $f) {
        if($f->parent != 0) {
            $meta = get_option( "taxonomy_$f->term_id");

            $f->order = $meta['lingo_forum_order'];
            $f->status = $meta['lingo_forum_status'];
            $forums->{$f->parent}->childs[] = $f;
        }
    }

    return (array)$forums;
}

/**
 * Generates direct link to selected post
 * 
 * @param int $topic_id
 * @param int $post_id
 */
function lingo_make_go_to_link($topic_id, $post_id) {
    
    $args = apply_filters( "lingo_post_query", array( 
        'post_type'         => 'lingo_post', 
        'post_status'       => 'publish',
        'posts_per_page'    => -1, 
        'post_parent'       => $topic_id,
        'orderby'           => array( 'menu_order' => 'DESC', 'date' => lingo_config( 'config.posts_order' ) ),
    ));
    
    $posts = new WP_Query( $args );
    
    $i = 0;
    $on_page = lingo_config( 'config.posts_on_page' );
    $pg = 1;
    
    if($posts->have_posts()) {
        while($posts->have_posts()) {
            $posts->the_post();
            $i++;
            
            if(get_the_ID() == $post_id) {
                break;
            }
            
            if($i % $on_page == 0) {
                $pg++;
            }   
        }
    }  
    
    $url = get_the_permalink($topic_id);
    $url .= "?pg=" . $pg;
    $url .= "#lingo-post-" . $post_id;
    
    ob_start() ?>
        <a href="<?php echo esc_url($url); ?>">
            <i class="fa fa-sign-in" aria-hidden="true"></i> 
            <?php _e("Go to post", "lingo"); ?>
        </a>
    <?php $echo = ob_get_clean();
        
    echo $echo;
}

/**
 * Checks if forum have any new post to read for current user
 * 
 * @param int $forum_id
 * @return boolean
 */
function lingo_forum_have_new_posts($forum_id) {
    
    if(!is_user_logged_in()) {
        return false;
    }
    
    if(get_transient("lingo_new_posts") === null) {
        return false;
    } 
    
    $posts_to_read = json_decode(get_transient("lingo_new_posts"), true);
    
    if(is_array($posts_to_read)) {
        if(array_key_exists($forum_id, $posts_to_read)) {
            return true;
        }
    }
  
    return false; 
}

/**
 * Checks if topic have any new post to read for current user
 * 
 * @param int $topic_id
 * @return boolean
 */
function lingo_topic_have_new_posts($topic_id) {
    
    if(!is_user_logged_in()) {
        return false;
    }
    
    if(get_transient("lingo_new_posts") === null) {
        return false;
    } 
    
    $posts_to_read = json_decode(get_transient("lingo_new_posts"), true);
    
    foreach($posts_to_read as $k => $forum) {
        if(array_key_exists($topic_id, $forum)) {
            return true;
        }
    }

    return false; 
}

/**
 * Check if given post is new to read for current user
 * @param int $post_id
 * @return boolean
 */
function lingo_if_new_post($post_id) {
    
    if(!is_user_logged_in()) {
        return false;
    }
    
    if(get_transient("lingo_new_posts") === null) {
        return false;
    } 
    
    $posts_to_read = (array)json_decode(get_transient("lingo_new_posts"), true);

    $fr = null; $tr = null; $pr = null;
    $return = false; 
    
    foreach($posts_to_read as $fk => $forum) {
        foreach($forum as $tk => $topic) {
            if(($pr = array_search($post_id, $topic)) !== false) {
                $fr = $fk;
                $tr = $tk; 
                $return = true;
                break;
            }
        }
        
        if($return) {
            break;
        }
    }
    
    if($return) {
        unset($posts_to_read[$fr][$tr][$pr]);
        if(count($posts_to_read[$fr][$tr]) == 0) {
            unset($posts_to_read[$fr][$tr]);
            if(count($posts_to_read[$fr]) == 0) {
                unset($posts_to_read[$fr]);
            }
        }
        set_transient( "lingo_new_posts", json_encode($posts_to_read), 30 * DAYS_IN_SECONDS);
    }
    
    return $return;
}

/**
 * Displays author of the post based on post_id
 * 
 * @param int $post_id
 */
function lingo_post_author($post_id)
{
    $user = get_user_by('id', get_post_field( 'post_author', $post_id ));
    
    if($user->ID > 0) {
        ?> <a href="<?php echo esc_url(get_page_link(lingo_config('config.user_page_id'))); ?>?id=<?php echo esc_attr($user->ID); ?>">
            <?php if(strlen($user->first_name . " " . $user->last_name) > 1): ?>
                <?php esc_attr_e($user->first_name . " " . $user->last_name); ?>
            <?php else: ?>
                <?php esc_attr_e($user->user_nicename); ?>
            <?php endif; ?>
        </a> <?php
    } elseif($nickname = get_post_meta( get_the_ID(), 'lingo_guest_nickname', true)) {
        esc_attr_e($nickname);
    } else {
        _e("Guest", "lingo"); 
    }
}

function lingo_get_post_author($post_id) {
    
    $user = get_user_by('id', get_post_field( 'post_author', $post_id ));
    $return = __("Guest", "lingo");
    
    if($user->ID > 0) {
        if(strlen($user->first_name . " " . $user->last_name) > 1) {
            $return = esc_attr($user->first_name . " " . $user->last_name);
        } else {
            $return = esc_attr($user->user_nicename);
        }
    } elseif($nickname = get_post_meta( get_the_ID(), 'lingo_guest_nickname', true)) {
        $return = esc_attr($nickname);
    } 
    
    return $return;
}

/**
 * Gets number of post for selected topic
 * 
 * @param int $topic_id
 * @return int
 */
function lingo_get_posts_count($topic_id) {
    
    $args = apply_filters( "lingo_post_query", array( 
        'post_type'         => 'lingo_post', 
        'post_status'       => 'publish',
        'posts_per_page'    => -1, 
        'post_parent'       => $topic_id,
    ));
    
    $posts = new WP_Query($args);
    
    return $posts->post_count;
}

/**
 * Checks if user have moderator status
 * @return boolean
 */
function lingo_user_is_moderator() {
    
    if(current_user_can('edit_others_lingo_moderators')) {
        return true;
    }
    
    return false;
}

/**
 * Creates url to last post in topic
 * @param int $topic_id
 * @return string
 */
function lingo_get_last_post_url($topic_id) {
    
    $args = apply_filters( "lingo_post_query", array(
        'post_parent' => $topic_id,
        'post_type'   => 'lingo_post', 
        //'numberposts' => 1,
        'post_status' => array('publish', 'closed'),
    )); 
    
    $posts = get_children($args);    
    $page = ceil(count($posts) / lingo_config('config.posts_on_page'));
    
    $post_id = key($posts);

    return get_post_permalink($topic_id) . "?pg=" . $page . "#lingo-post-" . $post_id;
}

/**
 * Creates url to first unread post in topic
 * @param int $topic_id
 * @return boolean
 */
function lingo_get_first_unread_post_url($topic_id) {
    
    if(!is_user_logged_in()) {
        return false;
    }
    
    if(get_transient("lingo_new_posts") === null) {
        return false;
    } 
    
    $posts_to_read = (array)json_decode(get_transient("lingo_new_posts"), true);
    $forum_id = lingo_get_topic_forum($topic_id);
    
    if(!(count($posts_to_read) > 0) || !(count($posts_to_read[$forum_id]) > 0)) {
        return false;
    }
    
    $posts = $posts_to_read[$forum_id][$topic_id];
    
    if(!(count($posts) > 0)) {
        return false;
    }
    
    $first_post_id = $posts[0]; 
    foreach($posts as $p_id) {
        $p = (array)get_post($p_id);
        $first_post = (array)get_post($first_post_id);
        
        if(strtotime($p["post_date"]) < strtotime($first_post["post_date"])) {
            $first_post_id = $p_id;
        }
    }

    
    $args = apply_filters( "lingo_post_query", array(
        'post_parent' => $topic_id,
        'post_type'   => 'lingo_post', 
        //'numberposts' => 1,
        'post_status' => array('publish', 'closed'),
        'orderby' => 'post_date',
        'order' => 'ASC'
    )); 
    
    $page = 0; $counter = -1;
    $topic_posts = get_children($args); 
    foreach($topic_posts as $id => $p) { 
        $counter++;
        
        if($counter % lingo_config('config.posts_on_page') == 0) {
            $page++;
        }
        
        if($first_post_id == $id) {
            break;
        }  
    }
    
    return get_post_permalink($topic_id) . "?pg=" . $page . "#lingo-post-" . $first_post_id;
}

/**
 * Get forum object for given topic ID
 * @param int $topic_id
 * @return WP_Taxonomy
 */
function lingo_get_topic_forum($topic_id) {
    $forum = wp_get_post_terms($topic_id, 'lingo_forum_group', array("fields" => "ids"));
    return $forum[0];
}

/**
 * Check if user is subscribing topic
 * @param int $topic_id
 * @return boolean
 */
function lingo_user_is_subscribing($topic_id) {
    
    $subscriptions = get_post_meta($topic_id, 'lingo_subscription', true);
    
    if(!is_user_logged_in()) {
        return false;
    }
        
    return (strpos($subscriptions, wp_get_current_user()->user_email) !== false);
}

/**
 * Hides media that not belongs to user
 * @global WP_User $current_user
 * @param string $where
 * @return string
 */
function lingo_hide_attachments_wpquery_where( $where ){
    global $current_user;
    if( !current_user_can( 'manage_options' ) ) {
        if( is_user_logged_in() ){
            if( isset( $_POST['action'] ) ){
                // library query
                if( $_POST['action'] == 'query-attachments' ){
                    $where .= ' AND post_author='.$current_user->data->ID;
                }
            }
        }
    }
    return $where;
}

/**
 * Hides media that not belongs to user
 * @global WP_Page $pagenow
 * @global int $user_ID
 * @param string $query
 * @return string
 */
function lingo_hide_posts_media($query) {
    global $pagenow;
    if( ( 'edit.php' != $pagenow && 'upload.php' != $pagenow   ) || !$query->is_admin ){
        return $query;
    }
    if( !current_user_can( 'manage_options' ) ) {
        global $user_ID;
        $query->set('author', $user_ID );
    }
    return $query;
}

/**
 * Counts unreaded posts
 * @return int
 */
function lingo_count_unread() {
    
    if(!is_user_logged_in()) {
        return 0;
    }
    
    $ids = array();
    $posts_to_read = json_decode(get_transient("lingo_new_posts"), true);
    if($posts_to_read !== false) {
        foreach($posts_to_read as $ptr_forum => $ptr_topics) {
            foreach($ptr_topics as $ptr_topic => $ptr_posts) {
                foreach($ptr_posts as $post_id) {
                    //var_dump($post_id);
                    $ids[] = $post_id;
                }
            }
        } 
    }
    
    return count($ids);
}
