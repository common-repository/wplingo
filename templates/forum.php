<?php if(lingo_config( 'config.only_signed_can_see' ) && !is_user_logged_in()): ?>
    <?php lingo_display_warning(__('Only signed users can see forum.', 'lingo')); ?>
<?php else: ?>

    <?php echo esc_html(lingo_display_flash()); ?>

    <?php do_action("lingo_template_before_forum"); ?>

    <?php include LINGO_PATH . 'templates/forum-search.php'; ?>

    <?php $unread_posts = lingo_count_unread(); ?>
        <?php if($unread_posts > 0): ?>
            <a class="lingo_button" href="<?php echo esc_url(get_page_link(lingo_config('config.search_page_id'))); ?>/?show_unread=1">
                <?php _e("Show all unread posts", "lingo"); ?> 
                <span style="font-weight: bold;">(<?php echo $unread_posts; ?>)</span>
            </a>
    <?php endif; ?>

    <div class="lingo-list">
        <?php if(isset($forums) && !empty($forums)): ?>
        <?php foreach($forums as $forum): ?>
            <?php if(count($forum->childs) > 0): ?>
                <div class="lingo-forum-group lingo-list-no-border">
                    <div class="lingo-forum-header">
                        <!--div class="lingo-header lingo-forum-header-image"></div-->
                        <div style="width: 75%;" class="lingo-header lingo-forum-header-title"><?php esc_attr_e( $forum->name ) ?></div>
                        <!--div class="lingo-header lingo-forum-header-stats"><?php _e("Stats", "lingo"); ?></div-->
                        <div class="lingo-header lingo-forum-header-activity"><?php _e("Last post", "lingo"); ?> </div>
                    </div>
                    <?php $i = 0; ?>
                    <?php foreach($forum->childs as $subForum): ?>
                        <?php include apply_filters( "lingo_template_load_topic", LINGO_PATH . 'templates/forum-item.php' ) ?>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php else: ?>
            <div class="lingo-list-empty">
                <em><?php _e("No forums found", "lingo") ?></em>
            </div>
        <?php endif; ?> 

        <?php wp_reset_query(); ?>
    </div>

    <?php include LINGO_PATH . 'templates/forum-search.php'; ?>

    <?php do_action("lingo_template_after_forum"); ?>
<?php endif; ?>