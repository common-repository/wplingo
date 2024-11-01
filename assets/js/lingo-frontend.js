jQuery(function($) {
    
    // Moves post to trash
    $(".lingo-remove-post").click(function(event) {
        event.preventDefault();
        
        var id = $(this).data('id');
        
        var data = {
            'action': 'lingo_post_trash',
            'nonce' : $(this).data('nonce'),
            'post_id' : id,
        };
        
        var row = $(this).parent().parent().parent().parent();

        $.post(lingo_lang.ajaxurl, data, function(response) {

            resp = $.parseJSON(response)
            
            $(".lingo-flash").remove();
            
            if(resp['status'] === 'error') {
                row.before('<div class="lingo-error lingo-flash"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> '+resp['message']+'</div>')
            } else {
                row.children().hide();
                row.addClass('lingo-removed-post')
                row.append("<p class='lingo-removed-post-msg'>" + resp['message'] + "<br/> <a href='#' class='lingo-trash-restore' data-id='"+id+"' >Undo</a></p>");
            }
        });   
    });
    
    // Edit post in frontend
    $(".lingo-post-edit").click(function(event) {
        event.preventDefault();
        
        $(this).toggle();
        $(this).parent().children(".lingo-edit-cancel").toggle();
        $(this).parent().parent().parent().children(".lingo-post-message-edit").toggle();
        $(this).parent().parent().parent().children(".lingo-post-message").toggle();
        
    });
    
    // Restore edited changes
    $(".lingo-edit-cancel").click(function(event) {
        event.preventDefault();
        
        var msgBox = $(this).parent().parent().parent().children(".lingo-post-message");
        
        var name = "lingo_edit_post_message_" + $(this).data("id");
        tinymce.editors[name].setContent(msgBox.text());
        
        $(this).toggle();
        $(this).parent().children(".lingo-post-edit").toggle();
        $(this).parent().parent().parent().children(".lingo-post-message-edit").toggle();
        msgBox.toggle();
    });
    
    // Restore edited changes
    $(".lingo-inner-edit-cancel").click(function(event) {
        event.preventDefault();
        
        var row = $(this).parent().parent();
        var msgBox = row.children(".lingo-post-message");
        
        var name = "lingo_edit_post_message_" + $(this).data("id");
        tinymce.editors[name].setContent(msgBox.text());
        
        row.children(".lingo-post-meta").children(".lingo-post-admin").children(".lingo-post-edit").toggle();
        row.children(".lingo-post-meta").children(".lingo-post-admin").children(".lingo-edit-cancel").toggle();
        row.children(".lingo-post-message-edit").toggle();
        row.children(".lingo-post-message").toggle();
    });
    
    // Save edition
    $(".lingo-edit-post-save").click(function(event) {
        event.preventDefault();
        
        var id = $(this).parent().children("#lingo-edit-post-id").val();
        var name = "lingo_edit_post_message_" + id;
        var message = tinymce.editors[name].getContent();
        
        var data = {
            'action': 'lingo_post_edit_save',
            'post_message' : message,
            'post_id' : id,
        };
        
        var row = $(this).parent().parent();
        
        $.post(lingo_lang.ajaxurl, data, function(response) {

            resp = $.parseJSON(response);
            
            $(".lingo-flash").remove();
            
            if(resp['status'] === 'error') {
                row.parent().before('<div class="lingo-error lingo-flash"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> '+resp['message']+'</div>');
            } else {
                row.children(".lingo-post-message").html(message);
                row.parent().before('<div class="lingo-info lingo-flash"><i class="fa fa-info-circle" aria-hidden="true"></i> '+resp['message']+'</div>');
            }
            
            row.children(".lingo-post-meta").children(".lingo-post-admin").children(".lingo-post-edit").toggle();
            row.children(".lingo-post-meta").children(".lingo-post-admin").children(".lingo-edit-cancel").toggle();
            row.children(".lingo-post-message-edit").toggle();
            row.children(".lingo-post-message").toggle();
        });
        
        
    });
    
    
    // Restore post from trash
    $('body').on('click', 'a.lingo-trash-restore', function(event) {
        event.preventDefault();
        
        var data = {
            'action': 'lingo_post_restore',
            'id' : $(this).data('id'),
        };
        
        var row = $(this).parent().parent();
        
        $.post(ajaxurl, data, function(response) {
            resp = $.parseJSON(response);
            if(resp['status'] == 'error') {
                row.before('<div class="lingo-error lingo-flash"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> '+resp['message']+'</div>');
            } else {
                row.children(".lingo-removed-post-msg").remove();
                row.children().fadeIn();
                row.removeClass("lingo-removed-post");
            }
        });
    });
    
    // Close topic
    $(".lingo-close-topic").click(function(event) {
        event.preventDefault();
        
        var id = $(this).data('id');
        
        var data = {
            'action': 'lingo_change_topic_status',
            'new_status' : "closed",
            'topic_id' : id,
        };

        $.post(lingo_lang.ajaxurl, data, function(response) {
            window.location.reload();
        });  
    });   
    
    // Open topic
    $(".lingo-open-topic").click(function(event) {
        event.preventDefault();
        
        var id = $(this).data('id');
        
        var data = {
            'action': 'lingo_change_topic_status',
            'new_status' : "publish",
            'topic_id' : id,
        };

        $.post(lingo_lang.ajaxurl, data, function(response) {
            window.location.reload();
        });  
    });   
    
    // Move post to trash
    $(".lingo-remove-topic").click(function(event) {
        event.preventDefault();
        
        var id = $(this).data('id');
        
        var data = {
            'action': 'lingo_topic_trash',
            'nonce' : $(this).data('nonce'),
            'topic_id' : id,
            'redirect' : $(this).data('redirect'),
        };

        $.post(lingo_lang.ajaxurl, data, function(response) {

            resp = $.parseJSON(response)
            
            $(".lingo-flash").remove();
            
            if(resp['status'] === 'error') {
                $(".lingo-post-wrapper").before('<div class="lingo-error lingo-flash"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> '+resp['message']+'</div>')
            } else {
                window.location.href = resp['redirect'];
            }
        });   
    });
    
    // Open reply form
    $(".lingo-button-reply").click(function(event) {
        event.preventDefault();
        var section = $(this).data("section");
        
        $(".lingo-topic-reply-1").slideToggle();
        $('html, body').animate({
            scrollTop: $(".lingo-topic-reply-1").offset().top
        }, 1000);
        
        if(!$(this).hasClass("lingo_button_hover")) {
            $(".lingo-button-reply").addClass("lingo_button_hover");
        } else {
            $(".lingo-button-reply").removeClass("lingo_button_hover");
        }
    });
    
    // Open search form
    $(".lingo-button-search").click(function(event) {
        event.preventDefault();
        var section = $(this).data("section");
        
        $(".lingo-forum-search-"+section).slideToggle();
        
        if(!$(this).hasClass("lingo_button_hover")) {
            $(this).addClass("lingo_button_hover");
        } else {
            $(this).removeClass("lingo_button_hover");
        } 
    });
    
    // Open moderation box
    $(".lingo-button-moderate").click(function(event) {
        event.preventDefault();
        var section = $(this).data("section");
        
        $(".lingo-moderator-actions-"+section).slideToggle();
        
        if(!$(this).hasClass("lingo_button_hover")) {
            $(this).addClass("lingo_button_hover");
        } else {
            $(this).removeClass("lingo_button_hover");
        } 
    });
    
    // Open subscribtion form
    $(".lingo-subscribe-topic").click(function(event) {
        event.preventDefault();
        var section = $(this).data("section");
        
        $(".lingo-forum-subscribe-"+section).toggle();
    });
    
    // More menu toggle
    $(".lingo-button-more").click(function(event) {
        event.preventDefault();
        event.stopPropagation();
        
        $(this).parent().children(".lingo-dropdown-menu").toggle();
        
        if(!$(this).hasClass("lingo_button_hover")) {
            $(this).addClass("lingo_button_hover");
        } else {
            $(this).removeClass("lingo_button_hover");
        } 
    });
    
    // Active action from more menu
    $(".lingo-more-action").click(function(event) {
        event.preventDefault();
        $(".lingo-dropdown-menu").hide();
        $(".lingo-button-more").removeClass("lingo_button_hover");
    });
    
    // Hide flash and more after window click
    $(window).click(function() {
        $(".lingo-dropdown-menu").hide();
        $(".lingo-button-more").removeClass("lingo_button_hover");
        $(".lingo-action-hidden-window").hide();
        
        $(".lingo-flash").fadeOut(300, function() { 
            $(this).remove();
        });

    });
    
    // Don't hide window on click SECTION
    $(".lingo-close-hidden").click(function(event) {
        event.preventDefault();
        $(this).parent().parent().parent().hide();
    });
    
    $(".lingo-action-hidden-window").click(function(event) {
        event.stopPropagation();
    });

    $('.lingo-dropdown-menu').click(function(event){
        event.stopPropagation();
    });
    
    $('.lingo-flash').click(function(event){
        event.stopPropagation();
    });
    
    // Don't hide window on click SECTION END
    
    // Unsubscribe topic
    $('.lingo-unsubscribe-topic').click(function(event) {
        event.preventDefault();
        
        var section = $(this).data("section");
        
        var data = {
            'action'    : 'lingo_subscription_remove',
            'email'     : $(this).data("email"),
            'topic_id'  : $(this).data("topic"),
        };

        $.post(lingo_lang.ajaxurl, data, function(response) {

            resp = $.parseJSON(response);
            
            
            var msg = '';
            if(resp['status'] !== 200) {
                msg = $('<div class="lingo-error lingo-flash" style="margin: 10px;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> '+resp['message']+'</div>');
            } else {
                msg = $('<div class="lingo-info lingo-flash" style="margin: 10px;"><i class="fa fa-info-circle" aria-hidden="true"></i> '+resp['message']+'</div>');
                $(".lingo-unsubscribe-topic").hide();
                $(".lingo-subscribe-topic").show();
            }

            lingo_flash(msg, section);
        }); 
    });
    
    // Subscribe topic
    $('.lingo-subscription-save').submit(function(event) {
        event.preventDefault();
        
        var section = $(this).children("#lingo_section").val();
        
        var data = {
            'action'    : 'lingo_subscription_save',
            'email'     : $(this).children("#lingo_subscribe_emial").val(),
            'topic_id'  : $(this).children("#lingo_subscribe_topic_id").val(),
        };

        $.post(lingo_lang.ajaxurl, data, function(response) {

            resp = $.parseJSON(response);

            var msg = '';
            if(resp['status'] !== 200) {
                msg = $('<div class="lingo-error lingo-flash" style="margin: 10px;"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> '+resp['message']+'</div>');
            } else {
                msg = $('<div class="lingo-info lingo-flash" style="margin: 10px;"><i class="fa fa-info-circle" aria-hidden="true"></i> '+resp['message']+'</div>');
                $(".lingo-unsubscribe-topic").show();
                $(".lingo-subscribe-topic").hide();
                $(".lingo-action-hidden-window").hide();
            }

            lingo_flash(msg, section);
        }); 
    });
    
    /**
     * Flash message
     * @param String msg
     * @param int section
     */
    function lingo_flash(msg, section) {
        if($(".lingo-flash").length) {
            $(".lingo-flash").fadeOut(300, function() { 
                $(".lingo-flash").remove(); 
                if(section === 0) {
                    $(".lingo-topic-actions-"+section).after(msg);
                } else {
                    $(".lingo-topic-actions-"+section).before(msg);
                }
            });
        } else {
            if(section === 0) {
                $(".lingo-topic-actions-"+section).after(msg);
            } else {
                $(".lingo-topic-actions-"+section).before(msg);
            }
        }
    }
    
    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd'
    });

});

