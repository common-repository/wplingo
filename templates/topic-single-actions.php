<div class="lingo-topic-actions lingo-topic-actions-<?php echo $section; ?>">
    <?php $closed = false; ?>
    <?php if($topic_status == 'closed' || $forumStatus == 1): ?>
        <?php $closed = true; ?>
    <?php endif; ?>
    
    <?php if($closed && $section == 0): ?>
        <?php if($forumStatus == 1): ?>
            <?php lingo_display_error(__('Forum is closed.', 'lingo')); ?>
        <?php else: ?>
            <?php lingo_display_error(__('Topic is closed.', 'lingo')); ?>
        <?php endif; ?>
        <?php $closed = true; ?>
    <?php elseif(lingo_config( 'config.only_signed_can_post' ) && !is_user_logged_in() && $section == 0): ?>
        <?php lingo_display_warning(__('Only signed users can add new posts.', 'lingo')); ?>
    <?php endif; ?>
    
    <div class="lingo-action-buttons">
    <?php if(!$closed): ?> 
        <?php if(!(lingo_config( 'config.only_signed_can_post' ) && !is_user_logged_in())): ?>
            <a href="" class="lingo_button lingo-button-reply" data-section="<?php echo $section; ?>"><i class="fa fa-reply" aria-hidden="true"></i> <?php _e('Reply', 'lingo'); ?></a>
        <?php endif; ?>
    <?php endif; ?>
    <?php if(lingo_config('config.display_search')): ?>
        <a href="" class="lingo_button lingo-button-search" data-section="<?php echo $section; ?>"><i class="fa fa-search" aria-hidden="true"></i> <?php _e('Search', 'lingo'); ?></a>
    <?php endif; ?>
    <?php if(lingo_user_is_moderator()): ?>
        <a href="" class="lingo_button lingo-button-moderate" data-section="<?php echo $section; ?>"><i class="fa fa-wrench" aria-hidden="true"></i> <?php _e('Moderation', 'lingo'); ?></a>
    <?php endif; ?>
        <div class="lingo-dropdown-container">
        <a href="" class="lingo_button lingo-button-more" title="<?php _e("More...", "lingo"); ?>"><i class="fa fa-bars" aria-hidden="true"></i></a>
            <ul class="lingo-dropdown-menu">
                <li><a href="" class="lingo-more-action lingo-unsubscribe-topic" data-section="<?php echo $section; ?>" data-topic="<?php echo get_the_ID(); ?>" data-email="<?php echo wp_get_current_user()->user_email; ?>" <?php if(!lingo_user_is_subscribing(get_the_ID())): ?>style="display: none;" <?php endif; ?>><?php _e("Unsubscribe", "lingo"); ?></a></li>
                <li><a href="" class="lingo-more-action lingo-subscribe-topic" data-section="<?php echo $section; ?>" <?php if(lingo_user_is_subscribing(get_the_ID())): ?>style="display: none;"<?php endif; ?>><?php _e("Subscribe", "lingo"); ?></a></li>
            </ul>
        </div>
        <?php do_action("lingo_after_topic_actions"); ?>
    </div>
    
    <div class="lingo-pagination">
        <?php echo paginate_links( array(
            'base' => $result['paginate_base'],
            'format' => $result['paginate_format'],
            'current' => max( 1, $result['paged'] ),
            'total' => $result['posts']->max_num_pages,
            'prev_next' => false
        ) ); ?>
    </div>
</div>
