<?php
/**
 * Taxonomias do Mundo da Copa.
 *
 * O país é uma taxonomia única e reutilizável. As taxonomias
 * pais_selecao e pais_estadio são mantidas apenas como legadas,
 * ocultas da administração, para permitir migração segura dos
 * dados existentes.
 *
 * @package mundo-da-copa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mdc_tax_labels( $plural, $singular ) {
	return array(
		'name'              => $plural,
		'singular_name'     => $singular,
		'search_items'      => 'Buscar ' . strtolower( $plural ),
		'all_items'         => 'Todos',
		'edit_item'         => 'Editar ' . strtolower( $singular ),
		'update_item'       => 'Atualizar ' . strtolower( $singular ),
		'add_new_item'      => 'Adicionar ' . strtolower( $singular ),
		'new_item_name'     => 'Novo ' . strtolower( $singular ),
		'menu_name'         => $plural,
	);
}

function mdc_register_taxonomias() {
	$taxonomias = array(
		'categoria_copa' => array(
			'object_type' => array( 'copa' ),
			'labels'      => mdc_tax_labels( 'Categorias da Copa', 'Categoria da Copa' ),
			'hierarchical'=> false,
			'slug'        => 'categoria-copa',
		),
		'categoria_selecao' => array(
			'object_type' => array( 'selecao' ),
			'labels'      => mdc_tax_labels( 'Categorias das Seleções', 'Categoria da Seleção' ),
			'hierarchical'=> false,
			'slug'        => 'categoria-selecao',
		),
		'categoria_jogador' => array(
			'object_type' => array( 'jogador' ),
			'labels'      => mdc_tax_labels( 'Categorias dos Jogadores', 'Categoria do Jogador' ),
			'hierarchical'=> false,
			'slug'        => 'categoria-jogador',
		),
		'posicao_jogador' => array(
			'object_type' => array( 'jogador' ),
			'labels'      => mdc_tax_labels( 'Posições', 'Posição' ),
			'hierarchical'=> true,
			'slug'        => 'posicao',
		),
		'continente' => array(
			'object_type' => array( 'selecao', 'entidade' ),
			'labels'      => mdc_tax_labels( 'Continentes', 'Continente' ),
			'hierarchical'=> true,
			'slug'        => 'continente',
		),
		'pais' => array(
			'object_type' => array( 'selecao', 'estadio', 'entidade' ),
			'labels'      => mdc_tax_labels( 'Países', 'País' ),
			'hierarchical'=> false,
			'slug'        => 'pais',
		),
	);

	foreach ( $taxonomias as $taxonomy => $data ) {
		register_taxonomy(
			$taxonomy,
			$data['object_type'],
			array(
				'labels'            => $data['labels'],
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'show_in_nav_menus' => true,
				'hierarchical'      => $data['hierarchical'],
				'rewrite'           => array(
					'slug'       => $data['slug'],
					'with_front' => false,
				),
				'query_var'         => $taxonomy,
			)
		);
	}

	/* Compatibilidade de dados antigos — não aparecem no admin. */
	$legacy = array(
		'pais_selecao' => array( 'selecao' ),
		'pais_estadio' => array( 'estadio' ),
	);

	foreach ( $legacy as $taxonomy => $objects ) {
		register_taxonomy(
			$taxonomy,
			$objects,
			array(
				'labels'            => array(
					'name'          => ucfirst( str_replace( '_', ' ', $taxonomy ) ),
					'singular_name' => ucfirst( str_replace( '_', ' ', $taxonomy ) ),
				),
				'public'            => false,
				'show_ui'           => false,
				'show_admin_column' => false,
				'show_in_rest'      => false,
				'show_in_nav_menus' => false,
				'rewrite'           => false,
				'query_var'         => false,
			)
		);
	}
}
