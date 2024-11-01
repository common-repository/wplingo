<div class="lingo-post-wrapper" id="lingo-post-<?php echo esc_attr(get_the_ID()); ?>">
    <?php $is_new = lingo_if_new_post(get_the_ID()); ?>
    <div class="lingo-post-author <?php if($is_new): ?> lingo-new-posts <?php endif; ?>">
        
        <img src="<?php echo esc_url(get_avatar_url($user->ID, array('size' => 64))) ?>" class="lingo-user-avatar" /> <br/>
        <?php lingo_post_author(get_the_ID()); ?>
        <?php do_action("lingo_after_user_data"); ?>
    </div>
    <div class="lingo-post-content">
        <div class="lingo-post-meta">
            <div class="lingo-post-metadata">
                
                <!--i title="<?php echo ($is_new) ? __("Post is new", "lingo") : __("Post is old", "lingo") ?>" class="fa fa-square <?php if($is_new): ?> lingo-new-posts <?php endif; ?>" aria-hidden="true"></i-->
                <i class="fa fa-calendar " aria-hidden="true" title="<?php echo esc_attr(date("H:i:s - d-m-Y", get_post_time())); ?>"></i>
                <?php esc_attr_e(human_time_diff(get_post_time(), time())); ?>
                <?php _e('ago', 'lingo'); ?>
                <?php do_action("lingo_after_user_meta"); ?>
            </div>
            
            <div class="lingo-post-admin">
                <?php if ( lingo_user_is_moderator() ): ?>
                <a href="#" class="lingo-post-edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php _e("Edit", "lingo") ?></a> 
                <a href="#" class="lingo-edit-cancel" data-id="<?php echo get_the_ID(); ?>" style="display: none;"><i class="fa fa-times" aria-hidden="true"></i> <?php _e("Cancel", "lingo") ?></a> |
                <a href="#" data-nonce="<?php echo wp_create_nonce( 'lingo-delete-post-nonce' ) ?>" data-id="<?php echo get_the_ID(); ?>" class="lingo-red lingo-remove-post"><i class="fa fa-trash" aria-hidden="true"></i> Remove</a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="lingo-post-message">
            <?php echo get_post_field('post_content', get_the_ID()); ?>
        </div>
        <?php if ( lingo_user_is_moderator() ): ?>
        <div class="lingo-post-message-edit" style="display: none;">
            <?php wp_editor( get_the_content() , 'lingo_edit_post_message_'.get_the_ID(), array('textarea_rows' => 15) ); ?> <br/>
            <input type="hidden" value="<?php echo get_the_ID() ?>" name="lingo-edit-post-id" id="lingo-edit-post-id" />
            <input type="submit" value="<?php _e('Save', 'lingo'); ?>" class="button button-primary button-large lingo-edit-post-save" />
            <a href="" data-id="<?php echo get_the_ID(); ?>" class="button button-large lingo-inner-edit-cancel"> <?php _e("Cancel", "lingo"); ?> </a>
        </div>
        <?php endif; ?>
        
        <?php do_action("lingo_after_post_content"); ?>
    </div>
</div>