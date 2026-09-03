<?php
/**
 * Missing Functions - Funções auxiliares não encontradas
 *
 * Este arquivo contém funções que estavam sendo chamadas mas não existiam
 * no tema. Elas foram extraídas ou criadas durante a refatoração.
 *
 * @package na-sumula
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Retorna os IDs dos posts mais lidos.
 *
 * @param int $limit Quantidade máxima.
 * @param int $days Número de dias para consultar (0 = sem limite de data).
 * @return int[]
 */
if ( ! function_exists( 'mdc_mais_lidas' ) ) {
	function mdc_mais_lidas( $limit = 5, $days = 0 ) {
		$limit = max( 1, absint( $limit ) );
		$days = absint( $days );

		$args = array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => $limit,
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
			'meta_key' => '_post_views_count',
			'no_found_rows' => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		if ( $days > 0 ) {
			$args['date_query'] = array(
				array(
					'after' => gmdate( 'Y-m-d', strtotime( '-' . $days . ' days' ) ),
					'inclusive' => true,
				),
			);
		}

		$query = new WP_Query( $args );

		return $query->posts ? wp_list_pluck( $query->posts, 'ID' ) : array();
	}
}

/**
 * Obtém o ID do colunista associado a um post.
 *
 * @param int $post_id ID do post.
 * @return int
 */
if ( ! function_exists( 'mdc_colunista_do_post' ) ) {
	function mdc_colunista_do_post( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : get_the_ID();

		if ( ! $post_id ) {
			return 0;
		}

		return absint( get_post_meta( $post_id, 'mdc_colunista', true ) );
	}
}

/**
 * Obtém dados formatados de um colunista.
 *
 * @param int $colunista_id ID do colunista (CPT).
 * @return array
 */
if ( ! function_exists( 'mdc_dados_colunista' ) ) {
	function mdc_dados_colunista( $colunista_id = 0 ) {
		$colunista_id = $colunista_id ? (int) $colunista_id : get_the_ID();

		if ( ! $colunista_id || 'colunista' !== get_post_type( $colunista_id ) ) {
			return array();
		}

		$nome = get_the_title( $colunista_id );
		$coluna = mdc_field( 'colunista_coluna', $colunista_id );
		$url = get_permalink( $colunista_id );

		return array(
			'nome' => $nome,
			'coluna' => $coluna,
			'url' => $url,
			'id' => $colunista_id,
		);
	}
}

/**
 * Prepara o sumário (índice) de um artigo a partir dos heading tags.
 *
 * @param int $post_id ID do post.
 * @return array
 */
if ( ! function_exists( 'mdc_prepara_sumario' ) ) {
	function mdc_prepara_sumario( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : get_the_ID();

		if ( ! $post_id ) {
			return array();
		}

		$content = get_post_field( 'post_content', $post_id );

		if ( ! $content ) {
			return array();
		}

		$sumario = array();
		$matches = array();

		// Captura h2 e h3
		if ( preg_match_all( '/<h[23](?:\s[^>]*)?>(.+?)<\/h[23]>/i', $content, $matches ) ) {
			foreach ( $matches[1] as $index => $titulo ) {
				$titulo_limpo = wp_strip_all_tags( $titulo );
				$nivel = preg_match( '/<h2/', $matches[0][ $index ] ) ? 2 : 3;
				$id = 'secao-' . $index . '-' . sanitize_title( $titulo_limpo );

				$sumario[] = array(
					'texto' => $titulo_limpo,
					'nivel' => $nivel,
					'id' => $id,
				);
			}
		}

		return $sumario;
	}
}

/**
 * Renderiza a seção de atualizações ao vivo de um post.
 *
 * @param int $post_id ID do post.
 * @return string
 */
if ( ! function_exists( 'mdc_render_atualizacao' ) ) {
	function mdc_render_atualizacao( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : get_the_ID();

		if ( ! $post_id ) {
			return '';
		}

		// Implementação básica - pode ser expandida conforme necessário
		$atualizacoes = get_post_meta( $post_id, 'mdc_atualizacoes', false );

		if ( empty( $atualizacoes ) ) {
			return '';
		}

		ob_start();
		?>
		<section class="mdc-atualizacoes" aria-label="Atualizações ao vivo">
			<div class="mdc-section-heading mdc-section-heading--compact">
				<div>
					<span class="mdc-section-kicker">ATUALIZAÇÕES</span>
					<h2>Acompanhamento ao vivo</h2>
				</div>
			</div>
			<div class="mdc-atualizacoes__list">
				<?php foreach ( array_reverse( $atualizacoes ) as $index => $atualiz ) : ?>
					<div class="mdc-atualizacoes__item">
						<?php if ( isset( $atualiz['hora'] ) ) : ?>
							<time class="mdc-atualizacoes__hora"><?php echo esc_html( $atualiz['hora'] ); ?></time>
						<?php endif; ?>
						<?php if ( isset( $atualiz['conteudo'] ) ) : ?>
							<div class="mdc-atualizacoes__texto"><?php echo wp_kses_post( $atualiz['conteudo'] ); ?></div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
}

/**
 * Verifica se um post tem atualizações ao vivo ativas.
 *
 * @param int $post_id ID do post.
 * @return bool
 */
if ( ! function_exists( 'mdc_atualizacao_ativa' ) ) {
	function mdc_atualizacao_ativa( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : get_the_ID();

		if ( ! $post_id ) {
			return false;
		}

		$valor = get_post_meta( $post_id, 'mdc_em_atualizacao', true );

		return in_array( $valor, array( '1', 'true', 'yes', 'sim', 'on', 'ativo', 'active' ), true );
	}
}

/**
 * Renderiza publicidade.
 *
 * @param string $local Identificador do local do anúncio.
 * @param string $classe_extra Classes CSS extras (opcional).
 * @return void
 */
if ( ! function_exists( 'mdc_render_ad' ) ) {
	function mdc_render_ad( $local = '', $classe_extra = '' ) {
		if ( ! function_exists( 'mdc_config' ) ) {
			return;
		}

		if ( ! mdc_config( 'mdc_ads' ) ) {
			return;
		}

		// Implementação básica - pode ser expandida com plugin de ads
		// echo '<!-- Anúncio: ' . esc_html( $local ) . ' -->';
	}
}

/**
 * Renderiza seção de "Leia Mais" com posts relacionados.
 *
 * @param int $post_id ID do post.
 * @return string
 */
if ( ! function_exists( 'mdc_render_leia_mais' ) ) {
	function mdc_render_leia_mais( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : get_the_ID();

		if ( ! $post_id || ! function_exists( 'mdc_posts_relacionados' ) ) {
			return '';
		}

		$related_ids = mdc_posts_relacionados( $post_id, 3 );

		if ( empty( $related_ids ) ) {
			return '';
		}

		ob_start();
		?>
		<section class="mdc-leia-mais">
			<div class="mdc-section-heading mdc-section-heading--compact">
				<div>
					<span class="mdc-section-kicker">LEITURA RECOMENDADA</span>
					<h2>Continue lendo</h2>
				</div>
			</div>
			<div class="mdc-post-grid">
				<?php foreach ( $related_ids as $related_id ) : ?>
					<?php get_template_part( 'template-parts/card-post', null, array( 'id' => $related_id ) ); ?>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}
}

/**
 * Gera links de compartilhamento para um post.
 *
 * @param int $post_id ID do post.
 * @return array
 */
if ( ! function_exists( 'mdc_links_compartilhamento' ) ) {
	function mdc_links_compartilhamento( $post_id = 0 ) {
		$post_id = $post_id ? (int) $post_id : get_the_ID();

		if ( ! $post_id ) {
			return array();
		}

		$url = get_permalink( $post_id );
		$titulo = get_the_title( $post_id );
		$url_encoded = rawurlencode( $url );
		$titulo_encoded = rawurlencode( $titulo );

		return array(
			'facebook' => array(
				'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . $url_encoded,
				'rotulo' => __( 'Compartilhar no Facebook', 'mundo-da-copa' ),
			),
			'x' => array(
				'url' => 'https://x.com/intent/tweet?url=' . $url_encoded . '&text=' . $titulo_encoded,
				'rotulo' => __( 'Compartilhar no X (Twitter)', 'mundo-da-copa' ),
			),
			'whatsapp' => array(
				'url' => 'https://wa.me/?text=' . $titulo_encoded . '%20' . $url_encoded,
				'rotulo' => __( 'Compartilhar no WhatsApp', 'mundo-da-copa' ),
			),
			'linkedin' => array(
				'url' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . $url_encoded,
				'rotulo' => __( 'Compartilhar no LinkedIn', 'mundo-da-copa' ),
			),
			'email' => array(
				'url' => 'mailto:?subject=' . $titulo_encoded . '&body=' . $url_encoded,
				'rotulo' => __( 'Compartilhar por E-mail', 'mundo-da-copa' ),
			),
		);
	}
}

/**
 * Obtém a URL da página de anúncios.
 *
 * @return string
 */
if ( ! function_exists( 'mdc_anuncio_page_url' ) ) {
	function mdc_anuncio_page_url() {
		if ( ! function_exists( 'mdc_config' ) ) {
			return home_url( '/anuncie/' );
		}

		$page_id = absint( mdc_config( 'ns_anuncie_page_id' ) );

		if ( $page_id ) {
			return get_permalink( $page_id );
		}

		return home_url( '/anuncie/' );
	}
}

/**
 * Renderiza o menu principal com suporte a personalização.
 *
 * @return void
 */
if ( ! function_exists( 'mdc_header_menu_principal' ) ) {
	function mdc_header_menu_principal() {
		if ( has_nav_menu( 'principal' ) ) {
			wp_nav_menu(
				array(
					'theme_location' => 'principal',
					'container' => false,
					'menu_class' => 'mdc-nav__list',
					'depth' => 2,
					'fallback_cb' => false,
				)
			);
		}
	}
}

/**
 * Obtém redes sociais configuradas.
 *
 * @param int $post_id ID do post ou entidade.
 * @return array
 */
if ( ! function_exists( 'mdc_redes_sociais' ) ) {
	function mdc_redes_sociais( $post_id = 0 ) {
		if ( ! function_exists( 'mdc_redes_registro' ) ) {
			return array();
		}

		if ( ! $post_id ) {
			// Redes sociais globais do tema
			$redes = mdc_redes_registro( 0 );
		} else {
			// Redes sociais do registro específico
			$redes = mdc_redes_registro( $post_id );
		}

		$saida = array();

		foreach ( (array) $redes as $rede ) {
			if ( isset( $rede['slug'] ) && isset( $rede['url'] ) ) {
				$saida[ $rede['slug'] ] = array(
					'url' => $rede['url'],
					'rotulo' => isset( $rede['rotulo'] ) ? $rede['rotulo'] : $rede['slug'],
				);
			}
		}

		return $saida;
	}
}
