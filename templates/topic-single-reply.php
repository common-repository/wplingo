<?php if($topic_status != 'closed' && $forumStatus != 1): ?>
    <div class="lingo-action-hidden lingo-topic-reply-<?php echo $section; ?>">
        <?php 
            $scheme = Lingo::instance()->get("form_add_new_post");
            $form = new Lingo_Form( $scheme );
            $btn_label = __("Leave Reply", "lingo");
        ?>
        <?php include apply_filters( "lingo_template_load", LINGO_PATH . 'templates/form-short.php' ); ?>
    </div>
<?php endif; ?>