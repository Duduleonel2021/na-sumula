<?php
/**
 * Motor editorial: sumário, tempo de leitura, compartilhamento,
 * "Leia mais" no meio do texto e contagem de visualizações.
 *
 * @package mundo-da-copa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tempo estimado de leitura, em minutos.
 *
 * @param int|null $post_id ID do post.
 * @return int
 */
function mdc_tempo_leitura( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$texto   = wp_strip_all_tags( get_post_field( 'post_content', $post_id ) );
	$palavras = count( preg_split( '/\s+/u', trim( $texto ), -1, PREG_SPLIT_NO_EMPTY ) );

	return max( 1, (int) ceil( $palavras / 200 ) );
}

/**
 * Percorre o conteúdo, coloca âncoras nos H2/H3 e devolve o sumário.
 *
 * Roda uma única vez por post e guarda o resultado, para que o template
 * possa imprimir o sumário antes do corpo do texto.
 *
 * @param string $conteudo HTML do conteúdo.
 * @return array {
 *     @type string $html   Conteúdo com os ids aplicados.
 *     @type array  $indice Lista de itens: id, texto, nivel.
 * }
 */
function mdc_processa_sumario( $conteudo ) {
	$indice = array();

	if ( ! $conteudo || ! class_exists( 'DOMDocument' ) ) {
		return array( 'html' => $conteudo, 'indice' => $indice );
	}

	if ( ! preg_match( '/<h[23][\s>]/i', $conteudo ) ) {
		return array( 'html' => $conteudo, 'indice' => $indice );
	}

	$dom = new DOMDocument();
	libxml_use_internal_errors( true );

	// O prefixo com charset evita que o DOMDocument quebre os acentos.
	$dom->loadHTML(
		'<?xml encoding="utf-8" ?><div id="mdc-root">' . $conteudo . '</div>',
		LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
	);

	libxml_clear_errors();

	$usados = array();
	$xpath  = new DOMXPath( $dom );
	$titulos = $xpath->query( '//h2 | //h3' );

	foreach ( $titulos as $titulo ) {
		$texto = trim( $titulo->textContent );

		if ( '' === $texto ) {
			continue;
		}

		$id = $titulo->getAttribute( 'id' );

		if ( ! $id ) {
			$id   = sanitize_title( $texto );
			$id   = $id ? $id : 'secao';
			$base = $id;
			$n    = 2;

			while ( isset( $usados[ $id ] ) ) {
				$id = $base . '-' . $n;
				$n++;
			}

			$titulo->setAttribute( 'id', $id );
		}

		$usados[ $id ] = true;

		$indice[] = array(
			'id'    => $id,
			'texto' => $texto,
			'nivel' => ( 'h3' === strtolower( $titulo->nodeName ) ) ? 3 : 2,
		);
	}

	$raiz = $dom->getElementById( 'mdc-root' );
	$html = '';

	if ( $raiz ) {
		foreach ( $raiz->childNodes as $filho ) {
			$html .= $dom->saveHTML( $filho );
		}
	}

	return array(
		'html'   => $html ? $html : $conteudo,
		'indice' => $indice,
	);
}

/**
 * Guarda o sumário do post sendo renderizado.
 *
 * @param array|null $novo Define o valor.
 * @return array
 */
function mdc_sumario_atual( $novo = null ) {
	static $indice = array();

	if ( null !== $novo ) {
		$indice = $novo;
	}

	return $indice;
}

/**
 * Aplica as âncoras no conteúdo e alimenta o sumário.
 *
 * @param string $conteudo Conteúdo.
 * @return string
 */
function mdc_filtra_conteudo_sumario( $conteudo ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $conteudo;
	}

	$resultado = mdc_processa_sumario( $conteudo );
	mdc_sumario_atual( $resultado['indice'] );

	return $resultado['html'];
}
add_filter( 'the_content', 'mdc_filtra_conteudo_sumario', 8 );

/**
 * Monta o sumário para ser impresso ANTES do corpo do texto.
 *
 * Lê o conteúdo bruto do post em vez de rodar a cadeia de filtros de novo.
 * Os ids são gerados pelo mesmo algoritmo usado no filtro the_content, então
 * os links do índice batem com as âncoras do texto.
 *
 * @param int|null $post_id ID do post.
 * @return array Lista de itens: id, texto, nivel.
 */
function mdc_prepara_sumario( $post_id = null ) {
	$post_id  = $post_id ? (int) $post_id : get_the_ID();
	$conteudo = get_post_field( 'post_content', $post_id );

	$resultado = mdc_processa_sumario( $conteudo );

	return $resultado['indice'];
}

/**
 * Insere o box "Leia mais" depois do enésimo parágrafo.
 *
 * O conteúdo do box vem das reportagens relacionadas do post; se não houver,
 * usa as mais recentes da mesma categoria editorial.
 *
 * @param string $conteudo Conteúdo.
 * @return string
 */
function mdc_render_leia_mais( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();

	if ( ! $post_id || ! mdc_config( 'mdc_post_leiamais' ) ) {
		return '';
	}

	$ids = mdc_leia_mais_ids( 3 );

	if ( ! $ids ) {
		return '';
	}

	ob_start();
	?>
	<section class="mdc-leiamais" data-mdc-leiamais aria-label="<?php esc_attr_e( 'Leia mais', 'mundo-da-copa' ); ?>">
		<div class="mdc-leiamais__cabecalho">
			<p class="mdc-leiamais__rotulo"><?php esc_html_e( 'Leia mais', 'mundo-da-copa' ); ?></p>
			<?php if ( count( $ids ) > 1 ) : ?>
				<div class="mdc-leiamais__controles" aria-label="Navegação do Leia mais">
					<button type="button" class="mdc-leiamais__seta mdc-leiamais__seta--ant" data-mdc-leiamais-ant aria-label="Anterior">‹</button>
					<button type="button" class="mdc-leiamais__seta mdc-leiamais__seta--prox" data-mdc-leiamais-prox aria-label="Próximo">›</button>
				</div>
			<?php endif; ?>
		</div>

		<div class="mdc-leiamais__viewport">
			<div class="mdc-leiamais__trilho" data-mdc-leiamais-trilho>
				<?php foreach ( $ids as $mdc_id ) : ?>
					<a class="mdc-leiamais__card" href="<?php echo esc_url( get_permalink( $mdc_id ) ); ?>">
						<?php if ( has_post_thumbnail( $mdc_id ) ) : ?>
							<?php echo get_the_post_thumbnail( $mdc_id, 'large', array( 'loading' => 'lazy', 'alt' => '' ) ); ?>
						<?php endif; ?>
						<span><?php echo esc_html( mdc_title( $mdc_id ) ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean();
}

/**
 * O bloco "Leia mais" é renderizado pelo template no final da matéria.
 * Não é mais inserido dentro do texto editorial.
 */

/**
 * IDs usados pelo box "Leia mais".
 *
 * @param int $limite Quantidade.
 * @return int[]
 */
function mdc_leia_mais_ids( $limite = 3 ) {
	$ids = mdc_reportagens_relacionadas( 'reportagens_relacionadas', '', $limite );

	if ( count( $ids ) >= $limite ) {
		return array_slice( $ids, 0, $limite );
	}

	$termos = get_the_category();
	$args   = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => $limite,
		'post__not_in'        => array_merge( array( get_the_ID() ), $ids ),
		'fields'              => 'ids',
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
	);

	if ( ! is_wp_error( $termos ) && ! empty( $termos ) ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => wp_list_pluck( $termos, 'term_id' ),
			),
		);
	}

	$query = new WP_Query( $args );

	return array_slice( array_merge( $ids, $query->posts ), 0, $limite );
}

/**
 * Links de compartilhamento.
 *
 * @param int|null $post_id ID do post.
 * @return array
 */
function mdc_links_compartilhamento( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$url     = rawurlencode( get_permalink( $post_id ) );
	$titulo  = rawurlencode( get_the_title( $post_id ) );

	return array(
		'facebook' => array(
			'rotulo' => 'Facebook',
			'url'    => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
		),
		'x' => array(
			'rotulo' => 'X',
			'url'    => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $titulo,
		),
		'whatsapp' => array(
			'rotulo' => 'WhatsApp',
			'url'    => 'https://api.whatsapp.com/send?text=' . $titulo . '%20' . $url,
		),
		'linkedin' => array(
			'rotulo' => 'LinkedIn',
			'url'    => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url,
		),
		'email' => array(
			'rotulo' => 'E-mail',
			'url'    => 'mailto:?subject=' . $titulo . '&body=' . $url,
		),
	);
}

/**
 * Contador simples de visualizações.
 *
 * Alimenta o bloco "Mais lidas". Em sites com cache de página o incremento
 * deixa de acontecer — nesse caso, troque a chamada por uma requisição
 * assíncrona ou use as estatísticas do seu provedor.
 *
 * @param int $post_id ID do post.
 * @return void
 */
function mdc_registra_visualizacao( $post_id ) {
	$post_id = (int) $post_id;

	if ( ! $post_id || ! in_array( get_post_type( $post_id ), array( 'post', 'page' ), true ) ) {
		return 0;
	}

	// Administradores não entram nas métricas editoriais.
	if ( current_user_can( 'manage_options' ) ) {
		return (int) get_post_meta( $post_id, '_mdc_views', true );
	}

	$total = (int) get_post_meta( $post_id, '_mdc_views', true );
	$total++;
	update_post_meta( $post_id, '_mdc_views', $total );

	return $total;
}

/**
 * Registra uma visualização por AJAX.
 *
 * Isso evita que cache de página impeça o contador de funcionar.
 */
function mdc_visualizacao_ajax() {
	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	$nonce   = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

	if (
		! $post_id ||
		! wp_verify_nonce( $nonce, 'mdc_view_' . $post_id ) ||
		! in_array( get_post_type( $post_id ), array( 'post', 'page' ), true ) ||
		'publish' !== get_post_status( $post_id )
	) {
		wp_send_json_error( array( 'message' => 'Visualização inválida.' ), 400 );
	}

	if ( current_user_can( 'manage_options' ) ) {
		wp_send_json_success( array( 'views' => (int) get_post_meta( $post_id, '_mdc_views', true ) ) );
	}

	$views = mdc_registra_visualizacao( $post_id );

	wp_send_json_success( array( 'views' => $views ) );
}
add_action( 'wp_ajax_mdc_registrar_visualizacao', 'mdc_visualizacao_ajax' );
add_action( 'wp_ajax_nopriv_mdc_registrar_visualizacao', 'mdc_visualizacao_ajax' );

/**
 * Posts mais lidos.
 *
 * @param int $limite Quantidade.
 * @param int $dias   Janela em dias. 0 = sempre.
 * @return int[]
 */
function mdc_mais_lidas( $limite = 5, $dias = 0 ) {
	$limite = max( 1, (int) $limite );
	$excluir = is_singular( 'post' ) ? array( get_queried_object_id() ) : array();

	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => $limite,
		'fields'              => 'ids',
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
		'meta_key'            => '_mdc_views',
		'orderby'             => 'meta_value_num',
		'order'               => 'DESC',
		'post__not_in'        => $excluir,
	);

	if ( $dias > 0 ) {
		$args['date_query'] = array(
			array(
				'after'     => $dias . ' days ago',
				'inclusive' => true,
			),
		);
	}

	$query = new WP_Query( $args );

	/*
	 * O retorno desta função é sempre uma lista de IDs.
	 * Isso evita que objetos WP_Post, arrays ou outros valores cheguem
	 * aos templates e provoquem warnings no bloco "Mais lidas".
	 */
	$ids = array();

	foreach ( (array) $query->posts as $item ) {
		$id = absint( $item );
		if ( $id > 0 && 'post' === get_post_type( $id ) ) {
			$ids[] = $id;
		}
	}

	/*
	 * Site novo, sem visualizações ainda: cai para os posts mais recentes.
	 */
	if ( empty( $ids ) ) {
		$fallback = new WP_Query(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'posts_per_page'      => $limite,
				'fields'              => 'ids',
				'no_found_rows'       => true,
				'ignore_sticky_posts' => true,
				'post__not_in'        => $excluir,
				'orderby'             => 'date',
				'order'               => 'DESC',
			)
		);

		foreach ( (array) $fallback->posts as $item ) {
			$id = absint( $item );
			if ( $id > 0 && 'post' === get_post_type( $id ) ) {
				$ids[] = $id;
			}
		}
	}

	return array_values( array_unique( $ids ) );
}

/**
 * Redes sociais no perfil do autor.
 *
 * @param array $campos Campos de contato.
 * @return array
 */
function mdc_campos_autor( $campos ) {
	$campos['mdc_x']         = 'X (Twitter)';
	$campos['mdc_instagram'] = 'Instagram';
	$campos['mdc_linkedin']  = 'LinkedIn';

	return $campos;
}
add_filter( 'user_contactmethods', 'mdc_campos_autor' );
