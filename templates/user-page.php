<?php if(!is_numeric($user->ID)): ?>
<div class="lingo-list-empty">
    <em><?php _e("You need to provide valid user ID to view this page.", "lingo") ?></em>
</div>
<?php else: ?>

<div class="lingo-user-page-header">
    <div class="lingo-user-page-header-image">
        <img src="<?php echo esc_url(get_avatar_url($user->ID, array('size' => 74))) ?>" class="lingo-user-avatar" /> 
    </div>
    <div class="lingo-user-page-header-content">
        <span class="lingo-user-page-header-title"><?php echo esc_html($user_info->first_name . " " . $user_info->last_name); ?></span>
        <em class="lingo-user-page-header-subtitle"><?php _e("Registred:", "lingo"); ?> <?php echo esc_html(human_time_diff(strtotime($user->user_registered), time())); ?> <?php _e("ago", "lingo"); ?></em>
    </div>
</div>

<div class="lingo-grid lingo-grid-closed-top">
    <div class="lingo-grid-row">
        <div class="lingo-grid-col lingo-grid-30"><?php _e("User Name", "lingo"); ?></div>
        <div class="lingo-grid-col lingo-grid-70">
            <i class="fa fa-user" aria-hidden="true"></i>
            <?php echo esc_html($user_info->first_name . " " . $user_info->last_name); ?>
        </div>
    </div>
    <div class="lingo-grid-row">
        <div class="lingo-grid-col lingo-grid-30"><?php _e("E-mail", "lingo"); ?></div>
        <div class="lingo-grid-col lingo-grid-70">
            <i class="fa fa-envelope" aria-hidden="true"></i>
            <?php echo esc_html($user->user_email); ?>
        </div>
    </div>
    <div class="lingo-grid-row">
        <div class="lingo-grid-col lingo-grid-30"><?php _e("Website", "lingo"); ?></div>
        <div class="lingo-grid-col lingo-grid-70">
            <i class="fa fa-globe" aria-hidden="true"></i>
            <?php echo esc_html($user->user_url); ?>
        </div>
    </div>
    <div class="lingo-grid-row">
        <div class="lingo-grid-col lingo-grid-30"><?php _e("Posts", "lingo"); ?></div>
        <div class="lingo-grid-col lingo-grid-70">
            <i class="fa fa-comment" aria-hidden="true"></i>
            <?php echo esc_html($posts->post_count); ?>
        </div>
    </div>
    <div class="lingo-grid-row">
        <div class="lingo-grid-col lingo-grid-30"><?php _e("Created topics", "lingo"); ?></div>
        <div class="lingo-grid-col lingo-grid-70">
            <i class="fa fa-comments" aria-hidden="true"></i>
            <?php echo esc_html($topics->post_count); ?>
        </div>
    </div>
    
    <?php do_action("lingo_user_data", $user); ?>
</div>

<h3 id="lingo_post_section_header"><?php _e("User Posts", "lingo"); ?></h3>
<div class="lingo-user-page-posts">
    <div class="lingo-grid lingo-grid-closed-top">
    <?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
        <div class="lingo-grid-row lingo-grid-double-top">
            <div class="lingo-grid-col lingo-grid-30"><?php _e("Topic", "lingo"); ?></div>
            <div class="lingo-grid-col lingo-grid-70">
                <i class="fa fa-folder" aria-hidden="true"></i>
                <?php $topic_id = wp_get_post_parent_id( get_the_ID() ); ?>
                <?php $topic = get_post($topic_id); ?>
                <a href="<?php the_permalink($topic_id); ?>"><?php echo esc_html($topic->post_title); ?></a> | 
                <?php lingo_make_go_to_link($topic_id, get_the_ID()) ?>
            </div>
        </div>
        <div class="lingo-grid-row">
            <div class="lingo-grid-col lingo-grid-30"><?php _e("Post Date", "lingo"); ?></div>
            <div class="lingo-grid-col lingo-grid-70">
                <i class="fa fa-calendar" aria-hidden="true"></i>
                <?php echo esc_html(human_time_diff(strtotime(get_the_date()), time())); ?> <?php _e("ago", "lingo"); ?>
            </div>
        </div>
        <div class="lingo-grid-row">
            <div class="lingo-grid-col lingo-grid-100"><?php the_content(); ?></div>
        </div>
    <?php endwhile; ?>
    </div>
    <?php wp_reset_query(); ?>
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

<?php endif; ?>