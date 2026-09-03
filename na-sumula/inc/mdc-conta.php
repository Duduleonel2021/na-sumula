<?php
/**
 * Conta do leitor e autenticação editorial.
 *
 * Oferece cadastro/login por e-mail e integração opcional com Google/Facebook.
 * As credenciais OAuth são configuradas em Configurações > Mundo da Copa > Conta.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function mdc_conta_url( $args = array() ) {
    $url = function_exists( 'mdc_get_conta_page_url' ) ? mdc_get_conta_page_url() : home_url( '/conta/' );
    return add_query_arg( $args, $url );
}

function mdc_get_conta_page_url() {
    $page_id = absint( get_option( 'mdc_conta_page_id', 0 ) );
    if ( $page_id && 'publish' === get_post_status( $page_id ) ) {
        return get_permalink( $page_id );
    }
    return home_url( '/conta/' );
}

function mdc_conta_redirect_back_url() {
    $url = isset( $_REQUEST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_REQUEST['redirect_to'] ) ) : '';
    $url = wp_validate_redirect( $url, '' );
    if ( ! $url ) {
        $url = wp_validate_redirect( wp_get_referer(), home_url( '/' ) );
    }
    return $url ? $url : home_url( '/' );
}

function mdc_conta_login_url( $redirect = '' ) {
    return mdc_conta_url( array( 'acao' => 'entrar', 'redirect_to' => $redirect ? $redirect : mdc_conta_redirect_back_url() ) );
}

function mdc_conta_registro_url( $redirect = '' ) {
    return mdc_conta_url( array( 'acao' => 'cadastro', 'redirect_to' => $redirect ? $redirect : mdc_conta_redirect_back_url() ) );
}

function mdc_conta_logout_url( $redirect = '' ) {
    return wp_logout_url( $redirect ? $redirect : home_url( '/' ) );
}

function mdc_conta_processar_email() {
    if ( ! isset( $_POST['mdc_conta_action'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mdc_conta_nonce'] ?? '' ) ), 'mdc_conta_email' ) ) {
        return;
    }

    $action   = sanitize_key( wp_unslash( $_POST['mdc_conta_action'] ) );
    $redirect = mdc_conta_redirect_back_url();

    if ( 'login' === $action ) {
        $login    = sanitize_text_field( wp_unslash( $_POST['log'] ?? '' ) );
        $password = (string) ( $_POST['pwd'] ?? '' );
        $remember = ! empty( $_POST['rememberme'] );

        if ( ! $login || ! $password ) {
            wp_safe_redirect( mdc_conta_url( array( 'acao' => 'entrar', 'erro' => 'preencha' ) ) );
            exit;
        }

        $user = wp_signon(
            array(
                'user_login'    => $login,
                'user_password' => $password,
                'remember'      => $remember,
            ),
            is_ssl()
        );

        if ( is_wp_error( $user ) ) {
            wp_safe_redirect( mdc_conta_url( array( 'acao' => 'entrar', 'erro' => 'credenciais' ) ) );
            exit;
        }

        wp_safe_redirect( $redirect );
        exit;
    }

    if ( 'register' === $action ) {
        $name     = sanitize_text_field( wp_unslash( $_POST['display_name'] ?? '' ) );
        $email    = sanitize_email( wp_unslash( $_POST['user_email'] ?? '' ) );
        $password = (string) ( $_POST['user_pass'] ?? '' );

        if ( ! $name || ! $email || ! $password ) {
            wp_safe_redirect( mdc_conta_url( array( 'acao' => 'cadastro', 'erro' => 'preencha' ) ) );
            exit;
        }
        if ( ! is_email( $email ) ) {
            wp_safe_redirect( mdc_conta_url( array( 'acao' => 'cadastro', 'erro' => 'email' ) ) );
            exit;
        }
        if ( strlen( $password ) < 8 ) {
            wp_safe_redirect( mdc_conta_url( array( 'acao' => 'cadastro', 'erro' => 'senha' ) ) );
            exit;
        }
        if ( email_exists( $email ) ) {
            wp_safe_redirect( mdc_conta_url( array( 'acao' => 'entrar', 'erro' => 'existente' ) ) );
            exit;
        }

        $base_login = sanitize_user( current( explode( '@', $email ) ), true );
        $base_login = $base_login ? $base_login : 'leitor';
        $login      = $base_login;
        $suffix     = 1;
        while ( username_exists( $login ) ) {
            $login = $base_login . $suffix;
            ++$suffix;
        }

        $user_id = wp_create_user( $login, $password, $email );
        if ( is_wp_error( $user_id ) ) {
            wp_safe_redirect( mdc_conta_url( array( 'acao' => 'cadastro', 'erro' => 'falha' ) ) );
            exit;
        }

        wp_update_user(
            array(
                'ID'           => $user_id,
                'display_name' => $name,
                'nickname'     => $name,
            )
        );

        wp_set_auth_cookie( $user_id, true, is_ssl() );
        wp_set_current_user( $user_id );
        wp_safe_redirect( $redirect );
        exit;
    }
}
add_action( 'template_redirect', 'mdc_conta_processar_email', 1 );

function mdc_conta_oauth_state( $provider, $redirect ) {
    $state = wp_generate_password( 32, false, false );
    set_transient( 'mdc_oauth_' . $state, array( 'provider' => $provider, 'redirect' => $redirect ), 10 * MINUTE_IN_SECONDS );
    return $state;
}

function mdc_conta_oauth_url( $provider, $redirect = '' ) {
    $redirect = $redirect ? $redirect : mdc_conta_redirect_back_url();
    $state    = mdc_conta_oauth_state( $provider, $redirect );
    $callback = add_query_arg( 'mdc_oauth', $provider, home_url( '/' ) );

    if ( 'google' === $provider ) {
        $client_id = trim( (string) get_option( 'mdc_google_client_id', '' ) );
        if ( ! $client_id ) { return ''; }
        return add_query_arg(
            array(
                'client_id'     => $client_id,
                'redirect_uri'  => $callback,
                'response_type' => 'code',
                'scope'         => 'openid email profile',
                'state'         => $state,
                'access_type'   => 'online',
                'prompt'        => 'select_account',
            ),
            'https://accounts.google.com/o/oauth2/v2/auth'
        );
    }

    if ( 'facebook' === $provider ) {
        $app_id = trim( (string) get_option( 'mdc_facebook_app_id', '' ) );
        if ( ! $app_id ) { return ''; }
        return add_query_arg(
            array(
                'client_id'     => $app_id,
                'redirect_uri'  => $callback,
                'response_type' => 'code',
                'scope'         => 'email,public_profile',
                'state'         => $state,
            ),
            'https://www.facebook.com/dialog/oauth'
        );
    }

    return '';
}

function mdc_conta_find_or_create_social_user( $provider, $profile ) {
    $provider = sanitize_key( $provider );
    $remote   = sanitize_text_field( (string) ( $profile['id'] ?? '' ) );
    $email    = sanitize_email( (string) ( $profile['email'] ?? '' ) );
    $name     = sanitize_text_field( (string) ( $profile['name'] ?? '' ) );

    if ( ! $remote || ! $email ) {
        return new WP_Error( 'mdc_social_profile', 'A rede social não forneceu um e-mail válido.' );
    }

    $meta_key = 'mdc_social_' . $provider . '_id';
    $users    = get_users( array( 'meta_key' => $meta_key, 'meta_value' => $remote, 'number' => 1, 'fields' => 'ids' ) );
    $user_id  = ! empty( $users ) ? absint( $users[0] ) : 0;

    if ( ! $user_id ) {
        $user_id = email_exists( $email );
    }

    if ( ! $user_id ) {
        $base_login = sanitize_user( current( explode( '@', $email ) ), true );
        $base_login = $base_login ? $base_login : 'leitor';
        $login      = $base_login;
        $suffix     = 1;
        while ( username_exists( $login ) ) {
            $login = $base_login . $suffix;
            ++$suffix;
        }
        $user_id = wp_insert_user(
            array(
                'user_login'   => $login,
                'user_pass'    => wp_generate_password( 32, true, true ),
                'user_email'   => $email,
                'display_name' => $name ? $name : $login,
                'nickname'     => $name ? $name : $login,
                'role'         => get_option( 'default_role', 'subscriber' ),
            )
        );
    }

    if ( is_wp_error( $user_id ) ) {
        return $user_id;
    }

    update_user_meta( $user_id, $meta_key, $remote );
    if ( ! empty( $profile['picture'] ) ) {
        update_user_meta( $user_id, 'mdc_social_avatar', esc_url_raw( $profile['picture'] ) );
    }
    return absint( $user_id );
}

function mdc_conta_oauth_callback() {
    if ( empty( $_GET['mdc_oauth'] ) ) { return; }
    $provider = sanitize_key( wp_unslash( $_GET['mdc_oauth'] ) );
    $code     = sanitize_text_field( wp_unslash( $_GET['code'] ?? '' ) );
    $state    = sanitize_text_field( wp_unslash( $_GET['state'] ?? '' ) );

    if ( ! in_array( $provider, array( 'google', 'facebook' ), true ) || ! $code || ! $state ) {
        wp_safe_redirect( mdc_conta_url( array( 'acao' => 'entrar', 'erro' => 'social' ) ) );
        exit;
    }

    $session = get_transient( 'mdc_oauth_' . $state );
    delete_transient( 'mdc_oauth_' . $state );
    if ( ! is_array( $session ) || $provider !== ( $session['provider'] ?? '' ) ) {
        wp_safe_redirect( mdc_conta_url( array( 'acao' => 'entrar', 'erro' => 'social' ) ) );
        exit;
    }

    $callback = add_query_arg( 'mdc_oauth', $provider, home_url( '/' ) );
    $profile  = array();

    if ( 'google' === $provider ) {
        $response = wp_remote_post(
            'https://oauth2.googleapis.com/token',
            array(
                'timeout' => 15,
                'body'    => array(
                    'code'          => $code,
                    'client_id'     => trim( (string) get_option( 'mdc_google_client_id', '' ) ),
                    'client_secret' => trim( (string) get_option( 'mdc_google_client_secret', '' ) ),
                    'redirect_uri'  => $callback,
                    'grant_type'    => 'authorization_code',
                ),
            )
        );
        $body = ! is_wp_error( $response ) ? json_decode( wp_remote_retrieve_body( $response ), true ) : array();
        $token = is_array( $body ) ? (string) ( $body['access_token'] ?? '' ) : '';
        if ( $token ) {
            $user_response = wp_remote_get( 'https://openidconnect.googleapis.com/v1/userinfo', array( 'timeout' => 15, 'headers' => array( 'Authorization' => 'Bearer ' . $token ) ) );
            $profile = ! is_wp_error( $user_response ) ? (array) json_decode( wp_remote_retrieve_body( $user_response ), true ) : array();
            if ( ! empty( $profile['picture'] ) ) { $profile['picture'] = esc_url_raw( $profile['picture'] ); }
        }
    } elseif ( 'facebook' === $provider ) {
        $app_id     = trim( (string) get_option( 'mdc_facebook_app_id', '' ) );
        $app_secret = trim( (string) get_option( 'mdc_facebook_app_secret', '' ) );
        $token_url  = add_query_arg( array( 'client_id' => $app_id, 'redirect_uri' => $callback, 'client_secret' => $app_secret, 'code' => $code ), 'https://graph.facebook.com/oauth/access_token' );
        $token_response = wp_remote_get( $token_url, array( 'timeout' => 15 ) );
        $token_body = ! is_wp_error( $token_response ) ? json_decode( wp_remote_retrieve_body( $token_response ), true ) : array();
        $token = is_array( $token_body ) ? (string) ( $token_body['access_token'] ?? '' ) : '';
        if ( $token ) {
            $profile_url = add_query_arg( array( 'fields' => 'id,name,email,picture.type(large)', 'access_token' => $token ), 'https://graph.facebook.com/me' );
            $profile_response = wp_remote_get( $profile_url, array( 'timeout' => 15 ) );
            $profile = ! is_wp_error( $profile_response ) ? (array) json_decode( wp_remote_retrieve_body( $profile_response ), true ) : array();
            if ( ! empty( $profile['picture']['data']['url'] ) ) { $profile['picture'] = esc_url_raw( $profile['picture']['data']['url'] ); }
        }
    }

    $user_id = mdc_conta_find_or_create_social_user( $provider, $profile );
    if ( is_wp_error( $user_id ) ) {
        wp_safe_redirect( mdc_conta_url( array( 'acao' => 'entrar', 'erro' => 'social' ) ) );
        exit;
    }

    wp_set_auth_cookie( $user_id, true, is_ssl() );
    wp_set_current_user( $user_id );
    wp_safe_redirect( ! empty( $session['redirect'] ) ? $session['redirect'] : home_url( '/' ) );
    exit;
}
add_action( 'template_redirect', 'mdc_conta_oauth_callback', 2 );

/** Força autenticação para comentar, sem depender da configuração do painel. */
function mdc_comentarios_exigem_login( $value ) {
    return 1;
}
add_filter( 'pre_option_comment_registration', 'mdc_comentarios_exigem_login' );

/** Cria a página de conta na ativação do tema. */
function mdc_criar_pagina_conta() {
    $page_id = absint( get_option( 'mdc_conta_page_id', 0 ) );
    if ( $page_id && 'trash' !== get_post_status( $page_id ) ) { return; }

    $existing = get_page_by_path( 'conta' );
    if ( $existing ) {
        update_post_meta( $existing->ID, '_wp_page_template', 'page-conta.php' );
        update_option( 'mdc_conta_page_id', $existing->ID, false );
        return;
    }

    $page_id = wp_insert_post(
        array(
            'post_title'  => 'Minha conta',
            'post_name'   => 'conta',
            'post_status' => 'publish',
            'post_type'   => 'page',
            'post_content' => '',
        )
    );
    if ( $page_id && ! is_wp_error( $page_id ) ) {
        update_post_meta( $page_id, '_wp_page_template', 'page-conta.php' );
        update_option( 'mdc_conta_page_id', $page_id, false );
    }
}
add_action( 'after_switch_theme', 'mdc_criar_pagina_conta', 20 );
add_action( 'init', 'mdc_criar_pagina_conta', 20 );

function mdc_conta_admin_settings() {
    add_settings_section( 'mdc_conta_oauth', 'Login por redes sociais', '__return_false', 'mdc-conta' );
    $fields = array(
        'mdc_google_client_id'     => 'Google Client ID',
        'mdc_google_client_secret' => 'Google Client Secret',
        'mdc_facebook_app_id'      => 'Facebook App ID',
        'mdc_facebook_app_secret'  => 'Facebook App Secret',
    );
    foreach ( $fields as $key => $label ) {
        register_setting( 'mdc-conta', $key, array( 'sanitize_callback' => 'sanitize_text_field' ) );
        add_settings_field( $key, $label, 'mdc_conta_admin_field', 'mdc-conta', 'mdc_conta_oauth', array( 'key' => $key ) );
    }
}
add_action( 'admin_init', 'mdc_conta_admin_settings' );

function mdc_conta_admin_field( $args ) {
    $key = $args['key'];
    printf( '<input type="text" class="regular-text" name="%1$s" value="%2$s" autocomplete="off">', esc_attr( $key ), esc_attr( get_option( $key, '' ) ) );
}

function mdc_conta_admin_menu() {
    add_theme_page( 'Conta e comentários', 'Conta e comentários', 'manage_options', 'mdc-conta', 'mdc_conta_admin_page' );
}
add_action( 'admin_menu', 'mdc_conta_admin_menu' );

function mdc_conta_admin_page() {
    $callback = add_query_arg( 'mdc_oauth', 'google', home_url( '/' ) );
    ?>
    <div class="wrap">
        <h1>Conta e comentários</h1>
        <p>Os comentários do portal exigem uma conta. O cadastro por e-mail cria leitores com o sistema de usuários do WordPress; Google e Facebook são opcionais.</p>
        <p><strong>URL de retorno OAuth:</strong><br><code><?php echo esc_html( $callback ); ?></code></p>
        <form method="post" action="options.php">
            <?php settings_fields( 'mdc-conta' ); do_settings_sections( 'mdc-conta' ); submit_button(); ?>
        </form>
        <hr>
        <h2>Cadastro de usuários</h2>
        <p>O cadastro pela página do tema cria usuários com o papel padrão definido no WordPress.</p>
        <p>A página <strong>Minha conta</strong> é criada automaticamente pelo tema em <code>/conta/</code>.</p>
    </div>
    <?php
}

function mdc_conta_render_message( $erro ) {
    $messages = array(
        'preencha'    => 'Preencha todos os campos obrigatórios.',
        'credenciais' => 'E-mail ou senha incorretos.',
        'existente'   => 'Já existe uma conta com este e-mail. Entre para continuar.',
        'email'       => 'Informe um endereço de e-mail válido.',
        'senha'       => 'A senha precisa ter pelo menos 8 caracteres.',
        'fechado'     => 'O cadastro de novos usuários está desativado no WordPress.',
        'falha'       => 'Não foi possível criar a conta. Tente novamente.',
        'social'      => 'Não foi possível concluir o login pela rede social. Verifique a configuração do aplicativo.',
    );
    return isset( $messages[ $erro ] ) ? $messages[ $erro ] : '';
}
