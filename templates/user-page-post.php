<div class="lingo-post-wrapper">
    <div class="lingo-post-content">
        <div class="lingo-post-meta">
            <div class="lingo-post-metadata">
                <i class="fa fa-calendar " aria-hidden="true" title="<?php echo esc_attr(date("H:i:s - d-m-Y", get_post_time())) ?>"></i>
                <?php esc_attr_e(human_time_diff(get_post_time(), time())); ?>
                <?php _e('ago', 'lingo'); ?>
            </div>      
        </div>
        
        <div class="lingo-post-message">
            <?php the_content(); ?>
        </div>
    </div>
</div>