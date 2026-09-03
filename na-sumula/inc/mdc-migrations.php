<?php
/**
 * Migrações estruturais do Na Súmula.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* Migração existente de países. */
function mdc_migrar_paises() {
    if ( get_option( 'mdc_migracao_paises_100' ) ) return;

    $legacy = array(
        'pais_selecao' => 'selecao',
        'pais_estadio' => 'estadio',
    );
    foreach ( $legacy as $taxonomy => $post_type ) {
        $ids = get_posts( array(
            'post_type' => $post_type, 'post_status' => 'any',
            'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true,
        ) );
        foreach ( $ids as $post_id ) {
            $terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'names' ) );
            if ( ! is_wp_error( $terms ) && $terms ) wp_set_object_terms( $post_id, $terms, 'pais', true );
        }
    }

    $types_with_meta = array( 'selecao' );
    foreach ( $types_with_meta as $post_type ) {
        $ids = get_posts( array(
            'post_type' => $post_type, 'post_status' => 'any',
            'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true,
        ) );
        foreach ( $ids as $post_id ) {
            $pais = trim( (string) get_post_meta( $post_id, 'mdc_pais', true ) );
            if ( $pais ) wp_set_object_terms( $post_id, $pais, 'pais', true );
        }
    }
    update_option( 'mdc_migracao_paises_100', 1, false );
}
add_action( 'init', 'mdc_migrar_paises', 20 );

/**
 * Copia um registro legado para o CPT entidade.
 */
function mdc_migrar_entidade_registro( $post, $nivel ) {
    $existing = get_posts( array(
        'post_type' => 'entidade',
        'post_status' => 'any',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_key' => '_mdc_migrado_de',
        'meta_value' => $post->ID,
        'no_found_rows' => true,
    ) );
    if ( $existing ) return (int) $existing[0];

    $target_id = wp_insert_post( array(
        'post_type' => 'entidade',
        'post_status' => $post->post_status,
        'post_title' => $post->post_title,
        'post_content' => $post->post_content,
        'post_excerpt' => $post->post_excerpt,
        'post_author' => $post->post_author,
        'post_date' => $post->post_date,
        'post_date_gmt' => $post->post_date_gmt,
        'menu_order' => $post->menu_order,
        'comment_status' => $post->comment_status,
        'ping_status' => $post->ping_status,
    ), true );
    if ( is_wp_error( $target_id ) ) return 0;

    foreach ( get_post_meta( $post->ID ) as $key => $values ) {
        if ( in_array( $key, array( '_edit_lock', '_edit_last' ), true ) ) continue;
        delete_post_meta( $target_id, $key );
        foreach ( $values as $value ) add_post_meta( $target_id, $key, maybe_unserialize( $value ) );
    }

    update_post_meta( $target_id, 'mdc_nivel_entidade', $nivel );
    update_post_meta( $target_id, '_mdc_migrado_de', $post->ID );

    $thumb = get_post_thumbnail_id( $post->ID );
    if ( $thumb ) set_post_thumbnail( $target_id, $thumb );

    /* Preserva taxonomias compartilhadas e converte o país textual legado. */
    foreach ( array( 'pais', 'continente' ) as $taxonomy ) {
        if ( taxonomy_exists( $taxonomy ) ) {
            $term_ids = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );
            if ( ! is_wp_error( $term_ids ) && $term_ids ) {
                wp_set_object_terms( $target_id, $term_ids, $taxonomy, false );
            }
        }
    }

    if ( 'nacional' === $nivel ) {
        $pais_texto = trim( (string) get_post_meta( $post->ID, 'mdc_pais', true ) );
        if ( $pais_texto && taxonomy_exists( 'pais' ) ) {
            wp_set_object_terms( $target_id, $pais_texto, 'pais', true );
        }
    }

    return (int) $target_id;
}

function mdc_migrar_relacionamentos_entidades( $map ) {
    if ( ! $map ) return;

    /* Atualiza IDs antigos mantendo as chaves legadas durante a transição. */
    $posts = get_posts( array(
        'post_type' => 'any', 'post_status' => 'any', 'posts_per_page' => -1,
        'fields' => 'ids', 'no_found_rows' => true,
    ) );

    foreach ( $posts as $post_id ) {
        foreach ( array( 'mdc_federacao', 'mdc_confederacao' ) as $key ) {
            $values = get_post_meta( $post_id, $key, false );
            if ( ! $values ) continue;

            $new_values = array();
            foreach ( $values as $value ) {
                if ( is_array( $value ) ) {
                    foreach ( $value as $item ) {
                        $item = absint( $item );
                        if ( isset( $map[ $item ] ) ) $item = $map[ $item ];
                        if ( $item ) $new_values[] = $item;
                    }
                } else {
                    $item = absint( $value );
                    if ( isset( $map[ $item ] ) ) $item = $map[ $item ];
                    if ( $item ) $new_values[] = $item;
                }
            }

            $new_values = array_values( array_unique( $new_values ) );
            delete_post_meta( $post_id, $key );
            foreach ( $new_values as $value ) add_post_meta( $post_id, $key, $value );
        }
    }
}

function mdc_migrar_entidades() {
    if ( get_option( 'mdc_entidade_migracao_concluida', false ) ) return;

    $map = array();

    foreach ( array( 'federacao' => 'nacional', 'confederacao' => 'continental' ) as $source_type => $nivel ) {
        $posts = get_posts( array(
            'post_type' => $source_type, 'post_status' => 'any',
            'posts_per_page' => -1, 'no_found_rows' => true,
        ) );
        foreach ( $posts as $post ) {
            $new_id = mdc_migrar_entidade_registro( $post, $nivel );
            if ( $new_id ) $map[ $post->ID ] = $new_id;
        }
    }

    mdc_migrar_relacionamentos_entidades( $map );

    update_option( 'mdc_entidade_migracao_map_v1', $map, false );
    update_option( 'mdc_entidade_migracao_concluida', 1, false );
    flush_rewrite_rules( false );
}
add_action( 'init', 'mdc_migrar_entidades', 30 );
