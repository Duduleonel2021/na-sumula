<?php
/** Template Name: Minha conta */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
$redirect = mdc_conta_redirect_back_url();
$acao = sanitize_key( $_GET['acao'] ?? ( is_user_logged_in() ? 'perfil' : 'entrar' ) );
$erro = sanitize_key( $_GET['erro'] ?? '' );
$message = mdc_conta_render_message( $erro );
?>
<section class="mdc-account-page">
    <div class="mdc-container mdc-account-page__inner">
        <div class="mdc-account-page__intro">
            <span class="mdc-section-kicker">COMUNIDADE</span>
            <h1>Minha conta</h1>
            <p>Entre para participar das conversas do Na Súmula, acompanhar suas interações e comentar nas publicações.</p>
        </div>

        <?php if ( $message ) : ?><div class="mdc-account-notice" role="alert"><?php echo esc_html( $message ); ?></div><?php endif; ?>

        <?php if ( is_user_logged_in() ) : $user = wp_get_current_user(); ?>
            <div class="mdc-account-card">
                <span class="mdc-section-kicker">BEM-VINDO</span>
                <h2><?php echo esc_html( $user->display_name ); ?></h2>
                <p><?php echo esc_html( $user->user_email ); ?></p>
                <div class="mdc-account-actions">
                    <a class="mdc-button" href="<?php echo esc_url( $redirect ); ?>">Voltar à leitura</a>
                    <a class="mdc-button mdc-button--outline" href="<?php echo esc_url( mdc_conta_logout_url( $redirect ) ); ?>">Sair</a>
                </div>
            </div>
        <?php elseif ( 'cadastro' === $acao ) : ?>
            <div class="mdc-account-card">
                <span class="mdc-section-kicker">CADASTRO</span>
                <h2>Crie sua conta</h2>
                <p>Use seu e-mail para participar dos comentários.</p>
                <form class="mdc-account-form" method="post">
                    <?php wp_nonce_field( 'mdc_conta_email', 'mdc_conta_nonce' ); ?>
                    <input type="hidden" name="mdc_conta_action" value="register">
                    <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect ); ?>">
                    <label>Nome<input type="text" name="display_name" autocomplete="name" required></label>
                    <label>E-mail<input type="email" name="user_email" autocomplete="email" required></label>
                    <label>Senha<input type="password" name="user_pass" autocomplete="new-password" minlength="8" required><small>Mínimo de 8 caracteres.</small></label>
                    <button class="mdc-button" type="submit">Criar conta</button>
                </form>
                <p class="mdc-account-switch">Já tem conta? <a href="<?php echo esc_url( mdc_conta_login_url( $redirect ) ); ?>">Entrar</a></p>
            </div>
        <?php else : ?>
            <div class="mdc-account-card">
                <span class="mdc-section-kicker">ENTRAR</span>
                <h2>Acesse sua conta</h2>
                <form class="mdc-account-form" method="post">
                    <?php wp_nonce_field( 'mdc_conta_email', 'mdc_conta_nonce' ); ?>
                    <input type="hidden" name="mdc_conta_action" value="login">
                    <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect ); ?>">
                    <label>E-mail ou usuário<input type="text" name="log" autocomplete="username" required></label>
                    <label>Senha<input type="password" name="pwd" autocomplete="current-password" required></label>
                    <label class="mdc-account-check"><input type="checkbox" name="rememberme" value="1"> Lembrar de mim</label>
                    <button class="mdc-button" type="submit">Entrar</button>
                </form>
                <a class="mdc-account-lost" href="<?php echo esc_url( wp_lostpassword_url( $redirect ) ); ?>">Esqueci minha senha</a>

                <?php $google = mdc_conta_oauth_url( 'google', $redirect ); $facebook = mdc_conta_oauth_url( 'facebook', $redirect ); ?>
                <?php if ( $google || $facebook ) : ?>
                    <div class="mdc-account-divider"><span>ou entre com</span></div>
                    <div class="mdc-account-socials">
                        <?php if ( $google ) : ?><a href="<?php echo esc_url( $google ); ?>" class="mdc-account-social">Google</a><?php endif; ?>
                        <?php if ( $facebook ) : ?><a href="<?php echo esc_url( $facebook ); ?>" class="mdc-account-social">Facebook</a><?php endif; ?>
                    </div>
                <?php endif; ?>

                <p class="mdc-account-switch">Ainda não tem conta? <a href="<?php echo esc_url( mdc_conta_registro_url( $redirect ) ); ?>">Cadastre-se</a></p>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php get_footer(); ?>
