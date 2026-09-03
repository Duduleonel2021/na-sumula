<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * CPTs editoriais do Na Súmula.
 *
 * Federação e Confederação foram unificadas em "Entidade".
 * Os CPTs antigos são registrados apenas durante a migração e, depois,
 * permanecem ocultos para compatibilidade com registros/URLs antigos.
 */
function mdc_register_cpts() {
    $types = array(
        'copa' => array( 'singular'=>'Copa', 'plural'=>'Copas', 'slug'=>'copas', 'icon'=>'dashicons-awards' ),
        'selecao' => array( 'singular'=>'Seleção', 'plural'=>'Seleções', 'slug'=>'selecoes', 'icon'=>'dashicons-flag' ),
        'jogador' => array( 'singular'=>'Jogador', 'plural'=>'Jogadores', 'slug'=>'jogadores', 'icon'=>'dashicons-groups' ),
        'estadio' => array( 'singular'=>'Estádio', 'plural'=>'Estádios', 'slug'=>'estadios', 'icon'=>'dashicons-building' ),
        'entidade' => array( 'singular'=>'Entidade', 'plural'=>'Entidades', 'slug'=>'entidades', 'icon'=>'dashicons-networking' ),
        'colunista' => array( 'singular'=>'Colunista', 'plural'=>'Colunistas', 'slug'=>'colunistas', 'icon'=>'dashicons-edit-page' ),
    );

    foreach ( $types as $slug => $data ) {
        register_post_type( $slug, array(
            'labels' => array(
                'name' => $data['plural'],
                'singular_name' => $data['singular'],
                'add_new_item' => 'Adicionar ' . $data['singular'],
                'edit_item' => 'Editar ' . $data['singular'],
                'new_item' => 'Novo ' . $data['singular'],
                'view_item' => 'Ver ' . $data['singular'],
                'search_items' => 'Buscar ' . $data['plural'],
                'not_found' => 'Nenhum registro encontrado.',
                'menu_name' => $data['plural'],
            ),
            'public' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'has_archive' => true,
            'rewrite' => array( 'slug' => $data['slug'], 'with_front' => false ),
            'menu_icon' => $data['icon'],
            'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
            'publicly_queryable' => true,
            'exclude_from_search' => false,
        ) );
    }

    if ( ! get_option( 'mdc_entidade_migracao_concluida', false ) ) {
        foreach ( array(
            'federacao' => array( 'Federação', 'Federações', 'federacoes', 'dashicons-networking' ),
            'confederacao' => array( 'Confederação', 'Confederações', 'confederacoes', 'dashicons-admin-site-alt3' ),
        ) as $slug => $data ) {
            register_post_type( $slug, array(
                'labels' => array(
                    'name' => $data[1],
                    'singular_name' => $data[0],
                    'menu_name' => $data[1],
                ),
                'public' => false,
                'publicly_queryable' => true,
                'show_ui' => false,
                'show_in_rest' => false,
                'exclude_from_search' => true,
                'has_archive' => false,
                'rewrite' => array( 'slug' => $data[2], 'with_front' => false ),
                'query_var' => true,
                'supports' => array( 'title' ),
                'menu_icon' => $data[3],
            ) );
        }
    }
}
