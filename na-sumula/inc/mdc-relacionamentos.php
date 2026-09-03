<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Funções auxiliares para relacionamentos armazenados em post_meta.
 */
function mdc_get_related_ids( $post_id, $meta_key ) {
    $value = get_post_meta( $post_id, $meta_key, true );
    if ( empty($value) ) return array();
    return array_values(array_filter(array_map('absint', (array)$value)));
}

function mdc_get_related_posts( $post_id, $meta_key, $post_type = '' ) {
    $ids = mdc_get_related_ids( $post_id, $meta_key );
    if ( ! $ids ) return array();
    $args = array(
        'post__in' => $ids,
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'post__in',
    );
    if ( $post_type ) $args['post_type'] = $post_type;
    return get_posts($args);
}
