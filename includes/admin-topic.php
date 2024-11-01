<?php
/**
 * Topic Admin
 * 
 * Class handles functions to manage Lingo Topics in wp-admin
 *
 * @package     Lingo
 * @copyright   Copyright (c) 2016, Mark Winiarski
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

/**
 * Creates metaboxes in Lingo_Topic edit page
 * 
 * @return void
 */
function lingo_topic_add_metabox() {
    // Change excerpt box title
    global $wp_meta_boxes;
    $wp_meta_boxes['lingo_topic']['normal']['core']['postexcerpt']['title']= __('Topic Short Description', 'lingo');
    
    // Posts in Topic
    add_meta_box('lingo_topic_messages', __('Topic All Posts', 'lingo'), 'lingo_topic_metabox_messages', 'lingo_topic', 'advanced', 'default');
    // Add New Post
    add_meta_box('lingo_topic_new_message', __('Add New Post', 'lingo'), 'lingo_topic_metabox_new_message', 'lingo_topic', 'advanced', 'high');
    // Relocate author box
    add_meta_box('authordiv', __('Topic Author', 'lingo'), 'post_author_meta_box', 'lingo_topic', 'side', 'default');
    // Add Post Category
    add_meta_box('lingo_topic_forum_group', __('Topic Forum', 'lingo'), 'lingo_topic_metabox_forum_group', 'lingo_topic', 'side', 'default');    
}

/**
 * Remove meta boxes in Lingo_Topic edit page
 * 
 * @return void
 */
function lingo_topic_remove_metabox() { 
    remove_meta_box('lingo_forum_groupdiv', 'lingo_topic', 'normal');
    remove_meta_box('authordiv', 'lingo_topic', 'advanced');
    remove_meta_box('slugdiv', 'lingo_topic', 'advanced');
}

/**
 * Add forum group select into "publish" metabox
 * 
 * @global WP_Post $post
 * @return WP_Post
 */
function lingo_topic_metabox_forum_group() {
    global $post;
    
    if (get_post_type($post) != 'lingo_topic') {
        return $post;
    }
     
    $post_term = wp_get_post_terms($post->ID, "lingo_forum_group", true);
    $post_term = $post_term[0];
    $nonce = wp_create_nonce( 'lingo-forum-group-save' );

    $terms = get_terms( array(
                            'taxonomy' => 'lingo_forum_group',
                            'hide_empty' => false,
                        ));

    $taxonomies = array();
    foreach($terms as $term) {
        if($term->parent == 0) {
            $taxonomies[$term->term_id] = new stdClass();
            $taxonomies[$term->term_id]->title = $term->name;
            $taxonomies[$term->term_id]->childs = array();

        }
    }

    foreach($terms as $term) {
        if($term->parent != 0) {
            $taxonomies[$term->parent]->childs[$term->term_id] = $term->name;
        }
    }
    
    if(isset($post_term->name) && !empty($post_term->name)) {
        $post_name = $post_term->name;
        $term_id = $post_term->term_id;
    }
        

    ob_start() ?>

    <div class="lingo-success-small lingo-side-box-category" style="display: none;">
        <?php _e('New category has been created. Now you can add forum as child of category. Topics are displayed only in forums.', 'lingo'); ?>
    </div>

    <div class="lingo-success-small lingo-side-box-forum" style="display: none;">
        <?php _e('New forum have been created. You can add topic into it now.', 'lingo'); ?>
    </div>

    <p>
        <select id="lingo_forum_group" name="lingo_forum_group" class="lingo-full-width">
        <?php foreach($taxonomies as $tax): ?>
            <optgroup label="<?php echo esc_attr($tax->title); ?>">
                <?php foreach($tax->childs as $id => $title): ?>
                <option <?php if($term_id == $id): ?> selected="selected" <?php endif; ?> value="<?php echo esc_attr($id); ?>"><?php echo esc_html($title); ?></option>
                <?php endforeach; ?>
            </optgroup>
        <?php endforeach; ?>
        </select>
    </p>
    <p class="howto"><?php _e('You can add topic only to forum (child of global category).', 'lingo') ?></p>
    <input type="hidden" name="lingo_forum_group_noncename" id="lingo_forum_group_noncename" value="<?php echo esc_attr($nonce); ?>" />
    <p>
        <a href="" class="lingo-forum-add-new-button">+ <?php _e('Add New Forum', 'lingo'); ?></a>
    </p>
    
    <div class="lingo-forum-add-form" style="display: none;">
        <p>
            <input type="text" id="lingo_new_forum_name" name="lingo_new_forum_name" class="lingo-full-width" />
        </p>
        <p>
            <select id="lingo_new_forum_parent" name="lingo_new_forum_parent" class="lingo-full-width">
                <option value=""><?php _e('-- No Parent --', 'lingo'); ?></option>
                <?php foreach($terms as $term): ?>
                    <?php if($term->parent == 0): ?>
                        <option value="<?php echo esc_attr($term->term_id); ?>">
                            <?php echo esc_html($term->name); ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select> <br/>
        </p>
        <p class="howto">
            <?php _e('If you select "No Parent" you will create global category for forums.', 'lingo') ?>
        </p>
        <p>
            <a href="" class="button button-large lingo_add_new_forum_button"><?php _e('Add New Forum', 'lingo'); ?></a>
        </p>
    </div>

    <?php
    $select = ob_get_clean();

    echo $select;  
}

/**
 * Saves forum group for lingo_topic
 * 
 * @param int $post_id
 * @param WP_Post $post
 * @return int
 */
function lingo_topic_metabox_forum_group_save($post_id, $post) {
    
    if ( !wp_verify_nonce($_POST['lingo_forum_group_noncename'], 'lingo-forum-group-save' )) {
        return $post_id;
    }
    
    if ( !current_user_can('edit_post', $post_id )) {
        return $post_id;
    }
    
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return $post_id;
    }
    
    if($post->post_type != 'lingo_topic') {
        return $post_id;
    }
    

    $newForum = intval(lingo_request('lingo_forum_group'));
    
    if(isset($newForum) && !empty($newForum)) {
        wp_set_post_terms( $post_id, $newForum, 'lingo_forum_group', FALSE ); 
    }
    
    update_post_meta($post_id, "lingo_last_post_date", time());
}

/**
 * Create meta box for all messages (Lingo_Post) in Lingo_Topic edit page
 * 
 * @global WP_Post $post
 * @return void
 */
function lingo_topic_metabox_messages() {
    global $post;
    
    $nonce = wp_create_nonce( plugin_basename(__FILE__) );

    $args = apply_filters( "lingo_post_query", array(
            'post_parent' => $post->ID,
            'post_type'   => 'lingo_post', 
            'numberposts' => -1,
            'post_status' => 'publish' 
    )); 
    
    $published = get_children($args);
    $args['post_status'] = 'trash';
    $trash = get_children($args);
    
    $childs = array_merge($published, $trash);
    usort($childs, 'lingo_compare_posts');
    
    ob_start(); 
    ?>
    <input type="hidden" name="statusmeta_noncename" id="statusmeta_noncename" value="<?php echo esc_attr($nonce); ?>" />
    <?php foreach($childs as $child): ?>
    <?php $author = get_user_by('id', $child->post_author); ?>
    
        <?php if($child->post_status == 'publish'): ?>
        <div class="lingo-admin-post">
            <div class="lingo-admin-post-avatar">
                <?php echo get_avatar( $author->ID ); ?> 
                <a href="<?php echo get_edit_user_link($author->ID) ?>"> <b><?php echo esc_html($author->user_nicename); ?></b></a>
            </div>
            <div class="lingo-admin-post-rest">
                <div class="lingo-admin-post-meta">
                    <div class="lingo-admin-post-meta-data">
                        <span class="dashicons dashicons-calendar-alt" title="<?php echo $child->post_date; ?>"></span> 
                        <?php echo esc_html(human_time_diff(strtotime($child->post_date), time())); ?> 
                        <?php _e("ago", 'lingo'); ?> 
                    </div>
                    <div class="lingo-admin-post-meta-admin">
                        <a href="<?php echo esc_attr($child->ID) ?>" class="lingo-post-edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> <?php _e('Edit', 'lingo'); ?></a> |
                        <a href="#" data-id="<?php echo esc_attr($child->ID) ?>" data-nonce="<?php echo wp_create_nonce('lingo-delete-post-nonce') ?>" class="lingo-red lingo-delete-post"><i class="fa fa-trash" aria-hidden="true"></i> <?php _e('Trash', 'lingo'); ?></a> | 
                        <i class="fa fa-caret-up lingo-single-icon lingo-toggle-icon" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="lingo-admin-post-message lingo-post-<?php echo esc_attr($child->ID); ?>">
                    <span class="lingo-admin-post-content">
                        <?php $content = apply_filters('the_content', $child->post_content); ?>
                        <?php echo $content; ?>
                    </span>
                    <span class="lingo-admin-edit-post-form" style="display: none;">
                        <form action="" method="POST">
                            <?php wp_editor( $child->post_content, 'lingo_edit_post_message_'.$child->ID, array('textarea_rows' => 15) ); ?> <br/>
                            <input type="hidden" value="<?php echo esc_attr($child->ID); ?>" name="lingo-edit-post-id" id="lingo-edit-post-id" />
                            <input type="submit" value="<?php _e('Save', 'lingo'); ?>" class="button button-primary button-large lingo-edit-post-save" />
                            <a href="" class="button button-large lingo-add-cancel"> <?php _e("Cancel", "lingo"); ?> </a>
                        </form>  
                    </span>
                </div>
            </div>
        </div>
        <?php elseif($child->post_status == 'trash'): ?>
        <div class="lingo-admin-post lingo-admin-post-trash-wraper">
            <div class="lingo-admin-post-trash">   
                <span class="dashicons dashicons-trash"></span> Post moved to trash 
                <?php echo esc_html(human_time_diff(strtotime($child->post_modified), time())); ?> 
                <?php _e("ago", 'lingo'); ?>. |
                <a href="<?php echo esc_url(admin_url('admin-ajax.php') . "?action=lingo_post_preview&id=" . $child->ID); ?>" title="<?php _e('Preview Post', 'lingo') ?>" class="thickbox"><span class="dashicons dashicons-visibility"></span> Preview</a> | 
                <a href="<?php echo esc_url(admin_url('admin-ajax.php') . "?action=lingo_post_remove&id=" . $child->ID); ?>" title="<?php _e('Remove Post Permanently (not reversible)', 'lingo') ?>"class="thickbox lingo-trash-remove"><span class="dashicons dashicons-no"></span></span> Remove</a> | 
                <a href="#" data-id="<?php echo esc_attr($child->ID) ?>" title="<?php _e('Restore Post', 'lingo') ?>" class="lingo-trash-restore"><span class="dashicons dashicons-undo"></span> Restore</a> 
            </div>
        </div>
        <?php endif; ?>
    <?php endforeach;
    wp_reset_query();
    $echo = ob_get_clean();
    
    
    if(is_edit_page('edit') && count($childs) > 0) {
        echo $echo;  
    } else {
        echo '<div class="lingo_new_post_wraper">';
        _e('There is no posts in this topic.', 'lingo');
        echo '</div>';
    }
}

/**
 * Creates meta box with form to create new messages (Lingo_Post) in Lingo_Topic edit page
 * 
 * @global WP_Post $post
 * @return void
 */
function lingo_topic_metabox_new_message() {
    global $post; 

    ob_start(); 
    ?>
    
    <div class="lingo_new_post_wraper">
    <?php if(is_edit_page('edit')): ?>
        <a href="" title="<?php _e('Add New Post', 'lingo') ?>" class="/*lingo_new_post_button*/ lingo_add_new_post_button button"> <?php _e('Add New Post', 'lingo') ?> </a>
    <?php else: ?>
        <?php _e("You need to save topic, before you will be able to add posts into it.", 'lingo'); ?>
    <?php endif; ?>
    </div>
    
    <div class="lingo-new-post-form" style="display:none;">
        <form action="" method="POST">
            <p>
                <?php wp_editor('', 'lingo_new_post_content'); ?>
            </p>
            <p>
                <a href="#" data-parent="<?php echo esc_attr($post->ID) ?>" class="button button-primary" id="lingo_confirm_new_post"><?php _e('Add New Post', 'lingo') ?></a>
            </p>
        </form>
    </div>
    
    <?php 
    $echo = ob_get_clean();
    
    echo $echo; 
}

/**
 * Add status closed into select for Lingo_Topic in Lingo_Topic edit page
 * 
 * @global WP_Post $post
 * @return void
 */
function lingo_append_post_status_list() {
    global $post;
    $complete = '';
    $label = '';
    
    if($post->post_type == 'lingo_topic'){
        
         if($post->post_status == 'closed'){
              $complete = ' selected="selected"';
              $label = '<span id="post-status-display">' . __('Closed', 'lingo') . '</span>';
         }
         
         ob_start(); ?>
         <script type="text/javascript">
         jQuery(document).ready(function($){
              jQuery("select#post_status").append('<option value="closed" <?php echo esc_attr($complete); ?>> <?php _e("Closed", 'lingo') ?></option>');
              jQuery(".misc-pub-section label").append('<?php echo esc_html($label); ?>');
         });
         </script>
         <?php
         $echo = ob_get_clean();
         
         echo $echo;
    }
}

/**
 * Displays notice after adding new post
 * 
 * @return void
 */
function lingo_post_saved_notice() {
    
    $post_added = lingo_request('lingo_post_added', false);
    if($post_added == 'true') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'New post was added', 'lingo' ); ?></p>
        </div>
        <?php
    }
    
    $remove = lingo_request('lingo_remove', false);
    if($remove == 'ok') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Post moved to trash', 'lingo' ); ?></p>
        </div>
        <?php
    } elseif($remove == 'error') {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( 'Error when moving to trash', 'lingo' ); ?></p>
        </div>
        <?php
    }
    
    $permRemove = lingo_request('lingo_remove_perm', false);
    if($permRemove == 'ok') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Post removed permamently', 'lingo' ); ?></p>
        </div>
        <?php
    } elseif($permRemove == 'error') {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( 'Error when removing post', 'lingo' ); ?></p>
        </div>
        <?php
    }
    
    $restore = lingo_request('lingo_restore', false);
    if($restore == 'ok') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Post restored from trash', 'lingo' ); ?></p>
        </div>
        <?php
    } elseif($restore == 'error') {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( 'Error when restoring post', 'lingo' ); ?></p>
        </div>
        <?php
    }
    
    $add = lingo_request('lingo_add', false);
    if($add == 'ok') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Post added', 'lingo' ); ?></p>
        </div>
        <?php
    } elseif($add == 'error') {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( 'Error when adding post', 'lingo' ); ?></p>
        </div>
        <?php
    }
    
    $id = intval(lingo_request('post'));
    $post_term = wp_get_post_terms($id, "lingo_forum_group", true);
    $post_type = get_post_type($id);
    
    if($post_term == null && is_numeric($id) && $id > 0 && $post_type == 'lingo_topic') {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e( 'Please select forum for this topic. Topics without forum will not be visible. ', 'lingo' ); ?></p>
        </div>
        <?php
    }
}

/**
 * Add back button in wp-admin header for in Lingo_Topic edit page
 * 
 * @return void
 */
function lingo_add_back_button() {
    
    $back_to_topic = admin_url( 'post.php?post='.$_GET['parent_id'].'&action=edit' );
    $back_to_topic_list = admin_url( 'edit.php?post_type=lingo_topic' );
    
    ?>
    <script>
    jQuery(function(){
        jQuery("body.post-type-lingo_post .wrap h1").append(' <a href="<?php echo esc_url($back_to_topic); ?>" class="page-title-action">Go Back To Topic</a>');
    });
    <?php
    
    if(is_edit_page()):  
    ?>
    jQuery(function(){
        jQuery("body.post-type-lingo_topic .wrap h1").append(' <a href="<?php echo esc_url($back_to_topic_list); ?>" class="page-title-action">Go Back To Topic</a>');
    });
    </script>
    <?php
    endif; 
}

/**
 * Displays order column in forum_group taxonomy in forum_group add form.
 * 
 * @return void
 */
function lingo_add_forum_order() {
    ?>
	<div class="form-field">
            <label for="term_meta[lingo_forum_order]"><?php _e( 'Forum Order', 'lingo' ); ?></label>
            <input type="text" name="term_meta[lingo_forum_order]" id="term_meta[lingo_forum_order]" value="">
            <p class="description"><?php _e( 'Every depth level have own order','lingo' ); ?></p>
	</div>
    
        <div class="form-field">
            <label for="term_meta[lingo_forum_status]"><?php _e( 'Is Closed', 'lingo' ); ?></label>
            <input type="checkbox" name="term_meta[lingo_forum_status]" id="term_meta[lingo_forum_status]" value="1">
            <p class="description"><?php _e( 'Users can not add posts and topics to closed forum ','lingo' ); ?></p>
	</div>
    <?php 
}

/**
 * Displays order field in forum_group taxonomy in forum_group edit form.
 * 
 * @param Taxonomy $term
 * @return void
 */
function lingo_edit_forum_order($term) {
 
    $t_id = $term->term_id;
    $term_meta = get_option( "taxonomy_$t_id" ); 
    
    ?>
        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="term_meta[lingo_forum_order]"><?php _e( 'Forum Order', 'lingo' ); ?></label>
            </th>
            <td>
                <input type="text" name="term_meta[lingo_forum_order]" id="term_meta[lingo_forum_order]" value="<?php echo esc_attr( $term_meta['lingo_forum_order'] ) ? esc_attr( $term_meta['lingo_forum_order'] ) : ''; ?>">
                <p class="description"><?php _e( 'Every depth level have own order','lingo' ); ?></p>
            </td>
        </tr>
        
        <tr class="form-field">
            <th scope="row" valign="top">
                <label for="term_meta[lingo_forum_status]"><?php _e( 'Is Closed', 'lingo' ); ?></label>
            </th>
            <td>
                <input type="checkbox" name="term_meta[lingo_forum_status]" id="term_meta[lingo_forum_status]" value="1" <?php echo esc_attr( $term_meta['lingo_forum_status'] ) ? 'checked="checked"' : ''; ?>>
                <p class="description"><?php _e( 'Users can not add posts and topics to closed forum','lingo' ); ?></p>
            </td>
        </tr>
    <?php
}

/**
 * Saves meta fields for forum_group taxonomy
 * 
 * @param int $term_id
 * @return void
 */
function lingo_save_forum_order( $term_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
        
        $t_id = $term_id;
        $term_meta = get_option( "taxonomy_$t_id" );
        $cat_keys = array_keys( $_POST['term_meta'] );
        
        foreach ( $cat_keys as $key ) {
            if ( isset ( $_POST['term_meta'][$key] ) ) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }

        update_option( "taxonomy_$t_id", $term_meta );
    }
}  

/**
 * Adds column header to forum_group taxonomy list
 * 
 * @param array $columns
 * @return array
 */
function lingo_add_forum_group_columns_header($columns){
    $columns['order'] = 'Order';
    $columns['status'] = 'Status';
    return $columns;
}

/**
 * Adds content to custom columns in forum_group taxonomy list
 * 
 * @param string $content
 * @param string $column_name
 * @param int $term_id
 * @return string
 */
function lingo_edit_forum_group_columns($content, $column_name, $term_id){
    
    $term = get_option( "taxonomy_$term_id" ); 

    switch ($column_name) {
        case 'order':
            if(isset($term['lingo_forum_order'])) {
                $content = $term['lingo_forum_order'];
            } else {
                $content = '-';
            }
            break;
        case 'status':
            if(isset($term['lingo_forum_status'])) {
                $content = 'Closed';
            } else {
                $content = 'Open';
            }
            break;
        default:
            break;
    }
    
    return $content;
}
/**
 * Adds new columns to Lingo_Topic list
 * 
 * @param array $columns
 * @return array
 */
function lingo_edit_topic_columns_header($columns) {
    $new_columns['cb'] = '<input type="checkbox" />';
     
    $new_columns['title'] = __('Title', 'lingo');
    $new_columns['desc'] = _x('Description', 'lingo');
    $new_columns['forum'] = __('Forum', 'lingo');
    //$new_columns['posts_num'] = __('Posts Num', 'lingo');
    $new_columns['author'] = _x('Author', 'lingo');
    $new_columns['date'] = _x('Modified', 'lingo');
    $new_columns['created'] = __("Created", 'lingo');
    $new_columns['last_post'] = __('Last Post', 'lingo');
    $new_columns['status'] = __('Status', 'lingo');
 
    return $new_columns;
}

/**
 * Adds value for new coulmns in Lingo_Topics list
 * 
 * @param string $column_name
 * @param int $id
 */
function lingo_edit_topic_columns($column_name, $id) {
    
    $p = get_post($id);
    $terms = wp_get_post_terms( $p->ID, 'lingo_forum_group', array("fields" => "all"));
    $terms = $terms[0];
    $args = apply_filters( "lingo_post_query", array(
            'post_parent' => $p->ID,
            'post_type'   => 'lingo_post', 
            'numberposts' => -1,
            'post_status' => 'publish' 
    )); 
    
    $childs = get_children($args);
    
    $last = $childs[0]->post_date;
    foreach($childs as $child) {
        if($last < $child->post_date) {
            $last = $child->post_date;
        }
    }
    
    switch ($column_name) {
    case 'desc':
        echo $p->post_excerpt;
        break;
 
    case 'topic':
        echo $parent->post_title;
        break;
    
    case 'forum':
        if($terms == null) {
            echo '<span title="'.__('Topic without forum will be not visible in frontend!', 'lingo').'" class="dashicons dashicons-warning lingo-red"></span>';
        } else {
            echo $terms->name; 
        }
        
        echo "<br/>".__('Posts', 'lingo').": " . count($childs);
        break;
    
    case 'status':
        echo "<span class='lingo-status lingo-".$p->post_status."'>" . $p->post_status . "</span>";
        break;
    
    /*case 'posts_num':
        echo count($childs);
        break;*/
    
    case 'last_post':
        if($last != null) {
            echo date("Y/m/d", strtotime($last)) . "<br/>" . date("H:i:s", strtotime($last));
        } else {
            echo _e("No Posts", "lingo");
        }
        break;
    
    case 'created':
        echo date("Y/m/d", strtotime($p->post_date)) . "<br/>" . date("H:i:s", strtotime($p->post_date));
        
        break;
        
    default:
        break;
    } 
}