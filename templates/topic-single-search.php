<?php if(lingo_config('config.display_search')): ?>
<div class="lingo-forum-actions lingo-forum-search-<?php echo $section; ?> lingo-action-hidden">
    <form action="<?php echo esc_url(get_page_link(lingo_config('config.search_page_id'))); ?>" method="GET" class="lingo_search_from">
        <input type="text" name="lingo_search" id="lingo_search" placeholder="<?php _e("Search in this topic...", "lingo"); ?>" />
        <input type="hidden" name="lingo_search_topic" id="lingo_search_topic" value="<?php the_title() ?>" />
        <?php $term = wp_get_post_terms(get_the_ID(), 'lingo_forum_group')[0]; ?>
        <input type="hidden" name="lingo_search_forum" id="lingo_search_forum" value="<?php echo esc_attr($term->term_id); ?>" />
        <input type="submit" value="<?php _e("Search", "lingo"); ?>" />
    </form>
</div>
<?php endif; ?>