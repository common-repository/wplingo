<div class="lingo-forum-list-item <?php echo ($i % 2 == 0) ? esc_attr("lingo-even") : esc_attr("lingo-odd"); ?>">
    <div class="lingo-column lingo-forum-image <?php if(lingo_forum_have_new_posts($subForum->term_id)): ?> lingo-new-posts <?php endif; ?>">
        <?php if($subForum->status != 1): ?>
            <i title="<?php _e("Froum is open", "lingo") ?>" class="fa fa-square fa-3x" aria-hidden="true"></i>
        <?php else: ?>
            <i title="<?php _e("Topic is closed", "lingo") ?>" class="fa fa-minus-square fa-3x" aria-hidden="true"></i>
        <?php endif; ?>
    </div>
    
    <div class="lingo-column lingo-forum-title">
        <a href="<?php echo esc_url(get_the_permalink() . "?forum=" . $subForum->term_id); ?>" >
            <span title="<?php esc_attr_e( $subForum->name ) ?>" class="lingo-link"><?php esc_attr_e( $subForum->name ) ?></span>
        </a>
        
        <div class="lingo-forum-description">
            <?php esc_attr_e($subForum->description); ?>
        </div>
    </div>
    
    <div class="lingo-column lingo-forum-stats">
        <?php lingo_get_forum_stats($subForum->term_id) ?>
    </div>

    <div class="lingo-column lingo-forum-activity">
        <?php lingo_get_forum_last_post($subForum->term_id) ?>
    </div>
</div>