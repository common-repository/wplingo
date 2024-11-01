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
        <?php _e("Edit: " . $template["name"], "lingo") ?>
        <a href="<?php esc_attr_e( remove_query_arg( array( 'key' ) ) ) ?>" class="page-title-action"><?php _e("Go Back", "lingo"); ?></a>
    </h2>

    <?php lingo_admin_flash() ?>
    
    <form action="" method="post" class="lingo-form">
        <table class="form-table">
            <tbody>
            <?php echo esc_html(lingo_form_layout_config($form)); ?>
            </tbody>
        </table>

        
        <p class="submit">
            <input type="submit" value="<?php _e("Save", "lingo") ?>" class="button-primary" name="Submit"/>
        </p>

        
        <h3 style="border-bottom:1px solid #dfdfdf; line-height:1.4em; font-size:15px"><?php _e("Variables", "lingo"); ?></h3>
        <table class="wp-list-table widefat fixed striped" style="width: 50%; margin: auto;">
            <thead>
                <tr>
                    <th style="width: 30%;"><?php _e("Code", "lingo") ?></th>
                    <th><?php _e("Purpose", "lingo") ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 30%; font-weight: bold">{$topic_url}</td>
                    <td><?php _e("URL to topic with new post", "lingo") ?></td>
                </tr>
                <tr>
                    <td style="width: 30%; font-weight: bold">{$topic_title}</td>
                    <td><?php _e("Title topic", "lingo") ?></td>
                </tr>
                <tr>
                    <td style="width: 30%; font-weight: bold">{$message}</td>
                    <td><?php _e("New post content", "lingo") ?></td>
                </tr>
            </tbody>
        </table>
        
        
    </form>

</div>