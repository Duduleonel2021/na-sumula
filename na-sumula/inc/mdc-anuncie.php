<?php
/**
 * Na Súmula — Anuncie aqui e solicitações comerciais.
 *
 * LEGADO / DESATIVADO: substituído por page-anuncie.php +
 * page-anuncie-aqui.php, que hoje geram o template, o CSS e processam
 * o formulário real (campos e nonce diferentes dos usados aqui).
 * Mantido só por compatibilidade; as funções abaixo ficam protegidas
 * contra redeclaração e os hooks de execução foram removidos para não
 * competir com o sistema atual.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! function_exists( 'mdc_anuncio_page_url' ) ) {
function mdc_anuncio_page_url() {
    $id = absint( get_option( 'mdc_anuncio_page_id', 0 ) );
    if ( $id && 'publish' === get_post_status( $id ) ) return get_permalink( $id );
    return home_url( '/anuncie-aqui/' );
}
}

if ( ! function_exists( 'mdc_criar_pagina_anuncie' ) ) {
function mdc_criar_pagina_anuncie() {
    $id = absint( get_option( 'mdc_anuncio_page_id', 0 ) );
    if ( $id && 'trash' !== get_post_status( $id ) ) return;
    $existing = get_page_by_path( 'anuncie-aqui' );
    if ( $existing ) {
        update_post_meta( $existing->ID, '_wp_page_template', 'page-anuncie.php' );
        update_option( 'mdc_anuncio_page_id', $existing->ID, false );
        return;
    }
    $id = wp_insert_post(array(
        'post_title' => 'Anuncie aqui',
        'post_name' => 'anuncie-aqui',
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_content' => '',
    ));
    if ( $id && ! is_wp_error($id) ) { update_post_meta( $id, '_wp_page_template', 'page-anuncie.php' ); update_option('mdc_anuncio_page_id', $id, false); }
}
}
/* Hooks desligados: page-anuncie.php + page-anuncie-aqui.php já cuidam
 * de tudo isso hoje. Reativar geraria concorrência com o sistema atual.
add_action('after_switch_theme','mdc_criar_pagina_anuncie',21);
add_action('init','mdc_criar_pagina_anuncie',21);
*/

if ( ! function_exists( 'mdc_processar_solicitacao_anuncio' ) ) {
function mdc_processar_solicitacao_anuncio() {
    if ( ! is_page_template( 'page-anuncie.php' ) || 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
        return;
    }

    if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mdc_anuncio_nonce'] ?? '' ) ), 'mdc_anuncio_enviar' ) ) {
        return;
    }

    $nome          = sanitize_text_field( wp_unslash( $_POST['nome'] ?? '' ) );
    $empresa       = sanitize_text_field( wp_unslash( $_POST['empresa'] ?? '' ) );
    $email         = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
    $telefone      = sanitize_text_field( wp_unslash( $_POST['telefone'] ?? '' ) );
    $local         = sanitize_key( wp_unslash( $_POST['local'] ?? '' ) );
    $periodo       = sanitize_text_field( wp_unslash( $_POST['periodo'] ?? '' ) );
    $url           = esc_url_raw( wp_unslash( $_POST['url'] ?? '' ) );
    $mensagem      = sanitize_textarea_field( wp_unslash( $_POST['mensagem'] ?? '' ) );
    $consentimento = ! empty( $_POST['consentimento'] );

    $locais = array(
        'topo'          => 'Topo / Header',
        'apos-header'   => 'Após o Header',
        'home-destaque' => 'Home — Destaque',
        'home-meio'     => 'Home — Entre blocos',
        'sidebar'       => 'Sidebar',
        'post-inicio'   => 'Post — Após introdução',
        'post-meio'     => 'Post — Meio do conteúdo',
        'post-final'    => 'Post — Final do conteúdo',
        'antes-footer'  => 'Antes do Footer',
        'menu'          => 'Menu lateral',
        'mobile'        => 'Mobile',
    );

    $destino = sanitize_email( mdc_config( 'mdc_anuncio_email' ) );
    if ( ! $destino ) {
        $destino = sanitize_email( get_option( 'admin_email' ) );
    }

    if ( ! $nome || ! $empresa || ! is_email( $email ) || ! isset( $locais[ $local ] ) || ! $periodo || ! $consentimento || ! $destino ) {
        wp_safe_redirect( add_query_arg( 'anuncio', 'erro', mdc_anuncio_page_url() ) );
        exit;
    }

    $attachments = array();

    if ( empty( $_FILES['banner']['name'] ) || empty( $_FILES['banner']['tmp_name'] ) ) {
        wp_safe_redirect( add_query_arg( 'anuncio', 'erro', mdc_anuncio_page_url() ) );
        exit;
    }

    if ( ! empty( $_FILES['banner']['size'] ) && (int) $_FILES['banner']['size'] > 5 * 1024 * 1024 ) {
        wp_safe_redirect( add_query_arg( 'anuncio', 'erro', mdc_anuncio_page_url() ) );
        exit;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $upload = wp_handle_upload(
        $_FILES['banner'],
        array(
            'test_form' => false,
            'mimes'     => array(
                'jpg|jpeg|jpe' => 'image/jpeg',
                'png'          => 'image/png',
                'webp'         => 'image/webp',
                'gif'          => 'image/gif',
            ),
        )
    );

    if ( isset( $upload['error'] ) ) {
        wp_safe_redirect( add_query_arg( 'anuncio', 'erro', mdc_anuncio_page_url() ) );
        exit;
    }

    $attachment = array(
        'post_mime_type' => $upload['type'],
        'post_title'     => sanitize_text_field( pathinfo( $upload['file'], PATHINFO_FILENAME ) ),
        'post_content'   => '',
        'post_status'    => 'private',
    );

    $anexo = wp_insert_attachment( $attachment, $upload['file'] );

    if ( $anexo && ! is_wp_error( $anexo ) ) {
        wp_update_attachment_metadata( $anexo, wp_generate_attachment_metadata( $anexo, $upload['file'] ) );
        $attachments[] = $upload['file'];
    }

    $subject = 'Solicitação de publicidade — Na Súmula';
    $body  = "Nova solicitação de publicidade\n\n";
    $body .= "Nome: {$nome}\n";
    $body .= "Empresa / marca: {$empresa}\n";
    $body .= "E-mail: {$email}\n";
    $body .= "Telefone: {$telefone}\n";
    $body .= "Local de veiculação: " . ( $locais[ $local ] ?? $local ) . "\n";
    $body .= "Tempo de veiculação: {$periodo}\n";
    $body .= "Link de destino: {$url}\n\n";
    $body .= "Mensagem / observações:\n{$mensagem}\n";

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $nome . ' <' . $email . '>',
    );

    $sent = wp_mail( $destino, $subject, $body, $headers, $attachments );

    wp_safe_redirect( add_query_arg( 'anuncio', $sent ? 'ok' : 'erro', mdc_anuncio_page_url() ) );
    exit;
}
}
/* Hook desligado — o formulário real usa os campos e o nonce de
 * page-anuncie-aqui.php (ns_anuncie_*), não os deste arquivo.
add_action( 'template_redirect', 'mdc_processar_solicitacao_anuncio', 4 );
*/
