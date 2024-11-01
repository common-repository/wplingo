<?php if(lingo_config( 'config.only_signed_can_see' ) && !is_user_logged_in()): ?>
    <?php lingo_display_warning(__('Only signed users can see forum.', 'lingo')); ?>
<?php else: ?>

    <?php echo esc_html(lingo_display_flash()); ?>

    <?php $section = 0; ?>

    <?php do_action("lingo_template_before_single_forum"); ?>

    <?php include LINGO_PATH . 'templates/breadcrumbs.php'; ?>

    <?php include LINGO_PATH . 'templates/forum-single-actions.php'; ?>

    <?php include LINGO_PATH . 'templates/forum-single-search.php'; ?>

    <div class="lingo-list">
        <?php if($topics['posts']->have_posts()): ?>
            <div class="lingo-forum-group lingo-list-no-border">
                <div class="lingo-forum-header">
                    <!--div class="lingo-header lingo-forum-header-image"></div-->
                    <div style="width: 75%;" class="lingo-header lingo-forum-header-title"><?php _e("Topics", "lingo"); ?></div>
                    <!--div class="lingo-header lingo-forum-header-stats"><?php _e("Stats", "lingo"); ?></div-->
                    <div class="lingo-header lingo-forum-header-activity"><?php _e("Last post", "lingo"); ?> </div>
                </div>
                <?php $i = 0; ?>
                <?php while($topics['posts']->have_posts()): $topics['posts']->the_post() ?>
                    <?php $result = lingo_get_topic_posts(get_the_ID()); ?>
                    <?php $user = get_user_by('id', get_the_author_id()); ?>
                    <?php include apply_filters( "lingo_template_load", LINGO_PATH . 'templates/topic-item.php' ) ?>
                    <?php $i++; ?>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="lingo-list-empty">
                <em><?php _e("No topics found", "lingo") ?></em>
            </div>
        <?php endif; ?>

        <?php wp_reset_query(); ?>
    </div>

    <?php $section++; ?>

    <?php include LINGO_PATH . 'templates/forum-single-search.php'; ?>

    <?php include LINGO_PATH . 'templates/forum-single-actions.php'; ?>

    <?php include LINGO_PATH . 'templates/breadcrumbs.php'; ?>

    <?php do_action("lingo_template_after_single_forum"); ?>

<?php endif; ?>