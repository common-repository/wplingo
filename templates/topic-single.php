<?php if(lingo_config( 'config.only_signed_can_see' ) && !is_user_logged_in()): ?>
    <?php lingo_display_warning(__('Only signed users can see forum.', 'lingo')); ?>
<?php else: ?>

    <?php echo esc_html(lingo_display_flash()); ?>

    <?php $topic_status = get_post_status(get_the_ID()); ?>
    <?php 
        $forum = wp_get_post_terms(get_the_ID(), "lingo_forum_group");
        $forum = $forum[0];
        $froumMeta = get_option( "taxonomy_$forum->term_id");
        $forumStatus = $froumMeta["lingo_forum_status"];
    ?>

    <?php $section = 0; ?>

    <?php do_action("lingo_template_before_topic"); ?>

    <?php $result = lingo_get_topic_posts(get_the_ID()); ?>

    <?php include LINGO_PATH . 'templates/breadcrumbs.php'; ?>

    <?php include LINGO_PATH . 'templates/topic-single-actions.php'; ?>

    <?php //include LINGO_PATH . 'templates/topic-single-reply.php'; ?>

    <?php include LINGO_PATH . 'templates/topic-single-moderation.php'; ?>

    <?php include LINGO_PATH . 'templates/topic-single-search.php'; ?>

    <?php include LINGO_PATH . 'templates/topic-single-subscribe.php'; ?>

    <?php if($result['posts']->post_count > 0): ?>
    <?php while($result['posts']->have_posts()): $result['posts']->the_post(); ?>  
        <?php $user = get_user_by('id', get_the_author_id()); ?>
        <?php include apply_filters( "lingo_template_load", LINGO_PATH . 'templates/post-single.php' ) ?>
    <?php endwhile; ?>
    <?php wp_reset_query(); ?>
    <?php else: ?>
        <?php lingo_display_warning(__('This topic have no posts', 'lingo')); ?>
    <?php endif; ?>

    <?php $section++; ?>

    <?php include LINGO_PATH . 'templates/topic-single-subscribe.php'; ?>

    <?php include LINGO_PATH . 'templates/topic-single-search.php'; ?>

    <?php include LINGO_PATH . 'templates/topic-single-moderation.php'; ?>

    <?php include LINGO_PATH . 'templates/topic-single-reply.php'; ?>

    <?php include LINGO_PATH . 'templates/topic-single-actions.php'; ?>

    <?php include LINGO_PATH . 'templates/breadcrumbs.php'; ?>

    <?php do_action("lingo_template_after_topic"); ?>
<?php endif; ?>
