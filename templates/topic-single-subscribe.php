<div class="lingo-forum-actions lingo-forum-subscribe-<?php echo $section; ?> lingo-action-hidden lingo-action-hidden-window lingo-grid">
    <div class="lingo-grid-row lingo-removable-box-menu" >
        <div class="lingo-grid-col lingo-grid-50">
            <?php _e("Subscribe to this topic", "lingo"); ?>
        </div>
        <div class="lingo-grid-col lingo-grid-50" style="text-align: right;">
            <a href="" class="lingo-close-hidden"><i class="fa fa-times" aria-hidden="true"></i></a>
        </div>
    </div>
    
    <form action="" method="POST" class="lingo-subscription-save">
        <input type="text" name="lingo_subscribe_emial" id="lingo_subscribe_emial" placeholder="<?php _e("Provide your e-mail", "lingo"); ?>" <?php if(is_user_logged_in()): ?> value="<?php echo wp_get_current_user()->user_email; ?>" <?php endif; ?> />
        <input type="hidden" name="lingo_subscribe_topic_id" id="lingo_subscribe_topic_id" value="<?php echo get_the_ID() ?>" />
        <input type="hidden" name="lingo_section" id="lingo_section" value="<?php echo $section; ?>" />
        <input type="submit" value="<?php _e("Subscribe", "lingo"); ?>" />
    </form>
</div>