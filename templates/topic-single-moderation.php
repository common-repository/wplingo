<?php if ( lingo_user_is_moderator() ): ?>
<div class="lingo-moderator-actions lingo-moderator-actions-<?php echo $section; ?> lingo-action-hidden">
    <a href="<?php echo esc_url(get_page_link(lingo_config('config.forum_id')). "?forum=" . $term->term_id . "&action=edit&topic_id=" . get_the_ID()) ?>" class="lingo_button lingo-edit-topic" data-id="<?php echo get_the_ID() ?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php _e("Edit Topic", "lingo"); ?></a>
    <a href="" class="lingo_button lingo-remove-topic" data-redirect="<?php echo esc_url(get_page_link(lingo_config('config.forum_id')) ."?forum=". $term->term_id); ?>" data-id="<?php echo get_the_ID() ?>" data-nonce="<?php echo wp_create_nonce( 'lingo-delete-topic-nonce' ) ?>"><i class="fa fa-times" aria-hidden="true"></i> <?php _e("Remove Topic", "lingo"); ?></a>
    <?php if(get_post_status(get_the_ID()) == 'publish'): ?>
        <a href="" class="lingo_button lingo-close-topic" data-id="<?php echo get_the_ID() ?>"><i class="fa fa-lock" aria-hidden="true"></i> <?php _e("Close Topic", "lingo"); ?></a>
    <?php else: ?>
        <a href="" class="lingo_button lingo-open-topic" data-id="<?php echo get_the_ID() ?>"><i class="fa fa-unlock-alt" aria-hidden="true"></i> <?php _e("Open Topic", "lingo"); ?></a>
    <?php endif; ?>
</div>
<?php endif; ?>