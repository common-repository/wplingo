<?php
/**
 * Displays Core Options Page
 * 
 * This file is a template for wp-admin / Forums / Options / Core panel. 
 * 
 * It is being loaded by adext_core_page_options function.
 * 
 * @see adext_core_page_options()
 * @since 0.1
 */
?>
<div class="wrap">
    <h2 class="">
        <?php esc_html_e($page_title) ?>
    </h2>

    <?php lingo_admin_flash() ?>

    <form action="" method="post" class="lingo-form">
        <table class="form-table">
            <tbody>
            <?php echo esc_html(lingo_form_layout_config($form)); ?>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" value="<?php esc_attr_e($button_text) ?>" class="button-primary" name="Submit"/>
        </p>

    </form>

</div>
