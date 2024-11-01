<?php if(lingo_config('config.display_search')): ?>
<div class="lingo-forum-actions">
    <?php if(lingo_config( 'config.display_search' )): ?>
        <form action="<?php echo esc_url(get_page_link(lingo_config('config.search_page_id'))); ?>" method="GET" class="lingo_search_from">
            <input type="text" name="lingo_search" id="lingo_search" placeholder="<?php _e("Search in forum...", "lingo"); ?>" />
            <input type="submit" value="<?php _e("Search", "lingo"); ?>" />
        </form>
    <?php endif; ?>
</div>
<?php endif; ?>