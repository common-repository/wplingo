<?php if(lingo_config('config.display_search')): ?>
<div class="lingo-forum-actions lingo-forum-search-<?php echo $section; ?> lingo-action-hidden">
    <?php if(lingo_config( 'config.display_search' )): ?>
        <form action="<?php echo esc_url(get_page_link(lingo_config('config.search_page_id'))); ?>" method="GET" class="lingo_search_from">
            <input type="text" name="lingo_search" id="lingo_search" placeholder="<?php _e("Search this forum...", "lingo"); ?>" />
            <input type="hidden" name="lingo_search_forum" id="lingo_search_forum" value="<?php echo esc_attr(intval(lingo_request("forum"))) ?>" />
            <input type="submit" value="<?php _e("Search", "lingo"); ?>" />
        </form>
    <?php endif; ?>
</div>
<?php endif; ?>