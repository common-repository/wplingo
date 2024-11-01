<form action="" method="GET">
    <div class="lingo-grid lingo-grid-closed-top">
        <div class="lingo-grid-row">
            <div class="lingo-grid-col lingo-grid-30"><label for="query"><?php _e("Post Query", "lingo"); ?></label></div>
            <div class="lingo-grid-col lingo-grid-70">
                <input type="text" name="query" id="query" placeholder="<?php _e("Search word...", "lingo"); ?>" value="<?php echo esc_attr($query); ?>" />
            </div>
        </div>
        <div class="lingo-grid-row">
            <div class="lingo-grid-col lingo-grid-30"><label for="topic"><?php _e("Search topic name", "lingo"); ?></label></div>
            <div class="lingo-grid-col lingo-grid-70">
                <input type="text" name="topic" id="topic" placeholder="<?php _e("Search in topic...", "lingo"); ?>" value="<?php echo esc_html($topic); ?>" />
            </div>
        </div>
        <div class="lingo-grid-row">
            <div class="lingo-grid-col lingo-grid-30"><label for="date_from"><?php _e("Created from", "lingo"); ?></label></div>
            <div class="lingo-grid-col lingo-grid-70">
                <input type="text" class="datepicker" name="date_from" id="date_from" placeholder="<?php _e("Posts created after...", "lingo"); ?>" value="<?php echo esc_html($date_from); ?>" />
            </div>
        </div>
        <div class="lingo-grid-row">
            <div class="lingo-grid-col lingo-grid-30"><label for="date_to"><?php _e("Created to", "lingo"); ?></label></div>
            <div class="lingo-grid-col lingo-grid-70">
                <input type="text" class="datepicker" name="date_to" id="date_to" placeholder="<?php _e("Posts created before...", "lingo"); ?>" value="<?php echo esc_html($date_to); ?>" />
            </div>
        </div>
        <?php if(is_user_logged_in()): ?>
        <div class="lingo-grid-row">
            <div class="lingo-grid-col lingo-grid-30"><label for="show_unread"><?php _e("Show only unread", "lingo"); ?></label></div>
            <div class="lingo-grid-col lingo-grid-70">
                <input type="checkbox" name="show_unread" id="show_unread" value="1" <?php if($show_unread == 1): ?> checked="checked" <?php endif; ?> />
            </div>
        </div>
        <?php endif; ?>
        <div class="lingo-grid-row">
            <div class="lingo-grid-col lingo-grid-30"><?php _e("Search in forum", "lingo"); ?></div>
            <div class="lingo-grid-col lingo-grid-70">
                <ul class="lingo-no-decoration-ul">
                    <?php foreach($allForums as $pf): ?>
                    <li><?php echo esc_html($pf->name); ?></li>
                    <li>
                        <ul>
                        <?php foreach($pf->childs as $f): ?>
                            <li>
                                <label>
                                    <input type="checkbox" name="forum[]" id="forum" <?php if(in_array($f->term_id, $forum)): ?> checked="checked" <?php endif; ?> value="<?php echo esc_attr($f->term_id); ?>" /> 
                                    <?php echo esc_html($f->name); ?> 
                                </label>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="lingo-grid-row">
            <div class="lingo-grid-col lingo-grid-100">
                <input type="submit" value="<?php _e("Search", "lingo"); ?>" />
            </div>
        </div>
    </div>
</form>
    
<h3 id="lingo_post_section_header"><?php _e("Search Result", "lingo"); ?></h3>
<div class="lingo-user-page-posts">
    <div class="lingo-grid lingo-grid-closed-top">
        <?php if($posts->have_posts()): ?>
            <?php while ( $posts->have_posts() ) : $posts->the_post(); ?>
                <div class="lingo-grid-row lingo-grid-double-top">
                    <div class="lingo-grid-col lingo-grid-30"><?php _e("Topic", "lingo"); ?></div>
                    <div class="lingo-grid-col lingo-grid-70">
                        <i class="fa fa-folder" aria-hidden="true"></i>
                        <?php $topic_id = wp_get_post_parent_id( get_the_ID() ); ?>
                        <?php $tmpTopic = get_post($topic_id); ?>
                        <a href="<?php the_permalink($topic_id); ?>"><?php echo str_ireplace($topic, '<span class="lingo_search_result">'.$topic.'</span>', $tmpTopic->post_title); ?></a> |
                        <?php lingo_make_go_to_link($topic_id, get_the_ID()) ?>
                    </div>
                </div>
                <div class="lingo-grid-row">
                    <div class="lingo-grid-col lingo-grid-30"><?php _e("Post Date", "lingo"); ?></div>
                    <div class="lingo-grid-col lingo-grid-70">
                        <i class="fa fa-calendar" aria-hidden="true" title="<?php esc_html_e(get_the_date()); ?>"></i>
                        <?php echo esc_html(human_time_diff(strtotime(get_the_date()), time())); ?> <?php _e("ago", "lingo"); ?>
                    </div>
                </div>
                <div class="lingo-grid-row">
                    <div class="lingo-grid-col lingo-grid-30"><?php _e("Author", "lingo"); ?></div>
                    <div class="lingo-grid-col lingo-grid-70">
                        <i class="fa fa-user" aria-hidden="true"></i>
                        <a href="<?php echo esc_url(get_page_link(lingo_config('config.user_page_id'))."?id=". get_the_author_ID()); ?>"><?php lingo_post_author(get_the_ID()) ?></a>
                    </div>
                </div>
                <div class="lingo-grid-row">                   
                    <div class="lingo-grid-col lingo-grid-100"><?php echo str_ireplace($query, '<span class="lingo_search_result">'.$query."</span>", get_the_content()); ?></div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="lingo-list-empty">
                <em><?php _e("There is no posts for this query", "lingo") ?></em>
            </div>
        <?php endif; ?>
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
    
    <?php wp_reset_query(); ?>
</div>