<?php
/**
 * Na Súmula — Newsletter.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function mdc_newsletter_inscrever() {
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' ) ) {
		wp_safe_redirect( home_url( '/' ) );
		exit;
	}
	$nonce = sanitize_text_field( wp_unslash( $_POST['mdc_newsletter_nonce'] ?? '' ) );
	if ( ! wp_verify_nonce( $nonce, 'mdc_newsletter_inscrever' ) ) {
		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : home_url( '/' ) );
		exit;
	}

	$email = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	if ( ! is_email( $email ) ) {
		wp_safe_redirect( add_query_arg( 'newsletter', 'erro', wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) );
		exit;
	}

	$subscribers = get_option( 'mdc_newsletter_subscribers', array() );
	$subscribers = is_array( $subscribers ) ? $subscribers : array();
	$normalized  = strtolower( $email );

	foreach ( $subscribers as $subscriber ) {
		if ( strtolower( (string) ( $subscriber['email'] ?? '' ) ) === $normalized ) {
			wp_safe_redirect( add_query_arg( 'newsletter', 'ok', wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) );
			exit;
		}
	}

	$subscribers[] = array(
		'email' => $email,
		'date'  => current_time( 'mysql' ),
	);
	update_option( 'mdc_newsletter_subscribers', $subscribers, false );

	$destino = sanitize_email( mdc_config( 'mdc_newsletter_email' ) );
	if ( ! $destino ) {
		$destino = sanitize_email( mdc_config( 'mdc_anuncio_email' ) );
	}
	if ( ! $destino ) {
		$destino = sanitize_email( get_option( 'admin_email' ) );
	}

	if ( $destino ) {
		wp_mail(
			$destino,
			'Nova inscrição na newsletter — Na Súmula',
			"Novo leitor inscrito na newsletter.\n\nE-mail: {$email}\nData: " . current_time( 'mysql' ),
			array( 'Content-Type: text/plain; charset=UTF-8' )
		);
	}

	wp_safe_redirect( add_query_arg( 'newsletter', 'ok', wp_get_referer() ? wp_get_referer() : home_url( '/' ) ) );
	exit;
}
add_action( 'admin_post_nopriv_mdc_newsletter_inscrever', 'mdc_newsletter_inscrever' );
add_action( 'admin_post_mdc_newsletter_inscrever', 'mdc_newsletter_inscrever' );

function mdc_newsletter_count() {
	$list = get_option( 'mdc_newsletter_subscribers', array() );
	return is_array( $list ) ? count( $list ) : 0;
}
