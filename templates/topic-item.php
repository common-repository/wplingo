<div class="lingo-forum-list-item <?php echo ($i % 2 == 0) ? "lingo-even" : "lingo-odd"; ?>">
    <div class="lingo-column lingo-forum-image <?php if(lingo_topic_have_new_posts(get_the_ID())): ?> lingo-new-posts <?php endif; ?>">
        
        <?php $url_title = sprintf(__("Post Author: %s", "lingo"), lingo_get_post_author(get_the_ID())); ?>
        <?php $url_class = "lingo-topic-item-avatar"; ?>
        <?php if(get_post_status(get_the_ID()) == 'closed') {
            $url_class .= " lingo-topic-status-icon-closed";
        } ?>
        <div class="lingo-image-wrapper">
            <img src="<?php echo esc_url(get_avatar_url($user->ID, array('size' => 32))) ?>" title="<?php echo $url_title  ?>" class="<?php echo $url_class; ?>" /> 
            <?php if(get_post_status(get_the_ID()) == 'closed'): ?>
            <!--span class="dashicons dashicons-lock lingo-topic-status-icon lingo-red" title="<?php _e("Topic is closed", "lingo"); ?>"></span-->
            <i class="fa fa-lock lingo-topic-status-icon lingo-red" aria-hidden="true" title="<?php _e("Topic is closed", "lingo"); ?>"></i>
            <?php endif; ?>
        </div>
    </div>
    
    <?php lingo_get_last_post_url(get_the_ID()) ?>
    
    <div class="lingo-column lingo-forum-title lingo lingo-grid <?php if(lingo_topic_have_new_posts(get_the_ID())): ?> lingo-new-posts <?php endif; ?>">
        <div class="lingo-topic-wrapper">
            <div class="lingo-forum-description lingo-half lingo-grid-70">
                <?php if(lingo_topic_have_new_posts(get_the_ID())): ?>
                <a href="<?php echo lingo_get_first_unread_post_url(get_the_ID()); ?>" title="Go To First Unread" class="lingo-first-unread-link"><i class="fa fa-sign-in" aria-hidden="true"></i></a> 
                <?php else: ?>
                <a href="<?php echo lingo_get_last_post_url(get_the_ID()); ?>" title="Go To Last Post" class="lingo-last-post"><i class="fa fa-sign-in" aria-hidden="true"></i></a>
                <?php endif; ?>
                &nbsp;
                <a href="<?php the_permalink() ?>" title="<?php esc_attr_e( get_the_title() ) ?>">
                    <span title="<?php the_title() ?>" class="lingo-link"><?php the_title() ?></span> 
                </a>
                <div class="lingo-small-link">
                    <?php esc_attr_e(human_time_diff(get_post_time(), time())); ?>
                    <?php _e('ago', 'lingo'); ?>
                    <?php _e('by', 'lingo'); ?>
                    <?php lingo_post_author(get_the_ID()); ?>
                </div>
            </div>
            <div class="lingo-forum-pagination lingo-half lingo-force-right lingo-grid-30">
                <?php echo paginate_links( array(
                    'base' => $result['paginate_base'],
                    'format' => $result['paginate_format'],
                    'current' => 0,
                    'total' => $result['posts']->max_num_pages,
                    'prev_next' => false
                ) ); ?>  
            </div>
        </div>
       
        <div class="lingo-tags">
            <?php if(lingo_config( 'use_tags' ) == 1): ?>
                <?php $tags = get_the_terms( get_the_ID(), "lingo_topic_tag"); ?>
                <?php if(isset($tags) && !empty($tags)): ?>
                    Tags: 
                    <?php foreach($tags as $tag): ?>
                        <a class="lingo-tag" href="<?php echo get_term_link($tag); ?>"><?php echo $tag->name; ?></a>, 
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="lingo-column lingo-forum-stats">
        <?php _e("Posts:", "lingo"); ?> <?php echo esc_html(lingo_get_topic_posts_num(get_the_ID())); ?>
    </div>

    <div class="lingo-column lingo-forum-activity">
        <?php lingo_get_topic_last_post(get_the_ID()) ?>
    </div>
</div>