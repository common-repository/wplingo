<div class="lingo-forum-actions">
    
    <?php if($forum_status == 1 && $section == 0): ?>
        <?php lingo_display_error(__("Forum is closed.", 'lingo')); ?>
    <?php elseif (lingo_config( 'config.only_signed_can_post' ) && !is_user_logged_in() && $section == 0): ?>
        <?php lingo_display_warning(__('Only signed users can add new topics.', 'lingo')); ?>
    <?php endif; ?>
    
    <div class="lingo-action-buttons">
        <?php if($forum_status != 1): ?>
            <?php if(!(lingo_config( 'config.only_signed_can_post' ) && !is_user_logged_in())): ?>
                <a href="<?php echo esc_url(get_the_permalink() . "?forum=" .$forum_id . "&action=add"); ?>" class="lingo_button"><i class="fa fa-comment-o" aria-hidden="true"></i> <?php _e('Add New Topic', 'lingo'); ?></a>
            <?php endif; ?>
        <?php endif; ?>
        <?php if(lingo_config('config.display_search')): ?>
            <a href="" class="lingo_button lingo-button-search" data-section="<?php echo $section; ?>"><i class="fa fa-search" aria-hidden="true"></i> <?php _e('Search', 'lingo'); ?></a>
        <?php endif; ?>
        <?php do_action("lingo_after_forum_single_actions"); ?>
    </div>

    <div class="lingo-pagination">
        <?php echo paginate_links( array(
            'base' => $topics['paginate_base'],
            'format' => $topics['paginate_format'],
            'current' => max( 1, $topics['paged'] ),
            'total' => $topics['posts']->max_num_pages,
            'prev_next' => false
        ) ); ?>
    </div>
</div>
