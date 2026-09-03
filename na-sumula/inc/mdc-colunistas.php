<?php
/**
 * Recursos editoriais dos Colunistas.
 *
 * @package mundo-da-copa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ID do colunista vinculado ao post.
 */
function mdc_colunista_do_post( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	return absint( get_post_meta( $post_id, 'mdc_colunista', true ) );
}

/**
 * Lê campos novos e campos das versões anteriores.
 */
function mdc_colunista_meta_compat( $id, $key ) {
	$id = absint( $id );

	$aliases = array(
		'coluna'    => array( 'mdc_colunista_coluna', '_mdc_colunista_coluna' ),
		'cargo'     => array( 'mdc_colunista_cargo', '_mdc_colunista_cargo' ),
		'bio_curta' => array( 'mdc_colunista_bio_curta', '_mdc_colunista_bio_curta', 'mdc_colunista_bio', '_mdc_colunista_bio' ),
		'site'      => array( 'mdc_colunista_site', '_mdc_colunista_site' ),
		'x'         => array( 'mdc_colunista_x', '_mdc_colunista_x' ),
		'instagram' => array( 'mdc_colunista_instagram', '_mdc_colunista_instagram' ),
		'facebook'  => array( 'mdc_colunista_facebook', '_mdc_colunista_facebook' ),
		'linkedin'  => array( 'mdc_colunista_linkedin', '_mdc_colunista_linkedin' ),
	);

	$keys = isset( $aliases[ $key ] ) ? $aliases[ $key ] : array( $key );

	foreach ( $keys as $meta_key ) {
		$value = get_post_meta( $id, $meta_key, true );

		if ( '' !== $value && null !== $value ) {
			return $value;
		}
	}

	return '';
}

/**
 * Dados padronizados de um colunista.
 */
function mdc_dados_colunista( $id ) {
	$id = absint( $id );

	if ( ! $id || 'colunista' !== get_post_type( $id ) ) {
		return array();
	}

	return array(
		'id'        => $id,
		'nome'      => get_the_title( $id ),
		'url'       => get_permalink( $id ),
		'coluna'    => mdc_colunista_meta_compat( $id, 'coluna' ),
		'cargo'     => mdc_colunista_meta_compat( $id, 'cargo' ),
		'bio'       => mdc_colunista_meta_compat( $id, 'bio_curta' ),
		'site'      => mdc_colunista_meta_compat( $id, 'site' ),
		'x'         => mdc_colunista_meta_compat( $id, 'x' ),
		'instagram' => mdc_colunista_meta_compat( $id, 'instagram' ),
		'facebook'  => mdc_colunista_meta_compat( $id, 'facebook' ),
		'linkedin'  => mdc_colunista_meta_compat( $id, 'linkedin' ),
	);
}

/**
 * Posts publicados de um colunista, do mais recente para o mais antigo.
 */
function mdc_posts_do_colunista( $colunista_id, $limit = 12, $exclude = 0 ) {
	return get_posts(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			'posts_per_page' => max( 1, absint( $limit ) ),
			'meta_key'       => 'mdc_colunista',
			'meta_value'     => absint( $colunista_id ),
			'orderby'        => 'date',
			'order'          => 'DESC',
			'post__not_in'   => $exclude ? array( absint( $exclude ) ) : array(),
		)
	);
}

/**
 * Colunistas publicados para blocos editoriais.
 */
function mdc_colunistas_publicados( $limit = 4 ) {
    $limit = max( 1, absint( $limit ) );
    $featured = get_posts( array(
        'post_type'      => 'colunista', 'post_status' => 'publish', 'posts_per_page' => $limit,
        'meta_key'       => 'mdc_colunista_destaque', 'meta_value' => '1',
        'orderby' => 'title', 'order' => 'ASC', 'no_found_rows' => true,
    ) );
    if ( count( $featured ) >= $limit ) { return $featured; }
    $featured_ids = wp_list_pluck( $featured, 'ID' );
    $rest = get_posts( array(
        'post_type' => 'colunista', 'post_status' => 'publish', 'posts_per_page' => $limit - count( $featured ),
        'post__not_in' => $featured_ids, 'orderby' => 'title', 'order' => 'ASC', 'no_found_rows' => true,
    ) );
    return array_merge( $featured, $rest );
}
