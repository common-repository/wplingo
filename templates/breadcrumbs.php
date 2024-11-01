<?php if(lingo_config('config.display_breadcrumbs')): ?>
    <?php wp_reset_query(); ?>
    <?php $breadcrumbs_id = get_the_ID(); $breadcrumbs_title = get_the_title(); ?>
    <div class="lingo-breadcrumbs">
        
        <?php // BASE URL ?>
        <a href="<?php echo esc_url(get_page_link(lingo_config('config.forum_id'))); ?>"><i class="fa fa-home" aria-hidden="true"></i></a> <i class="fa fa-chevron-right" aria-hidden="true"></i>
        
        
        
        <?php $term = wp_get_post_terms($breadcrumbs_id, 'lingo_forum_group')[0]; ?>
        <?php if($term != null): ?>
            <?php // INSIDE TOPIC! ?>
            <a href="<?php echo esc_url(get_page_link(lingo_config('config.forum_id')) . "?forum=" . $term->term_id); ?>"><?php echo esc_html($term->name); ?></a> <i class="fa fa-chevron-right" aria-hidden="true"></i> 
            <?php echo esc_html($breadcrumbs_title) ?>
        <?php else: ?>
            <?php if(is_numeric(intval(lingo_request("forum")))): ?>

                <?php $term = get_term( intval(lingo_request("forum")), 'lingo_forum_group' ); ?>
            
                <?php if(sanitize_text_field(lingo_request("action")) == "add"): ?>
                    <?php // ADD TOPIC FORM ?>
                    <a href="<?php echo esc_url(get_page_link(lingo_config('config.forum_id')) . "?forum=" . $term->term_id) ?>"><?php echo esc_html($term->name); ?></a> <i class="fa fa-chevron-right" aria-hidden="true"></i>
                    <?php _e("Add New Topic", "lingo"); ?>
                <?php else: ?>
                    <?php // SINGLE FORUM ?>
                    <?php echo esc_html($term->name); ?>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
                    
        <?php $topic = intval(lingo_request("topic")); ?>
        <?php if(is_numeric($topic) && $topic > 0): ?>
            <?php $term = wp_get_post_terms($topic, 'lingo_forum_group')[0]; ?>
            <a href="<?php echo esc_url(get_page_link(lingo_config('config.forum_id')). "?forum=" . $term->term_id) ?>"><?php echo esc_html($term->name); ?></a> <i class="fa fa-chevron-right" aria-hidden="true"></i> 
            <?php $bPost = get_post($topic); ?>
            <a href="<?php echo esc_url(get_page_link($bPost)); ?>"><?php echo esc_html($bPost->post_title); ?></a> <i class="fa fa-chevron-right" aria-hidden="true"></i>
            <?php _e("Add New Post", "lingo"); ?>
        <?php endif; ?>
        
    </div>
<?php endif; ?>