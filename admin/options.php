<?php
/**
 * Displays WPLingo Options Page
 * 
 * This file is a template for wp-admin / Forums / Options page. It is being loaded
 * by lingo_admin_page_extensions() function.
 * 
 * @see lingo_admin_page_extensions()
 * @since 0.1
 */
?>
<?php foreach($module_groups as $mg_name => $group): ?>
<h2><?php esc_html_e($group["title"]) ?></h2>

<div class="lingo-options">
    
    <?php foreach($group["modules"] as $key => $data): ?>
    <div class="lingo-options-wrap">
        <div class="lingo-options-item">
            <h3 class="icon verification-tools"><?php esc_html_e($data["title"]) ?></h3>
            <img style="box-sizing: border-box; width: 100%;" src="<?php echo esc_url(LINGO_URL . '/addons/'.$key.'/assets/img/options-img.jpg'); ?>" />
            <p><?php esc_html_e($data["text"]) ?></p>
        </div>
        <div class="lingo-options-actions">
            <?php if($data["type"]=="static"): ?>
            
                <em><?php _e("Cannot be disabled", "lingo") ?></em>
                <a href="<?php esc_attr_e( add_query_arg( array( 'module'=>$key ) ) ) ?>" class="button-primary"><?php _e("Settings") ?></a>

            <?php elseif($data["plugin"]): ?>
            
                <?php include_once ABSPATH . 'wp-admin/includes/plugin.php' ?>
            
                <?php if( is_plugin_active( $data["plugin"] ) ): ?>
                    <em><?php _e( "Addon Uploaded and Activated", "lingo") ?></em>
                    <a href="<?php esc_attr_e( add_query_arg( array( 'module'=>$key ) ) ) ?>" class="button-primary"><?php _e("Settings") ?></a>
                <?php elseif( lingo_plugin_uploaded( $data["plugin"] ) ): ?>
                    <em><?php _e( "Addon Uploaded but Inactive", "lingo") ?></em>
                    <a href="<?php esc_attr_e( admin_url( 'plugins.php?plugin_status=inactive' ) ) ?>" class="button-primary"><?php _e("Activate") ?></a>
                <?php else: ?>
                    <a href="<?php esc_attr_e( $data["purchase_url"]) ?>" class="button-secondary">
                        <strong><?php _e("Get This Addon", "lingo") ?></strong>
                        <span class="dashicons dashicons-cart" style="font-size:18px; line-height: 24px"></span>
                    </a>
                <?php endif; ?>
            
            <?php else: ?>
            
                <?php if(isset($module[$key])): ?>
                <a href="<?php esc_attr_e( add_query_arg( array( 'module'=>$key ) ) ) ?>" class="button-primary"><?php _e("Settings") ?></a>
                <a href="<?php esc_attr_e( add_query_arg( array( 'disable'=>$key, "noheader"=>1 ) ) ) ?>" class="button-secondary" style="margin-right:4px"><?php _e("Disable", "lingo") ?></a>
                <?php else: ?>
                <a href="<?php esc_attr_e( add_query_arg( array( 'enable'=>$key, "noheader"=>1 ) ) ) ?>" class="button-secondary"><?php _e("Enable") ?></a>
                <?php endif; ?>
            
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>

    
</div>
<?php endforeach; ?>