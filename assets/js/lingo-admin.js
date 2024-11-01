jQuery(function($) {
    
    // Replace message with editor after clicking edit
    jQuery(".lingo-post-edit").click(function(event) {
        event.preventDefault();
        var button = jQuery(this);
        var message = button.parent().parent().parent().children('.lingo-admin-post-message');
        
        message.children('.lingo-admin-post-content').toggle();
        message.children('.lingo-admin-edit-post-form').toggle();
        
        if(button.hasClass('lingo-post-edit')) {
            button.html('<i class="fa fa-times" aria-hidden="true"></i> Cancel');
            button.removeClass('lingo-post-edit');
            button.addClass('lingo-post-edit-cancel');      
        } else {
            button.html('<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit');
            button.removeClass('lingo-post-edit-cancel');
            button.addClass('lingo-post-edit');          
        }
        
    });

    // Toggle post message
    jQuery(".lingo-toggle-icon").click(function() {
        jQuery(this).parent().parent().parent().children(".lingo-admin-post-message").toggle();

        if(jQuery(this).hasClass('fa-caret-up')) {
            jQuery(this).removeClass('fa-caret-up');
            jQuery(this).addClass('fa-caret-down');
        } else {
            jQuery(this).removeClass('fa-caret-down');
            jQuery(this).addClass('fa-caret-up');
        }
    });
    
    // Saves post after edition
    jQuery('.lingo-edit-post-save').click(function(event) {
        event.preventDefault();
        
        var message = jQuery(this).parent().parent().parent();
        var button = jQuery(this).parent().parent().parent().parent().children('.lingo-admin-post-meta').children('.lingo-admin-post-meta-admin').children('.lingo-post-edit-cancel');
        
        var name = 'lingo_edit_post_message_' + jQuery(this).parent().children('#lingo-edit-post-id').val();
        
        
        var data = {
            'action': 'lingo_post_edit_save',
            'post_id' : jQuery(this).parent().children('#lingo-edit-post-id').val(),
            'post_message' : tinymce.editors[name].getContent()
        };

        jQuery.post(ajaxurl, data, function(response) {
                resp = jQuery.parseJSON(response);

                if(resp.status === 'ok') {
                    jQuery('h1').after('<div class="notice notice-success is-dismissible"><p>'+resp.message+'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>')
                    message.children('.lingo-admin-post-content').html(resp.new_value);
                } else {
                    jQuery('h1').after('<div class="notice notice-error is-dismissible"><p>'+resp.error+'</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>')
                }
                
                message.children('.lingo-admin-post-content').toggle();
                message.children('.lingo-admin-edit-post-form').toggle();
                
                if(button.hasClass('lingo-post-edit')) {
                    button.html('<i class="fa fa-times" aria-hidden="true"></i> Cancel');
                    button.removeClass('lingo-post-edit');
                    button.addClass('lingo-post-edit-cancel');      
                } else {
                    button.html('<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit');
                    button.removeClass('lingo-post-edit-cancel');
                    button.addClass('lingo-post-edit');          
                }       
        });
    });
    
    // Restores changes in post edition after clickin 'cancel' button
    jQuery('.lingo-add-cancel').click(function(event) {
       event.preventDefault();
       
        var message = jQuery(this).parent().parent().parent();
        var button = jQuery(this).parent().parent().parent().parent().children('.lingo-admin-post-meta').children('.lingo-admin-post-meta-admin').children('.lingo-post-edit-cancel');
        var content = message.children('.lingo-admin-post-content').html();
        
        if(button.hasClass('lingo-post-edit')) {
            button.html('<i class="fa fa-times" aria-hidden="true"></i> Cancel');
            button.removeClass('lingo-post-edit');
            button.addClass('lingo-post-edit-cancel');      
        } else {
            button.html('<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit');
            button.removeClass('lingo-post-edit-cancel');
            button.addClass('lingo-post-edit');          
        }  
        
        var name = 'lingo_edit_post_message_' + jQuery(this).parent().children('#lingo-edit-post-id').val();
        tinymce.editors[name].setContent(content);
        message.children('.lingo-admin-post-content').toggle();
        message.children('.lingo-admin-edit-post-form').toggle();
    });
    
    // Moves post into trash
    jQuery('.lingo-delete-post').click(function(event) {
        event.preventDefault();
        
        //var wraper = jQuery(this).parent().parent().parent();
        
        var data = {
            'action': 'lingo_post_trash',
            'nonce' : jQuery(this).data('nonce'),
            'post_id' : jQuery(this).data('id'),
        };
        
        jQuery.post(ajaxurl, data, function(response) {
            resp = jQuery.parseJSON(response);
            
            var url = build_clean_url() + "&lingo_remove=" + resp.status;
            window.location.href = url;          
        });
    });
        
    // Restores post from trash
    jQuery('.lingo-trash-restore').click(function(event) {
        event.preventDefault();
        
        var data = {
            'action': 'lingo_post_restore',
            'id' : jQuery(this).data('id'),
        };
        
        jQuery.post(ajaxurl, data, function(response) {
            
            resp = jQuery.parseJSON(response);
            var url = build_clean_url() + "&lingo_remove=" + resp.status;
            window.location.href = url;     
            
        });
    });
    
    // Removes post after confirmation
    jQuery('body').on('click', '#lingo-post-remove-confirmed', function(event){
        event.preventDefault();
        
        var data = {
            'action': 'lingo_post_remove_perm',
            'id' : jQuery(this).data('id'),
        };
        
        jQuery.post(ajaxurl, data, function(response) {
            resp = jQuery.parseJSON(response);
            var url = build_clean_url() + "&lingo_remove=" + resp.status;
            window.location.href = url;     
        });
    });
    
    // Change new post button on form
    jQuery('.lingo_add_new_post_button').click(function(event) {
        event.preventDefault();
        
        var box = jQuery(this).parent().parent();
        
        var form = box.children('.lingo-new-post-form');
        var button = box.children('.lingo_new_post_wraper');
        
        form.toggle();
        button.toggle();
    });
    
   // Creates new post
   jQuery('#lingo_confirm_new_post').click(function(event) {
        event.preventDefault();

        var data = {
            'action': 'lingo_insert_new_post',
            'parent_id' : jQuery(this).data('parent'),
            'post_content' : tinymce.editors['lingo_new_post_content'].getContent()
        };

        jQuery.post(ajaxurl, data, function(response) {
            resp = jQuery.parseJSON(response);
            var url = build_clean_url() + "&lingo_add=" + resp.status;
            window.location.href = url;     
        });
    });
    
    jQuery('.lingo-forum-add-new-button').click(function(event) {
        event.preventDefault();
        jQuery('.lingo-forum-add-form').toggle();
    });
    
    
    jQuery('.lingo_add_new_forum_button').click(function(event) {
        event.preventDefault();
        
        var parent_id = jQuery("#lingo_new_forum_parent").val();
        var forum_name = jQuery("#lingo_new_forum_name").val();
        
        var data = {
            'action': 'lingo_insert_new_forum',
            'parent_id' : parent_id,
            'forum_name' : forum_name,
        };
        
        jQuery.post(ajaxurl, data, function(response) {
            resp = jQuery.parseJSON(response);
              
            var parent_label = jQuery("#lingo_new_forum_parent option:selected").html();
            
            if(jQuery.isNumeric(parent_id)) {
                jQuery("optgroup[label=" + parent_label + "]").append('<option value="' + resp.term_id + '">' + forum_name + '</option>')
                jQuery("#lingo_forum_group").val(resp.term_id);
                jQuery(".lingo-side-box-forum").show();
            } else {
                jQuery("#lingo_forum_group").append('<optgroup label="' + forum_name + '"></optgroup>');
                jQuery(".lingo-side-box-category").show();
            }
            
            jQuery("#lingo_new_forum_name").val('');
            jQuery("#lingo_new_forum_parent").val('');
  
        });
        
    });
});

function build_clean_url() {
    var url = 'http://' + window.location.hostname + window.location.pathname;
    
    var id = getUrlParameter('post');
    var action = getUrlParameter('action');
    
    url += '?post=' + id + '&action=' + action;
    
    return url;
}

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};