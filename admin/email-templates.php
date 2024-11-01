<?php
/**
 * Displays WPLingo E-Mail Templates 
 * 
 * This file is a template for wp-admin / Forums / E-Mail Templates page. 

 * @since 1.1
 */
?>

<div class="wrap">
    <h2 class="">
        <?php _e("E-mail Templates", "lingo") ?>
    </h2>

    <?php lingo_admin_flash() ?>
    
    <ul>
        <?php foreach(lingo_email_template() as $key => $email): ?>
        <li class="lingo_config_box">     
            <a href="<?php esc_attr_e( add_query_arg( array( 'key'=>$key ) ) ) ?>"><?php esc_html_e($email["name"]) ?></a> <br/>
            <small><?php esc_html_e($email["description"]) ?></small>  
        </li>
        <?php endforeach; ?>
    </ul>

</div>