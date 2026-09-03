<?php
if ( post_password_required() ) { return; }

if ( have_comments() ) : ?>
    <ol class="comment-list">
        <?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true ) ); ?>
    </ol>
    <?php the_comments_pagination(); ?>
<?php endif; ?>

<?php if ( comments_open() ) : ?>
    <?php if ( is_user_logged_in() ) : ?>
        <?php comment_form(); ?>
    <?php else : ?>
        <div class="mdc-comments-login">
            <p>Para participar da conversa, entre na sua conta ou faça um cadastro gratuito.</p>
            <a class="mdc-button" href="<?php echo esc_url( mdc_conta_login_url( get_permalink() . '#comentarios' ) ); ?>">Entrar ou criar conta</a>
        </div>
    <?php endif; ?>
<?php endif; ?>
