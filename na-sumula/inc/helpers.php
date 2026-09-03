<?php
/**
 * Funções auxiliares do tema Mundo da Copa.
 *
 * @package mundo-da-copa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ID da foto personalizada do autor.
 *
 * Aceita os nomes de meta usados nas versões anteriores do tema.
 * Quando não houver foto personalizada, retorna 0 para que os templates
 * utilizem o avatar padrão do WordPress.
 *
 * @param int|null $user_id ID do usuário.
 * @return int
 */
function mdc_autor_foto_id( $user_id = null ) {
	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();

	if ( ! $user_id ) {
		return 0;
	}

	$chaves = array(
		'mdc_autor_foto_id',
		'mdc_autor_foto',
		'mdc_foto_autor',
		'mdc_foto',
	);

	foreach ( $chaves as $chave ) {
		$valor = get_user_meta( $user_id, $chave, true );

		if ( is_array( $valor ) ) {
			$valor = reset( $valor );
		}

		$valor = absint( $valor );

		if ( $valor && 'attachment' === get_post_type( $valor ) ) {
			return $valor;
		}
	}

	return 0;
}

/**
 * Nome real da chave em post_meta.
 *
 * Todos os campos do tema são gravados com o prefixo mdc_ — boa prática, evita
 * colisão com outros plugins. Os templates chamam mdc_field( 'sigla' ) e esta
 * função resolve para 'mdc_sigla'. Chaves já prefixadas passam intactas.
 *
 * @param string $name Nome do campo.
 * @return string
 */
function mdc_meta_key( $name ) {
	$name = (string) $name;

	if ( 0 === strpos( $name, 'mdc_' ) || 0 === strpos( $name, '_' ) ) {
		return $name;
	}

	return 'mdc_' . $name;
}

/**
 * Lê um campo armazenado em post_meta.
 *
 * @param string   $name    Nome do campo.
 * @param int|null $post_id ID do post.
 * @return mixed
 */
function mdc_field( $name, $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	return get_post_meta( $post_id, mdc_meta_key( $name ), true );
}

/**
 * Lê um campo de relacionamento e devolve IDs publicados.
 *
 * @param string   $name    Nome do campo.
 * @param int|null $post_id ID do post.
 * @return int[]
 */
function mdc_field_ids( $name, $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$chave   = mdc_meta_key( $name );

	// Relacionamentos são gravados como uma linha de meta por item: assim o
	// meta_query da busca inversa funciona com comparação exata, sem LIKE.
	$valores = get_post_meta( $post_id, $chave, false );

	// Compatibilidade com dados antigos gravados como array serializado.
	if ( 1 === count( $valores ) && is_array( $valores[0] ) ) {
		$valores = $valores[0];
	}

	if ( empty( $valores ) ) {
		return array();
	}

	$ids = array();

	foreach ( $valores as $item ) {
		if ( $item instanceof WP_Post ) {
			$id = (int) $item->ID;
		} elseif ( is_array( $item ) && isset( $item['ID'] ) ) {
			$id = (int) $item['ID'];
		} else {
			$id = (int) $item;
		}

		if ( $id && 'publish' === get_post_status( $id ) ) {
			$ids[] = $id;
		}
	}

	return array_values( array_unique( $ids ) );
}

/**
 * Primeiro ID de um relacionamento.
 *
 * @param string   $name    Nome do campo.
 * @param int|null $post_id ID do post.
 * @return int
 */
function mdc_field_id( $name, $post_id = null ) {
	$ids = mdc_field_ids( $name, $post_id );

	return $ids ? (int) $ids[0] : 0;
}

/**
 * Ordena Copas pelo ano.
 *
 * @param int[] $ids IDs.
 * @param bool  $asc Ordem crescente.
 * @return int[]
 */
function mdc_sort_copas_por_ano( $ids, $asc = false ) {
	if ( count( $ids ) < 2 ) {
		return $ids;
	}

	usort(
		$ids,
		function ( $a, $b ) use ( $asc ) {
			$ano_a = (int) mdc_field( 'mdc_ano', $a );
			$ano_b = (int) mdc_field( 'mdc_ano', $b );

			return $asc ? $ano_a <=> $ano_b : $ano_b <=> $ano_a;
		}
	);

	return $ids;
}

/**
 * Título seguro.
 *
 * @param int $id ID.
 * @return string
 */
function mdc_title( $id ) {
	$title = get_the_title( $id );

	return $title ? $title : __( '(sem título)', 'mundo-da-copa' );
}

/**
 * Primeiro termo de uma taxonomia.
 *
 * @param string   $taxonomy Taxonomia.
 * @param int|null $post_id ID.
 * @return string
 */
/**
 * Retorna o rótulo editorial masculino/feminino de um registro.
 */
function mdc_genero_label( $post_id = 0 ) {
	$post_id   = $post_id ? (int) $post_id : get_the_ID();
	$post_type = get_post_type( $post_id );
	$map       = array(
		'copa'    => 'categoria_copa',
		'selecao' => 'categoria_selecao',
		'jogador' => 'categoria_jogador',
	);

	if ( ! isset( $map[ $post_type ] ) ) {
		return '';
	}

	$term = wp_get_post_terms( $post_id, $map[ $post_type ], array( 'number' => 1 ) );

	if ( is_wp_error( $term ) || empty( $term ) ) {
		return '';
	}

	return ( 'feminina' === $term[0]->slug || 'feminino' === $term[0]->slug ) ? 'Futebol feminino' : 'Futebol masculino';
}

function mdc_term_name( $taxonomy, $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$terms   = get_the_terms( $post_id, $taxonomy );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}

	return $terms[0]->name;
}

/**
 * Link do primeiro termo.
 *
 * @param string   $taxonomy Taxonomia.
 * @param int|null $post_id ID.
 * @return string
 */
function mdc_term_link( $taxonomy, $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$terms   = get_the_terms( $post_id, $taxonomy );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}

	$link = get_term_link( $terms[0] );

	return is_wp_error( $link ) ? '' : $link;
}

/**
 * Rótulo de um tipo de conteúdo.
 *
 * @param string $post_type Tipo.
 * @param bool   $plural    Plural.
 * @return string
 */
function mdc_post_type_label( $post_type, $plural = false ) {
	$labels = array(
		'copa'          => 'Copa',
		'selecao'       => 'Seleção',
		'jogador'       => 'Jogador',
		'estadio'       => 'Estádio',
		'entidade'      => 'Entidade',
		'post'          => 'Reportagem',
	);

	$plurais = array(
		'copa'          => 'Copas',
		'selecao'       => 'Seleções',
		'jogador'       => 'Jogadores',
		'estadio'       => 'Estádios',
		'entidade'      => 'Entidades',
		'post'          => 'Reportagens',
	);

	$lista = $plural ? $plurais : $labels;

	return isset( $lista[ $post_type ] ) ? __( $lista[ $post_type ], 'mundo-da-copa' ) : '';
}

/**
 * Breadcrumb.
 *
 * @param array $items Itens.
 * @return void
 */
function mdc_breadcrumb( $items = null ) {
	/*
	 * Quando nenhum conjunto de itens é informado, monta o breadcrumb
	 * automaticamente de acordo com o contexto atual do WordPress.
	 * Isso evita chamadas sem argumento nos templates e mantém a função
	 * compatível com chamadas personalizadas.
	 */
	if ( null === $items ) {
		$items = array(
			array(
				'label' => __( 'Início', 'mundo-da-copa' ),
				'url'   => home_url( '/' ),
			),
		);

		if ( is_singular() ) {
			$post_id   = get_queried_object_id();
			$post_type = get_post_type( $post_id );

			if ( 'post' === $post_type ) {
				$categories = get_the_category( $post_id );
				if ( ! empty( $categories ) ) {
					$category = $categories[0];
					$items[] = array(
						'label' => $category->name,
						'url'   => get_category_link( $category->term_id ),
					);
				}
			} elseif ( $post_type && function_exists( 'mdc_post_type_label' ) ) {
				$archive_url = get_post_type_archive_link( $post_type );
				$items[] = array(
					'label' => mdc_post_type_label( $post_type, true ),
					'url'   => $archive_url ? $archive_url : '',
				);
			}

			$items[] = array(
				'label' => get_the_title( $post_id ),
				'url'   => '',
			);
		} elseif ( is_category() ) {
			$items[] = array( 'label' => single_cat_title( '', false ), 'url' => '' );
		} elseif ( is_tax() ) {
			$items[] = array( 'label' => single_term_title( '', false ), 'url' => '' );
		} elseif ( is_page() ) {
			$items[] = array( 'label' => get_the_title(), 'url' => '' );
		} elseif ( is_search() ) {
			$items[] = array(
				'label' => sprintf( __( 'Busca: %s', 'mundo-da-copa' ), get_search_query() ),
				'url'   => '',
			);
		}
	}

	if ( empty( $items ) ) {
		return;
	}

	echo '<nav class="mdc-breadcrumb" aria-label="' .
		esc_attr__( 'Você está aqui', 'mundo-da-copa' ) .
		'"><ol>';

	foreach ( $items as $item ) {
		$label = isset( $item['label'] ) ? $item['label'] : '';
		$url   = isset( $item['url'] ) ? $item['url'] : '';

		if ( '' === $label ) {
			continue;
		}

		echo '<li>';

		if ( $url ) {
			printf(
				'<a href="%s">%s</a>',
				esc_url( $url ),
				esc_html( $label )
			);
		} else {
			printf(
				'<span aria-current="page">%s</span>',
				esc_html( $label )
			);
		}

		echo '</li>';
	}

	echo '</ol></nav>';
}

/**
 * Reportagens relacionadas.
 *
 * @param string $campo   Campo manual.
 * @param string $inverso Campo inverso.
 * @param int    $limite  Quantidade.
 * @return int[]
 */
/**
 * Retorna até quatro reportagens relacionadas, priorizando os vínculos
 * escolhidos pelo editor e completando com matérias da mesma categoria.
 *
 * @param int $post_id ID da reportagem atual.
 * @param int $limite Quantidade máxima.
 * @return int[]
 */
function mdc_posts_relacionados( $post_id = 0, $limite = 4 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$limite  = max( 1, absint( $limite ) );
	$ids     = array();

	// Relações escolhidas manualmente pelo editor têm prioridade.
	$manual = mdc_field_ids( 'reportagens_relacionadas', $post_id );
	foreach ( $manual as $id ) {
		$id = absint( $id );
		if ( $id && $id !== $post_id && 'post' === get_post_type( $id ) && 'publish' === get_post_status( $id ) ) {
			$ids[] = $id;
		}
		if ( count( $ids ) >= $limite ) {
			return array_slice( array_values( array_unique( $ids ) ), 0, $limite );
		}
	}

	// Completa por tags em comum.
	$remaining = $limite - count( $ids );
	$tags = wp_get_post_tags( $post_id, array( 'fields' => 'ids' ) );
	if ( $remaining > 0 && $tags ) {
		$query = new WP_Query(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => $remaining,
				'post__not_in'        => array_merge( array( $post_id ), $ids ),
				'tag__in'             => array_map( 'absint', $tags ),
				'orderby'             => 'date',
				'order'               => 'DESC',
				'fields'              => 'ids',
				'no_found_rows'       => true,
				'ignore_sticky_posts' => true,
			)
		);
		$ids = array_merge( $ids, array_map( 'absint', (array) $query->posts ) );
	}

	// Último recurso: posts da mesma categoria.
	$remaining = $limite - count( $ids );
	$categories = wp_get_post_categories( $post_id );
	if ( $remaining > 0 && $categories ) {
		$query = new WP_Query(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => $remaining,
				'post__not_in'        => array_merge( array( $post_id ), $ids ),
				'category__in'        => array_map( 'absint', $categories ),
				'orderby'             => 'date',
				'order'               => 'DESC',
				'fields'              => 'ids',
				'no_found_rows'       => true,
				'ignore_sticky_posts' => true,
			)
		);
		$ids = array_merge( $ids, array_map( 'absint', (array) $query->posts ) );
	}

	return array_slice( array_values( array_unique( array_map( 'absint', $ids ) ) ), 0, $limite );
}

function mdc_reportagens_relacionadas( $campo, $inverso = '', $limite = 3 ) {
	$ids = mdc_field_ids( $campo );

	if ( $ids ) {
		return array_slice( $ids, 0, $limite );
	}

	if ( ! $inverso ) {
		return array();
	}

	$atual = get_the_ID();

	$query = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $limite,
			'fields'              => 'ids',
			'no_found_rows'       => true,
			'ignore_sticky_posts' => true,
			'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'key'     => mdc_meta_key( $inverso ),
					'value'   => $atual,
					'compare' => '=',
				),
			),
		)
	);

	return $query->posts;
}

/**
 * Taça estilizada da marca.
 *
 * @param array $args Argumentos.
 * @return string
 */
function mdc_trophy_svg( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'size'  => 44,
			'class' => 'mdc-trophy',
		)
	);

	$altura = (int) round( $args['size'] * 1.12 );

	ob_start();
	?>
	<svg
		class="<?php echo esc_attr( $args['class'] ); ?>"
		width="<?php echo esc_attr( (int) $args['size'] ); ?>"
		height="<?php echo esc_attr( $altura ); ?>"
		viewBox="0 0 64 72"
		fill="none"
		aria-hidden="true"
		focusable="false"
		xmlns="http://www.w3.org/2000/svg"
	>
		<g stroke-linecap="round" stroke-linejoin="round" fill="none">
			<path
				class="mdc-trophy__accent"
				d="M18 30a15 15 0 1 1 28 0"
				stroke="var(--mdc-destaque, var(--mdc-verde))"
				stroke-width="3.2"
			/>
			<path
				d="M22 22c-2 15 10 19 9 30"
				stroke="currentColor"
				stroke-width="3.2"
			/>
			<path
				class="mdc-trophy__accent"
				d="M42 22c2 15-10 19-9 30"
				stroke="var(--mdc-destaque, var(--mdc-verde))"
				stroke-width="3.2"
			/>
			<path
				d="M17 57c9-4 21-4 30 0"
				stroke="currentColor"
				stroke-width="3.2"
			/>
			<path
				class="mdc-trophy__accent"
				d="M13 63c12-5 26-5 38 0"
				stroke="var(--mdc-destaque, var(--mdc-verde))"
				stroke-width="3.2"
			/>
			<circle
				cx="40"
				cy="17"
				r="3.4"
				stroke="currentColor"
				stroke-width="3"
			/>
		</g>
	</svg>
	<?php

	return ob_get_clean();
}

/**
 * Ícones SVG da interface.
 *
 * @param string $name Nome.
 * @param int    $size Tamanho.
 * @return string
 */
function mdc_icon( $name, $size = 20 ) {
	$paths = array(
		'trophy'   => '<path d="M8 21h8m-4-4v4m6-17H6v5a6 6 0 0 0 12 0V4Zm0 1h3v2a4 4 0 0 1-4 4M6 5H3v2a4 4 0 0 1 4 4"/>',
		'globe'    => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18 15 15 0 0 1 0-18Z"/>',
		'users'    => '<path d="M16 20v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 20v-2a4 4 0 0 0-3-3.9"/>',
		'star'     => '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.2-5.4-2.9-5.4 2.9 1-6.2L3.2 9.5l6.1-.9L12 3Z"/>',
		'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4m8-4v4M3 11h18"/>',
		'shield'   => '<path d="M12 3l8 3v6c0 5-3.4 8.4-8 9.6C7.4 20.4 4 17 4 12V6l8-3Z"/>',
		'flag'     => '<path d="M5 21V4m0 0h11l-1.6 3.5L16 11H5"/>',
		'search'   => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.6-3.6"/>',
		'menu'     => '<path d="M4 7h16M4 12h16M4 17h16"/>',
		'close'    => '<path d="M6 6l12 12M18 6 6 18"/>',
		'sun'      => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4m11.4-11.4 1.4-1.4"/>',
		'moon'     => '<path d="M21 12.8A8.5 8.5 0 1 1 11.2 3 6.5 6.5 0 0 0 21 12.8Z"/>',
		'arrow'    => '<path d="M5 12h14m-6-6 6 6-6 6"/>',
		'lista'    => '<path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/>',
		'relogio'  => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'comment'  => '<path d="M21 15a2 2 0 0 1-2 2H8l-4 4V6a2 2 0 0 1 2-2h13a2 2 0 0 1 2 2Z"/>',
		'link'     => '<path d="M10 13a5 5 0 0 0 7 0l2-2a5 5 0 0 0-7-7l-1 1"/><path d="M14 11a5 5 0 0 0-7 0l-2 2a5 5 0 0 0 7 7l1-1"/>',
		'facebook' => '<path d="M14 8h3V5h-3c-2.8 0-5 2.2-5 5v2H6v3h3v6h3v-6h3l1-3h-4v-2c0-1.1.9-2 2-2Z"/>',
		'x'        => '<path d="M5 4l14 16M19 4 5 20"/>',
		'instagram'=> '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".8" fill="currentColor" stroke="none"/>',
		'youtube'  => '<path d="M21 7.5a2.5 2.5 0 0 0-1.8-1.8C17.6 5.3 12 5.3 12 5.3s-5.6 0-7.2.4A2.5 2.5 0 0 0 3 7.5 26 26 0 0 0 2.7 12 26 26 0 0 0 3 16.5a2.5 2.5 0 0 0 1.8 1.8c1.6.4 7.2.4 7.2.4s5.6 0 7.2-.4a2.5 2.5 0 0 0 1.8-1.8 26 26 0 0 0 .3-4.5 26 26 0 0 0-.3-4.5Z"/><path d="m10 9 5 3-5 3V9Z"/>',
		'radio'    => '<circle cx="12" cy="12" r="2.5"/><path d="M5.6 5.6a9 9 0 0 0 0 12.8M18.4 5.6a9 9 0 0 1 0 12.8"/>',
		'whatsapp' => '<path d="M20 4.1A9.9 9.9 0 0 0 3.2 15.9L2 22l6.2-1.6A9.9 9.9 0 1 0 20 4.1Z"/><path d="M8.2 7.5c.3-.7.6-.7 1-.7h.7c.3 0 .5.1.7.6l.9 2c.1.3.1.5-.1.8l-.7.8c.8 1.6 2 2.7 3.6 3.5l.8-.8c.2-.2.5-.3.8-.1l2 .9c.5.2.6.4.6.7v.7c0 .4 0 .7-.7 1-1 .4-2.2.3-3.3-.2-2.1-1-4.8-3.6-5.9-5.7-.5-1.1-.6-2.3-.2-3.3Z"/>',
		'linkedin' => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 10v7M8 7v.01M12 17v-4a2 2 0 0 1 4 0v4"/>',
		'email'    => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
		'trending' => '<path d="M3 17 9 11l4 4 7-8"/><path d="M16 7h4v4"/>',
	);

	if ( ! isset( $paths[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg class="mdc-icon mdc-icon--%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $name ),
		(int) $size,
		$paths[ $name ]
	);
}
/**
 * Data formatada em português.
 *
 * @param int|null $post_id ID do post.
 * @return string
 */
function mdc_data( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	return get_the_date( 'j \\d\\e F \\d\\e Y', $post_id );
}

/**
 * Data editorial completa, com fallback seguro para qualquer template.
 *
 * @param int|null $post_id ID do post.
 * @return string
 */
if ( ! function_exists( 'mdc_format_date_editorial' ) ) {
	function mdc_format_date_editorial( $post_id = null ) {
		return mdc_data( $post_id );
	}
}

/**
 * Período legível a partir de duas datas no formato Y-m-d.
 *
 * @param string $inicio Data inicial.
 * @param string $fim    Data final.
 * @return string
 */
function mdc_periodo( $inicio, $fim ) {
	$d1 = $inicio ? strtotime( $inicio ) : 0;
	$d2 = $fim ? strtotime( $fim ) : 0;

	if ( ! $d1 && ! $d2 ) {
		return '';
	}

	if ( ! $d1 || ! $d2 ) {
		return date_i18n( 'j \\d\\e F \\d\\e Y', $d1 ? $d1 : $d2 );
	}

	// Mesmo ano: "20 de novembro a 18 de dezembro de 2022".
	if ( date_i18n( 'Y', $d1 ) === date_i18n( 'Y', $d2 ) ) {
		return date_i18n( 'j \\d\\e F', $d1 ) . ' a ' . date_i18n( 'j \\d\\e F \\d\\e Y', $d2 );
	}

	return date_i18n( 'j \\d\\e F \\d\\e Y', $d1 ) . ' a ' . date_i18n( 'j \\d\\e F \\d\\e Y', $d2 );
}

/**
 * Imprime um bloco de texto vindo de um campo longo do registro.
 *
 * @param string   $campo     Nome do campo.
 * @param string   $titulo    Título do bloco.
 * @param bool     $capitular Aplica a letra capitular.
 * @param int|null $post_id   ID do post.
 * @return void
 */
function mdc_bloco_texto( $campo, $titulo, $capitular = false, $post_id = null ) {
	$valor = mdc_field( $campo, $post_id );

	if ( ! $valor || is_array( $valor ) ) {
		return;
	}

	$classe = 'mdc-texto';

	if ( $capitular && function_exists( 'mdc_config' ) && mdc_config( 'mdc_post_capitular' ) ) {
		$classe .= ' mdc-texto--capitular';
	}

	echo '<section class="mdc-bloco">';
	echo '<h2 class="mdc-bloco__titulo">' . esc_html( $titulo ) . '</h2>';
	echo '<div class="' . esc_attr( $classe ) . '">' . wp_kses_post( wpautop( $valor ) ) . '</div>';
	echo '</section>';
}

/**
 * Nome no plural de um tipo de conteúdo.
 *
 * Lê o rótulo registrado no próprio CPT, então acompanha automaticamente
 * qualquer mudança feita em inc/mdc-cpts.php.
 *
 * @param string $post_type Tipo.
 * @return string
 */
function mdc_post_type_plural( $post_type ) {
	$objeto = get_post_type_object( $post_type );

	if ( $objeto && ! empty( $objeto->labels->name ) ) {
		return $objeto->labels->name;
	}

	return mdc_post_type_label( $post_type );
}

/**
 * Converte um campo de texto multilinha em lista de itens.
 *
 * Usado nos campos "Clubes", "Campeonatos" e afins, em que o editor digita
 * um item por linha.
 *
 * @param string   $campo   Nome do campo.
 * @param int|null $post_id ID do post.
 * @return string[]
 */
function mdc_lista_linhas( $campo, $post_id = null ) {
	$valor = mdc_field( $campo, $post_id );

	if ( ! $valor || is_array( $valor ) ) {
		return array();
	}

	$linhas = preg_split( '/\r\n|\r|\n/', wp_strip_all_tags( $valor ) );
	$linhas = array_map( 'trim', $linhas );

	return array_values( array_filter( $linhas ) );
}

/**
 * Lê o campo de redes sociais no formato "Rótulo|URL" por linha.
 *
 * @param int|null $post_id ID do post.
 * @return array Lista com rotulo, url e slug.
 */
function mdc_redes_registro( $post_id = null ) {
	$saida = array();

	foreach ( mdc_lista_linhas( 'redes', $post_id ) as $linha ) {
		$partes = array_map( 'trim', explode( '|', $linha, 2 ) );

		if ( count( $partes ) < 2 || ! $partes[1] ) {
			continue;
		}

		$saida[] = array(
			'rotulo' => $partes[0],
			'url'    => esc_url_raw( $partes[1] ),
			'slug'   => sanitize_title( $partes[0] ),
		);
	}

	return $saida;
}

/**
 * Data legível a partir de um campo de data (Y-m-d).
 *
 * @param string $valor Data.
 * @return string
 */
function mdc_data_campo( $valor ) {
	if ( ! $valor ) {
		return '';
	}

	$ts = strtotime( $valor );

	return $ts ? date_i18n( 'j \d\e F \d\e Y', $ts ) : '';
}

/**
 * Data legível com tempo decorrido desde a data informada.
 * Ex.: 21 de junho de 1916 (110 anos).
 *
 * @param string $valor Data no formato Y-m-d.
 * @return string
 */
function mdc_data_campo_com_tempo( $valor ) {
	if ( ! $valor ) {
		return '';
	}

	$ts = strtotime( $valor );

	if ( ! $ts ) {
		return '';
	}

	$data = date_i18n( 'j \d\e F \d\e Y', $ts );
	$inicio = new DateTime( date( 'Y-m-d', $ts ) );
	$hoje = new DateTime( current_time( 'Y-m-d' ) );

	if ( $inicio > $hoje ) {
		return $data;
	}

	$diff = $inicio->diff( $hoje );

	return $data . ' (' . (int) $diff->y . ' ' . _n( 'ano', 'anos', (int) $diff->y, 'mundo-da-copa' ) . ')';
}

/**
 * Idade a partir da data de nascimento (e de morte, quando houver).
 *
 * @param string $nascimento Data de nascimento.
 * @param string $morte      Data de morte.
 * @return string
 */
function mdc_idade( $nascimento, $morte = '' ) {
	if ( ! $nascimento ) {
		return '';
	}

	$inicio = strtotime( $nascimento );
	$fim    = $morte ? strtotime( $morte ) : time();

	if ( ! $inicio || ! $fim || $fim < $inicio ) {
		return '';
	}

	$anos = (int) floor( ( $fim - $inicio ) / YEAR_IN_SECONDS );

	/* translators: %d: idade em anos */
	return sprintf( _n( '%d ano', '%d anos', $anos, 'mundo-da-copa' ), $anos );
}

/**
 * IDs das imagens da galeria do registro.
 *
 * @param int|null $post_id ID do post.
 * @return int[]
 */
function mdc_galeria_ids( $post_id = null ) {
	$valor = mdc_field( 'galeria', $post_id );

	if ( ! $valor || is_array( $valor ) ) {
		return array();
	}

	return array_values( array_filter( array_map( 'absint', explode( ',', $valor ) ) ) );
}

/**
 * Contexto de um arquivo: o que escrever no cabeçalho da listagem.
 *
 * Antes o tema imprimia "HISTÓRIA DA COMPETIÇÃO" fixo em qualquer arquivo —
 * inclusive em "Categoria: Reportagens", onde não fazia sentido nenhum. Aqui
 * cada tipo de listagem devolve a sua própria linha de apoio, o título já sem
 * o prefixo do WordPress e, quando existe, a descrição do termo.
 *
 * @return array 'kicker', 'titulo', 'resumo'.
 */
function mdc_arquivo_contexto() {
	$kicker = __( 'Arquivo', 'mundo-da-copa' );
	$titulo = wp_strip_all_tags( get_the_archive_title() );
	$resumo = wp_strip_all_tags( get_the_archive_description() );

	if ( is_search() ) {
		return array(
			'kicker' => __( 'Busca no portal', 'mundo-da-copa' ),
			/* translators: %s: termo buscado */
			'titulo' => sprintf( __( 'Resultados para “%s”', 'mundo-da-copa' ), get_search_query() ),
			'resumo' => '',
		);
	}

	if ( is_category() ) {
		$kicker = __( 'Editoria', 'mundo-da-copa' );
		$titulo = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$kicker = __( 'Assunto', 'mundo-da-copa' );
		$titulo = single_tag_title( '', false );
	} elseif ( is_author() ) {
		$kicker = __( 'Assinatura', 'mundo-da-copa' );
		$titulo = get_the_author();
	} elseif ( is_date() ) {
		$kicker = __( 'Publicado em', 'mundo-da-copa' );
	} elseif ( is_tax() ) {
		$termo = get_queried_object();

		if ( $termo instanceof WP_Term ) {
			$taxonomia = get_taxonomy( $termo->taxonomy );
			$kicker    = $taxonomia ? $taxonomia->labels->singular_name : __( 'Categoria', 'mundo-da-copa' );
			$titulo    = $termo->name;
			$resumo    = $termo->description ? $termo->description : $resumo;
		}
	} elseif ( is_post_type_archive() ) {
		$tipo = get_query_var( 'post_type' );

		if ( is_array( $tipo ) ) {
			$tipo = reset( $tipo );
		}

		$objeto = $tipo ? get_post_type_object( $tipo ) : null;
		$kicker = __( 'Guias do Mundo da Copa', 'mundo-da-copa' );
		$titulo = $objeto ? $objeto->labels->name : $titulo;
	} elseif ( is_home() ) {
		$kicker = __( 'Publicações', 'mundo-da-copa' );
		$titulo = $titulo ? $titulo : __( 'Últimas do portal', 'mundo-da-copa' );
	}

	return array(
		'kicker' => $kicker,
		'titulo' => $titulo,
		'resumo' => $resumo,
	);
}

/**
 * Quantos itens a consulta principal encontrou, já em texto.
 *
 * @return string
 */
function mdc_arquivo_contagem() {
	global $wp_query;

	$total = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0;

	if ( $total < 1 ) {
		return '';
	}

	return sprintf(
		/* translators: %s: quantidade de publicações */
		_n( '%s publicação', '%s publicações', $total, 'mundo-da-copa' ),
		number_format_i18n( $total )
	);
}

/**
 * Renderiza vídeos editoriais mesmo quando o oEmbed remoto não está disponível.
 */
function mdc_video_embed( $url ) {
	$url = trim( (string) $url );
	if ( ! $url ) return '';

	$embed = wp_oembed_get( esc_url_raw( $url ), array( 'width' => 900 ) );
	if ( $embed ) return $embed;

	$host = wp_parse_url( $url, PHP_URL_HOST );
	$path = wp_parse_url( $url, PHP_URL_PATH );
	$query = wp_parse_url( $url, PHP_URL_QUERY );
	$video_id = '';

	if ( $host && preg_match( '/(?:youtube(?:-nocookie)?\\.com|youtu\\.be)$/i', $host ) ) {
		if ( $host === 'youtu.be' ) {
			$video_id = trim( (string) $path, '/' );
		} elseif ( preg_match( '/(?:^|&)v=([^&]+)/', (string) $query, $m ) ) {
			$video_id = $m[1];
		} elseif ( preg_match( '#/embed/([^/?]+)#', (string) $path, $m ) ) {
			$video_id = $m[1];
		} elseif ( preg_match( '#/shorts/([^/?]+)#', (string) $path, $m ) ) {
			$video_id = $m[1];
		}
		if ( $video_id ) {
			$src = 'https://www.youtube.com/embed/' . rawurlencode( $video_id );
			return '<iframe src="' . esc_url( $src ) . '" title="Vídeo" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';
		}
	}

	$video_extension = strtolower( pathinfo( (string) $path, PATHINFO_EXTENSION ) );
	if ( in_array( $video_extension, array( 'mp4', 'webm', 'ogv', 'mov' ), true ) ) {
		return '<video controls preload="metadata" playsinline><source src="' . esc_url( $url ) . '"></video>';
	}

	if ( $host && preg_match( '/(?:^|\\.)vimeo\\.com$/i', $host ) ) {
		if ( preg_match( '#/(\\d+)(?:$|/)#', (string) $path, $m ) ) {
			$src = 'https://player.vimeo.com/video/' . $m[1];
			return '<iframe src="' . esc_url( $src ) . '" title="Vídeo" loading="lazy" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
		}
	}

	return '';
}

/**
 * Renderiza áudio por oEmbed, arquivo direto ou SoundCloud.
 */
function mdc_audio_embed( $url ) {
	$url = trim( (string) $url );
	if ( ! $url ) return '';

	$embed = wp_oembed_get( esc_url_raw( $url ) );
	if ( $embed ) return $embed;

	$host = wp_parse_url( $url, PHP_URL_HOST );
	$path = wp_parse_url( $url, PHP_URL_PATH );
	$extension = strtolower( pathinfo( (string) $path, PATHINFO_EXTENSION ) );
	if ( in_array( $extension, array( 'mp3', 'm4a', 'ogg', 'wav', 'aac', 'flac' ), true ) ) {
		return wp_audio_shortcode( array( 'src' => esc_url( $url ), 'preload' => 'metadata' ) );
	}

	if ( $host && preg_match( '/(?:^|\\.)soundcloud\\.com$/i', $host ) ) {
		$src = 'https://w.soundcloud.com/player/?url=' . rawurlencode( $url ) . '&color=%23009B4D&auto_play=false&hide_related=true&show_comments=false&show_user=true&show_reposts=false&visual=false';
		return '<iframe src="' . esc_url( $src ) . '" title="Áudio" loading="lazy" allow="autoplay" style="width:100%;height:166px;border:0;"></iframe>';
	}

	return '<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">Ouvir áudio →</a>';
}


/**
 * Classificação editorial das entidades.
 */
function mdc_entidade_nivel_label( $post_id = 0 ) {
    $post_id = $post_id ? (int) $post_id : get_the_ID();
    $nivel = get_post_meta( $post_id, 'mdc_nivel_entidade', true );
    $labels = array(
        'mundial'     => 'Entidade mundial',
        'continental' => 'Confederação continental',
        'nacional'    => 'Entidade nacional',
    );
    return isset( $labels[ $nivel ] ) ? $labels[ $nivel ] : 'Entidade';
}
