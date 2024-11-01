<?php lingo_flash( $lingo_flash ) ?>

<?php if(lingo_config( 'config.only_signed_can_post' ) && !is_user_logged_in()): ?>
    <?php lingo_display_warning(__('Only signed users can add new posts.', 'lingo')); ?>
<?php else: ?>

<?php include LINGO_PATH . 'templates/breadcrumbs.php'; ?>

<form action="" method="post" class="lingo-form lingo-form-aligned">
    <fieldset>
        
        <?php foreach($form->get_fields( array( "type" => array( "lingo_field_hidden" ) ) ) as $field): ?>
        <?php call_user_func( lingo_field_get_renderer($field), $field) ?>
        <?php endforeach; ?>
        
        <?php foreach($form->get_fields() as $field): ?>
        <div class="lingo-control-group <?php esc_attr_e( str_replace("_", "-", $field["type"] ) . " lingo-field-name-" . $field["name"] ) ?> <?php if(lingo_field_has_errors($field)): ?>lingo-field-error<?php endif; ?>">
            
            <?php if($field["type"] == "lingo_field_header"): ?>
            <h3 style="border-bottom:2px solid silver"><?php esc_html_e($field["label"]) ?></h3>
            <?php else: ?>
            
                <label for="<?php esc_attr_e($field["name"]) ?>">
                    <?php esc_html_e($field["label"]) ?>
                    <?php if(lingo_field_has_validator($field, "is_required")): ?>
                    <span class="lingo-form-required lingo-red">*</span>
                    <?php endif; ?>
                </label>

                <?php call_user_func( lingo_field_get_renderer($field), $field) ?>

                 <?php if(isset($field['hint']) && !empty($field['hint'])): ?>
                    <br/><span class="description"><?php echo esc_html($field['hint']); ?></span>
                <?php endif; ?>

            <?php endif; ?>

            <?php if(lingo_field_has_errors($field)): ?>
            <ul class="lingo-field-error-list">
                <?php foreach($field["error"] as $k => $v): ?>
                <li><?php esc_html_e($v) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            
        </div>
        <?php endforeach; ?>
        
        <div  style="border-top:2px solid silver; padding: 1em 0 1em 0">

            <input type="submit" name="submit" value="<?php echo esc_attr($btn_label) ?>" style="font-size:1.2em" class="lingo-cancel-unload" />
        </div>
        
    </fieldset>
</form>

<?php if($redirect_url): ?>
<script type="text/javascript">
    window.location.replace("<?php echo esc_url($redirect_url); ?>");
</script>
<?php endif; ?>

<?php include LINGO_PATH . 'templates/breadcrumbs.php'; ?>

<?php endif; ?>