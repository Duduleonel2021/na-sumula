<?php
/**
 * Na Súmula — Painel de Controle do Tema
 *
 * IMPORTANTE:
 * As configurações centrais ficam exclusivamente em inc/mdc-config.php.
 * Este arquivo cuida apenas do painel administrativo e da publicidade.
 *
 * @package na-sumula
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================
 * PUBLICIDADE
 * ========================================================= */

/**
 * Formatos suportados pelo painel.
 */
function mdc_ad_formatos() {
	return array(
		'728x90'    => 'Leaderboard — 728 × 90',
		'300x250'   => 'Medium Rectangle — 300 × 250',
		'336x280'   => 'Large Rectangle — 336 × 280',
		'468x60'    => 'Full Banner — 468 × 60',
		'160x600'   => 'Wide Skyscraper — 160 × 600',
		'120x600'   => 'Skyscraper — 120 × 600',
		'200x200'   => 'Small Square — 200 × 200',
		'250x250'   => 'Square — 250 × 250',
		'234x60'    => 'Half Banner — 234 × 60',
		'120x240'   => 'Vertical Banner — 120 × 240',
		'125x125'   => 'Square — 125 × 125',
		'responsive' => 'Responsivo — Google AdSense',
	);
}

/**
 * Locais de publicidade.
 */
function mdc_ad_locais() {
	return array(
		'topo'          => 'Topo / Header',
		'apos-header'   => 'Após o Header',
		'home-destaque' => 'Home — Destaque',
		'home-meio'     => 'Home — Entre blocos',
		'sidebar'       => 'Sidebar',
		'post-inicio'   => 'Post — Após introdução',
		'post-meio'     => 'Post — Meio do conteúdo',
		'post-final'    => 'Post — Final do conteúdo',
		'antes-footer'  => 'Antes do Footer',
		'menu'          => 'Abaixo do menu',
		'mobile'        => 'Mobile',
	);
}

/**
 * Sanitização dos campos do painel.
 */
function mdc_painel_sanitiza( $valor, $campo ) {
	switch ( $campo['tipo'] ) {
		case 'cor':
			$cor = sanitize_hex_color( $valor );
			return $cor ? $cor : '';

		case 'url':
			return esc_url_raw( $valor );

		case 'media':
		case 'arquivo':
			return absint( $valor );

		case 'numero':
			$n = absint( $valor );
			if ( isset( $campo['min'] ) ) {
				$n = max( (int) $campo['min'], $n );
			}
			if ( isset( $campo['max'] ) ) {
				$n = min( (int) $campo['max'], $n );
			}
			return $n;

		case 'bool':
			return ! empty( $valor );

		case 'select':
			return isset( $campo['opcoes'][ $valor ] ) ? sanitize_key( $valor ) : '';

		case 'textarea':
			return current_user_can( 'unfiltered_html' )
				? wp_unslash( $valor )
				: wp_kses_post( wp_unslash( $valor ) );

		default:
			return sanitize_text_field( $valor );
	}
}

/* =========================================================
 * ABAS
 * ========================================================= */

function mdc_painel_abas() {
	return array(
		'visao-geral' => array(
			'titulo'  => 'Visão geral',
			'resumo'  => 'Central de controle do Na Súmula.',
			'campos'  => array(),
		),

		'marca' => array(
			'titulo' => 'Marca',
			'resumo' => 'Controle a identificação visual usada nos modos claro e escuro.',
			'campos' => array(
				'mdc_site_nome' => array(
					'label' => 'Nome do site',
					'tipo'  => 'text',
				),
				'mdc_site_tagline' => array(
					'label' => 'Assinatura',
					'tipo'  => 'text',
				),
				'mdc_logo_claro' => array(
					'label' => 'Logo — modo claro',
					'tipo'  => 'media',
				),
				'mdc_logo_escuro' => array(
					'label' => 'Logo — modo escuro',
					'tipo'  => 'media',
				),
			),
		),

		'cores' => array(
			'titulo' => 'Cores',
			'resumo' => 'Paleta visual e cores de interface do portal.',
			'campos' => array(
				'mdc_cor_primaria'       => array( 'label' => 'Cor primária', 'tipo' => 'cor' ),
				'mdc_cor_destaque'       => array( 'label' => 'Cor de destaque', 'tipo' => 'cor' ),
				'mdc_cor_fundo'          => array( 'label' => 'Fundo claro', 'tipo' => 'cor' ),
				'mdc_cor_fundo_suave'    => array( 'label' => 'Fundo suave', 'tipo' => 'cor' ),
				'mdc_cor_texto'          => array( 'label' => 'Texto claro', 'tipo' => 'cor' ),
				'mdc_cor_texto_suave'    => array( 'label' => 'Texto secundário', 'tipo' => 'cor' ),
				'mdc_cor_fundo_escuro'   => array( 'label' => 'Fundo escuro', 'tipo' => 'cor' ),
				'mdc_cor_texto_escuro'   => array( 'label' => 'Texto no modo escuro', 'tipo' => 'cor' ),
			),
		),

		'tipografia' => array(
			'titulo' => 'Tipografia',
			'resumo' => 'Fontes, pesos e dimensões tipográficas do portal.',
			'campos' => array(
				'mdc_fonte_titulos' => array(
					'label'   => 'Fonte dos títulos',
					'tipo'    => 'select',
					'opcoes'  => array(
						'manrope'    => 'Manrope',
						'inter'      => 'Inter',
						'montserrat' => 'Montserrat',
					),
				),
				'mdc_fonte_textos' => array(
					'label'  => 'Fonte dos textos',
					'tipo'   => 'select',
					'opcoes' => array(
						'inter'      => 'Inter',
						'manrope'    => 'Manrope',
						'montserrat' => 'Montserrat',
					),
				),
				'mdc_peso_titulos' => array(
					'label' => 'Peso dos títulos',
					'tipo'  => 'numero',
					'min'   => 400,
					'max'   => 900,
				),
				'mdc_tamanho_h1' => array(
					'label' => 'H1 — px',
					'tipo'  => 'numero',
					'min'   => 32,
					'max'   => 96,
				),
				'mdc_tamanho_h2' => array(
					'label' => 'H2 — px',
					'tipo'  => 'numero',
					'min'   => 24,
					'max'   => 64,
				),
				'mdc_tamanho_h3' => array(
					'label' => 'H3 — px',
					'tipo'  => 'numero',
					'min'   => 18,
					'max'   => 60,
				),
				'mdc_tamanho_corpo' => array(
					'label' => 'Texto — px',
					'tipo'  => 'numero',
					'min'   => 14,
					'max'   => 24,
				),
			),
		),

		'layout' => array(
			'titulo' => 'Layout',
			'resumo' => 'Dimensões estruturais e comportamento visual do portal.',
			'campos' => array(
				'mdc_largura_site' => array(
					'label' => 'Largura máxima do site — px',
					'tipo'  => 'numero',
					'min'   => 960,
					'max'   => 1600,
				),
				'mdc_largura_artigo' => array(
					'label' => 'Largura do artigo — px',
					'tipo'  => 'numero',
					'min'   => 600,
					'max'   => 1000,
				),
				'mdc_largura_sidebar' => array(
					'label' => 'Largura da sidebar — px',
					'tipo'  => 'numero',
					'min'   => 240,
					'max'   => 420,
				),
				'mdc_modo_padrao' => array(
					'label'   => 'Modo visual padrão',
					'tipo'    => 'select',
					'opcoes'  => array(
						'light'  => 'Claro',
						'dark'   => 'Escuro',
						'system' => 'Automático pelo sistema',
					),
				),
				'mdc_botao_tema' => array(
					'label' => 'Exibir botão claro/escuro',
					'tipo'  => 'bool',
				),
			),
		),

		'home' => array(
			'titulo' => 'Home',
			'ordem'  => 15,
			'resumo' => 'Configure os principais elementos editoriais da página inicial.',
			'campos' => array(
				'mdc_home_hero' => array(
					'label' => 'Imagem do Hero',
					'tipo'  => 'media',
				),
			),
		),

		'newsletter' => array(
			'titulo' => 'Newsletter',
			'resumo' => 'Configure a captação de leitores e o endereço que recebe novas inscrições.',
			'campos' => array(
				'mdc_newsletter_ativo'  => array( 'label' => 'Newsletter ativa', 'tipo' => 'bool' ),
				'mdc_newsletter_titulo' => array( 'label' => 'Título', 'tipo' => 'text' ),
				'mdc_newsletter_texto'  => array( 'label' => 'Texto de apresentação', 'tipo' => 'textarea' ),
				'mdc_newsletter_email'  => array( 'label' => 'E-mail para receber inscrições', 'tipo' => 'text' ),
			),
		),

		'anuncie' => array(
			'titulo' => 'Anuncie aqui',
			'resumo' => 'Configure o contato comercial e o Media Kit disponível para download.',
			'campos' => array(
				'mdc_anuncio_email' => array( 'label' => 'E-mail para solicitações de anúncio', 'tipo' => 'text' ),
				'mdc_media_kit'     => array( 'label' => 'Media Kit — PDF', 'tipo' => 'arquivo' ),
				'mdc_anuncio_periodos' => array( 'label' => 'Períodos de veiculação disponíveis', 'tipo' => 'textarea' ),
			),
		),

		'footer' => array(
			'titulo' => 'Footer',
			'resumo' => 'Conteúdo e organização do rodapé.',
			'campos' => array(
				'mdc_footer_texto' => array(
					'label' => 'Texto de apresentação',
					'tipo'  => 'textarea',
				),
				'mdc_footer_creditos' => array(
					'label' => 'Créditos',
					'tipo'  => 'text',
				),
				'mdc_footer_colunas' => array(
					'label' => 'Número de colunas',
					'tipo'  => 'numero',
					'min'   => 1,
					'max'   => 4,
				),
			),
		),
	);
}

/* =========================================================
 * SALVAMENTO
 * ========================================================= */

function mdc_painel_salva() {
	if ( ! isset( $_POST['mdc_painel_nonce'] ) ) {
		return '';
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['mdc_painel_nonce'] ) );

	if ( ! wp_verify_nonce( $nonce, 'mdc_painel_salvar' ) || ! current_user_can( 'edit_theme_options' ) ) {
		return '';
	}

	$aba  = isset( $_POST['mdc_aba'] ) ? sanitize_key( wp_unslash( $_POST['mdc_aba'] ) ) : '';
	$abas = mdc_painel_abas();

	if ( 'publicidade' === $aba ) {
		return mdc_painel_salva_ads();
	}

	if ( ! isset( $abas[ $aba ] ) ) {
		return '';
	}

	foreach ( $abas[ $aba ]['campos'] as $chave => $campo ) {
		$valor = isset( $_POST[ $chave ] ) ? wp_unslash( $_POST[ $chave ] ) : '';

		if ( 'bool' === $campo['tipo'] && ! isset( $_POST[ $chave ] ) ) {
			$valor = false;
		}

		set_theme_mod(
			$chave,
			mdc_painel_sanitiza( $valor, $campo )
		);
	}

	return 'Configurações salvas.';
}

function mdc_painel_salva_ads() {
	$locais   = mdc_ad_locais();
	$formatos = mdc_ad_formatos();

	$entrada = isset( $_POST['mdc_ads'] ) && is_array( $_POST['mdc_ads'] )
		? wp_unslash( $_POST['mdc_ads'] )
		: array();

	$ads = array();

	foreach ( $locais as $slug => $label ) {
		$item = isset( $entrada[ $slug ] ) && is_array( $entrada[ $slug ] )
			? $entrada[ $slug ]
			: array();

		$tipo = isset( $item['type'] ) ? sanitize_key( $item['type'] ) : 'adsense';
		$tipo = in_array( $tipo, array( 'adsense', 'imagem', 'html' ), true )
			? $tipo
			: 'adsense';

		$formato = isset( $item['format'] ) ? sanitize_key( $item['format'] ) : 'responsive';
		$formato = isset( $formatos[ $formato ] ) ? $formato : 'responsive';

		$ads[ $slug ] = array(
			'enabled' => ! empty( $item['enabled'] ),
			'title'   => isset( $item['title'] ) ? sanitize_text_field( $item['title'] ) : '',
			'type'    => $tipo,
			'format'  => $formato,
			'image'   => isset( $item['image'] ) ? absint( $item['image'] ) : 0,
			'url'     => isset( $item['url'] ) ? esc_url_raw( $item['url'] ) : '',
			'code'    => isset( $item['code'] )
				? ( current_user_can( 'unfiltered_html' )
					? $item['code']
					: wp_kses_post( $item['code'] ) )
				: '',
		);
	}

	set_theme_mod( 'mdc_ads', $ads );
	$solicitacao_email = isset( $_POST['mdc_anuncio_email'] ) ? sanitize_email( wp_unslash( $_POST['mdc_anuncio_email'] ) ) : '';
	set_theme_mod( 'mdc_anuncio_email', $solicitacao_email );

	return 'Publicidade salva.';
}

/* =========================================================
 * PUBLICIDADE — API FRONT-END
 * ========================================================= */

function mdc_get_ad( $local ) {
	$ads = mdc_config( 'mdc_ads' );

	if ( ! is_array( $ads ) || empty( $ads[ $local ] ) || ! is_array( $ads[ $local ] ) ) {
		return array();
	}

	$ad = $ads[ $local ];

	if ( empty( $ad['enabled'] ) ) {
		return array();
	}

	return wp_parse_args(
		$ad,
		array(
			'enabled' => false,
			'title'   => '',
			'type'    => 'adsense',
			'format'  => 'responsive',
			'image'   => 0,
			'url'     => '',
			'code'    => '',
		)
	);
}

/**
 * Verifica se existe uma publicidade ativa em determinado local.
 *
 * Diferente de mdc_ad_code(), esta função também considera banners por imagem,
 * que não possuem código HTML salvo no painel.
 *
 * @param string $local Identificador do espaço publicitário.
 * @return bool
 */
function mdc_ad_enabled( $local ) {
	$ad = mdc_get_ad( $local );

	if ( empty( $ad ) ) {
		return false;
	}

	/* Uma imagem válida sempre representa um banner visual, mesmo se um
	 * registro antigo ainda estiver marcado como AdSense. */
	if ( ! empty( $ad['image'] ) ) {
		return true;
	}

	return ! empty( $ad['code'] );
}

/**
 * Retorna o código bruto de uma publicidade configurada.
 *
 * @param string $local Identificador do espaço publicitário.
 * @return string
 */
function mdc_ad_code( $local ) {
	$ad = mdc_get_ad( $local );

	return ! empty( $ad['code'] ) ? $ad['code'] : '';
}

function mdc_render_ad( $local, $classes = '' ) {
	$ad = mdc_get_ad( $local );

	if ( empty( $ad ) ) {
		return;
	}

	$classe = 'mdc-ad mdc-ad--' . sanitize_html_class( $local );

	if ( $classes ) {
		$classe .= ' ' . $classes;
	}

	echo '<div class="' . esc_attr( $classe ) . '" aria-label="Publicidade">';

	if ( ! empty( $ad['image'] ) ) {
		$img = wp_get_attachment_image(
			(int) $ad['image'],
			'full',
			false,
			array(
				'loading' => 'eager',
				'decoding' => 'async',
				'fetchpriority' => 'high',
			)
		);

		if ( $img ) {
			if ( ! empty( $ad['url'] ) ) {
				echo '<a href="' . esc_url( $ad['url'] ) . '" target="_blank" rel="nofollow sponsored noopener">' . $img . '</a>';
			} else {
				echo $img;
			}
		}
	} elseif ( 'html' === $ad['type'] ) {
		echo current_user_can( 'unfiltered_html' ) ? $ad['code'] : wp_kses_post( $ad['code'] );
	} elseif ( ! empty( $ad['code'] ) ) {
		/*
		 * Snippets AdSense precisam ser entregues intactos ao navegador.
		 * O acesso ao painel já está protegido por edit_theme_options.
		 */
		echo $ad['code'];
	}

	echo '</div>';
}

/* =========================================================
 * MÍDIA
 * ========================================================= */

function mdc_painel_campo_media( $chave, $valor ) {
	$url = $valor ? wp_get_attachment_image_url( (int) $valor, 'medium' ) : '';
	?>
	<div class="mdc-media-control">
		<div class="mdc-media-control__preview">
			<?php if ( $url ) : ?>
				<img src="<?php echo esc_url( $url ); ?>" alt="">
			<?php else : ?>
				<span>Nenhum arquivo selecionado</span>
			<?php endif; ?>
		</div>

		<input
			type="hidden"
			id="<?php echo esc_attr( $chave ); ?>"
			name="<?php echo esc_attr( $chave ); ?>"
			value="<?php echo esc_attr( $valor ); ?>"
		>

		<button type="button" class="button mdc-media-select" data-target="<?php echo esc_attr( $chave ); ?>">
			Selecionar imagem
		</button>

		<button type="button" class="button mdc-media-remove" data-target="<?php echo esc_attr( $chave ); ?>">
			Remover
		</button>
	</div>
	<?php
}

function mdc_painel_campo_arquivo( $chave, $valor ) {
	$url  = $valor ? wp_get_attachment_url( (int) $valor ) : '';
	$file = $valor ? get_attached_file( (int) $valor ) : '';
	$name = $file ? wp_basename( $file ) : '';
	?>
	<div class="mdc-media-control">
		<div class="mdc-media-control__preview">
			<?php if ( $url ) : ?>
				<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $name ? $name : 'Abrir arquivo' ); ?></a>
			<?php else : ?>
				<span>Nenhum arquivo selecionado</span>
			<?php endif; ?>
		</div>
		<input type="hidden" id="<?php echo esc_attr( $chave ); ?>" name="<?php echo esc_attr( $chave ); ?>" value="<?php echo esc_attr( $valor ); ?>">
		<button type="button" class="button mdc-media-select" data-target="<?php echo esc_attr( $chave ); ?>">Selecionar arquivo</button>
		<button type="button" class="button mdc-media-remove" data-target="<?php echo esc_attr( $chave ); ?>">Remover</button>
	</div>
	<?php
}

/* =========================================================
 * CAMPOS
 * ========================================================= */

function mdc_painel_campo( $chave, $campo ) {
	$valor  = mdc_config( $chave );
	$padroes = mdc_config_padrao();
	$padrao = isset( $padroes[ $chave ] ) ? $padroes[ $chave ] : '';
	?>
	<tr>
		<th scope="row">
			<label for="<?php echo esc_attr( $chave ); ?>">
				<?php echo esc_html( $campo['label'] ); ?>
			</label>
		</th>
		<td>
			<?php
			switch ( $campo['tipo'] ) {
				case 'cor':
					printf(
						'<input type="text" class="mdc-campo-cor" id="%1$s" name="%1$s" value="%2$s" data-default-color="%3$s">',
						esc_attr( $chave ),
						esc_attr( $valor ),
						esc_attr( $padrao )
					);
					break;

				case 'media':
					mdc_painel_campo_media( $chave, $valor );
					break;

				case 'arquivo':
					mdc_painel_campo_arquivo( $chave, $valor );
					break;

				case 'bool':
					printf(
						'<label><input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s> Ativado</label>',
						esc_attr( $chave ),
						checked( (bool) $valor, true, false )
					);
					break;

				case 'numero':
					printf(
						'<input type="number" id="%1$s" name="%1$s" value="%2$s" class="small-text" min="%3$s" max="%4$s">',
						esc_attr( $chave ),
						esc_attr( $valor ),
						isset( $campo['min'] ) ? esc_attr( $campo['min'] ) : '',
						isset( $campo['max'] ) ? esc_attr( $campo['max'] ) : ''
					);
					break;

				case 'select':
					echo '<select id="' . esc_attr( $chave ) . '" name="' . esc_attr( $chave ) . '" class="regular-text">';

					foreach ( $campo['opcoes'] as $opcao => $rotulo ) {
						echo '<option value="' . esc_attr( $opcao ) . '" ' . selected( $valor, $opcao, false ) . '>' . esc_html( $rotulo ) . '</option>';
					}

					echo '</select>';
					break;

				case 'textarea':
					printf(
						'<textarea id="%1$s" name="%1$s" rows="5" class="large-text">%2$s</textarea>',
						esc_attr( $chave ),
						esc_textarea( $valor )
					);
					break;

				default:
					printf(
						'<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text">',
						esc_attr( $chave ),
						esc_attr( $valor )
					);
			}
			?>
		</td>
	</tr>
	<?php
}

/* =========================================================
 * TELA DE PUBLICIDADE
 * ========================================================= */

function mdc_painel_render_publicidade() {
	$ads      = mdc_config( 'mdc_ads' );
	$ads      = is_array( $ads ) ? $ads : array();
	$locais   = mdc_ad_locais();
	$formatos = mdc_ad_formatos();
	?>
	<div class="mdc-admin-callout" style="margin:0 0 24px;padding:18px 20px;border:1px solid #ddd;border-radius:12px;background:#fff;">
		<strong style="display:block;margin-bottom:6px;">E-mail para solicitações de anúncio</strong>
		<p style="margin:0 0 10px;color:#666;">As solicitações enviadas pela página “Anuncie aqui” serão encaminhadas para este endereço.</p>
		<input type="email" class="regular-text" name="mdc_anuncio_email" value="<?php echo esc_attr( get_theme_mod( 'mdc_anuncio_email', get_option( 'admin_email' ) ) ); ?>" placeholder="seuemail@exemplo.com" required>
	</div>

	<div class="mdc-ad-grid">
		<?php foreach ( $locais as $slug => $label ) : ?>
			<?php
			$ad = isset( $ads[ $slug ] ) && is_array( $ads[ $slug ] )
				? $ads[ $slug ]
				: array();
			?>
			<section class="mdc-ad-card">
				<header class="mdc-ad-card__head">
					<div>
						<span class="mdc-admin-kicker">PUBLICIDADE</span>
						<h2><?php echo esc_html( $label ); ?></h2>
					</div>

					<label class="mdc-switch">
						<input
							type="checkbox"
							name="mdc_ads[<?php echo esc_attr( $slug ); ?>][enabled]"
							value="1"
							<?php checked( ! empty( $ad['enabled'] ) ); ?>
						>
						<span>Ativo</span>
					</label>
				</header>

				<p class="description">
					Identificador:
					<code><?php echo esc_html( $slug ); ?></code>
				</p>

				<table class="form-table">
					<tr>
						<th><label>Nome interno</label></th>
						<td>
							<input
								type="text"
								class="regular-text"
								name="mdc_ads[<?php echo esc_attr( $slug ); ?>][title]"
								value="<?php echo esc_attr( $ad['title'] ?? '' ); ?>"
							>
						</td>
					</tr>

					<tr>
						<th><label>Tipo</label></th>
						<td>
							<select
								class="regular-text"
								name="mdc_ads[<?php echo esc_attr( $slug ); ?>][type]"
							>
								<option value="adsense" <?php selected( $ad['type'] ?? '', 'adsense' ); ?>>
									Google AdSense
								</option>
								<option value="imagem" <?php selected( $ad['type'] ?? '', 'imagem' ); ?>>
									Imagem / Banner
								</option>
								<option value="html" <?php selected( $ad['type'] ?? '', 'html' ); ?>>
									HTML personalizado
								</option>
							</select>
						</td>
					</tr>

					<tr>
						<th><label>Formato</label></th>
						<td>
							<select
								class="regular-text"
								name="mdc_ads[<?php echo esc_attr( $slug ); ?>][format]"
							>
								<?php foreach ( $formatos as $id => $nome ) : ?>
									<option
										value="<?php echo esc_attr( $id ); ?>"
										<?php selected( $ad['format'] ?? 'responsive', $id ); ?>
									>
										<?php echo esc_html( $nome ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>

					<tr>
						<th><label>Imagem</label></th>
						<td>
							<?php
							$image_id  = ! empty( $ad['image'] ) ? absint( $ad['image'] ) : 0;
							$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';
							?>

							<div class="mdc-media-control">
								<div class="mdc-media-control__preview">
									<?php if ( $image_url ) : ?>
										<img src="<?php echo esc_url( $image_url ); ?>" alt="">
									<?php else : ?>
										<span>Nenhum arquivo selecionado</span>
									<?php endif; ?>
								</div>

								<input
									type="hidden"
									id="mdc_ads_<?php echo esc_attr( $slug ); ?>_image"
									name="mdc_ads[<?php echo esc_attr( $slug ); ?>][image]"
									value="<?php echo esc_attr( $image_id ); ?>"
								>

								<button
									type="button"
									class="button mdc-media-select"
									data-target="mdc_ads_<?php echo esc_attr( $slug ); ?>_image"
								>
									Selecionar imagem
								</button>

								<button
									type="button"
									class="button mdc-media-remove"
									data-target="mdc_ads_<?php echo esc_attr( $slug ); ?>_image"
								>
									Remover
								</button>
							</div>
						</td>
					</tr>

					<tr>
						<th><label>Link</label></th>
						<td>
							<input
								type="url"
								class="regular-text"
								name="mdc_ads[<?php echo esc_attr( $slug ); ?>][url]"
								value="<?php echo esc_attr( $ad['url'] ?? '' ); ?>"
								placeholder="https://"
							>
						</td>
					</tr>

					<tr>
						<th><label>Código</label></th>
						<td>
							<textarea
								class="large-text code"
								rows="7"
								name="mdc_ads[<?php echo esc_attr( $slug ); ?>][code]"
								placeholder="Cole aqui o snippet do AdSense ou HTML do banner."
							><?php echo esc_textarea( $ad['code'] ?? '' ); ?></textarea>
						</td>
					</tr>
				</table>
			</section>
		<?php endforeach; ?>
	</div>
	<?php
}

/* =========================================================
 * LOGO
 * ========================================================= */

function mdc_logo_url( $modo = 'claro' ) {
	$key = 'escuro' === $modo ? 'mdc_logo_escuro' : 'mdc_logo_claro';
	$id  = absint( mdc_config( $key ) );

	if ( $id ) {
		$url = wp_get_attachment_image_url( $id, 'full' );

		if ( $url ) {
			return $url;
		}
	}

	return MDC_THEME_URI . '/assets/images/' . (
		'escuro' === $modo ? 'logo-dark.png' : 'logo-light.png'
	);
}

/* =========================================================
 * MENU ADMINISTRATIVO
 * ========================================================= */

function mdc_painel_menu() {
	add_menu_page(
		'Na Súmula',
		'Na Súmula',
		'edit_theme_options',
		'mdc-painel',
		'mdc_painel_render',
		'dashicons-edit-page',
		59
	);
}
add_action( 'admin_menu', 'mdc_painel_menu', 9 );

/* =========================================================
 * ASSETS ADMIN
 * ========================================================= */

function mdc_painel_assets( $hook ) {
	if ( 'toplevel_page_mdc-painel' !== $hook ) {
		return;
	}

	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_media();
	wp_enqueue_script( 'wp-color-picker' );

	$css = MDC_THEME_DIR . '/assets/css/mdc-painel-admin.css';
	$js  = MDC_THEME_DIR . '/assets/js/mdc-painel-admin.js';

	if ( file_exists( $css ) ) {
		wp_enqueue_style(
			'mdc-painel-admin',
			MDC_THEME_URI . '/assets/css/mdc-painel-admin.css',
			array( 'wp-color-picker' ),
			(string) filemtime( $css )
		);
	}

	if ( file_exists( $js ) ) {
		wp_enqueue_script(
			'mdc-painel-admin',
			MDC_THEME_URI . '/assets/js/mdc-painel-admin.js',
			array( 'jquery', 'wp-color-picker' ),
			(string) filemtime( $js ),
			true
		);
	}
}
add_action( 'admin_enqueue_scripts', 'mdc_painel_assets' );

/* =========================================================
 * RENDERIZAÇÃO
 * ========================================================= */

function mdc_painel_render() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$mensagem = mdc_painel_salva();
	$abas     = mdc_painel_abas();

	$abas['publicidade'] = array(
		'titulo' => 'Publicidade',
		'resumo' => 'Gerencie os espaços publicitários do portal, incluindo banners, HTML e Google AdSense.',
		'campos' => array(),
	);

	$atual = isset( $_GET['aba'] )
		? sanitize_key( wp_unslash( $_GET['aba'] ) )
		: 'visao-geral';

	if ( ! isset( $abas[ $atual ] ) ) {
		$atual = 'visao-geral';
	}

	$versao = defined( 'MDC_THEME_VERSION' ) ? MDC_THEME_VERSION : '—';
	?>
	<div class="wrap mdc-admin">

		<div class="mdc-admin__top">
			<div class="mdc-admin__brand">
				<div class="mdc-admin__mark">ns</div>

				<div>
					<strong>Na Súmula</strong>
					<span>Jornalismo • Memória • Futebol</span>
				</div>
			</div>

			<div class="mdc-admin__actions">
				<span class="mdc-admin__version">
					Tema <?php echo esc_html( $versao ); ?>
				</span>

				<a
					class="button"
					href="<?php echo esc_url( home_url( '/' ) ); ?>"
					target="_blank"
					rel="noopener"
				>
					Ver site
				</a>
			</div>
		</div>

		<div class="mdc-admin__layout">

			<aside class="mdc-admin__sidebar">
				<?php foreach ( $abas as $id => $aba ) : ?>
					<a
						href="<?php echo esc_url( admin_url( 'admin.php?page=mdc-painel&aba=' . $id ) ); ?>"
						class="<?php echo $id === $atual ? 'is-active' : ''; ?>"
					>
						<?php echo esc_html( $aba['titulo'] ); ?>
					</a>
				<?php endforeach; ?>
			</aside>

			<main class="mdc-admin__content">

				<div class="mdc-admin__heading">
					<span>CONFIGURAÇÃO DO PORTAL</span>
					<h1><?php echo esc_html( $abas[ $atual ]['titulo'] ); ?></h1>
					<p><?php echo esc_html( $abas[ $atual ]['resumo'] ); ?></p>
				</div>

				<?php if ( $mensagem ) : ?>
					<div class="notice notice-success is-dismissible">
						<p><?php echo esc_html( $mensagem ); ?></p>
					</div>
				<?php endif; ?>

				<form method="post" action="">
					<?php wp_nonce_field( 'mdc_painel_salvar', 'mdc_painel_nonce' ); ?>

					<input
						type="hidden"
						name="mdc_aba"
						value="<?php echo esc_attr( $atual ); ?>"
					>

					<?php if ( 'visao-geral' === $atual ) : ?>

						<div class="mdc-dashboard-grid">
							<div class="mdc-dashboard-card">
								<strong>Marca</strong>
								<span>Logos claro e escuro</span>
							</div>

							<div class="mdc-dashboard-card">
								<strong>Aparência</strong>
								<span>Cores, fontes e dimensões</span>
							</div>

							<div class="mdc-dashboard-card">
								<strong>Publicidade</strong>
								<span>AdSense, banners e HTML</span>
							</div>

							<div class="mdc-dashboard-card">
								<strong>Footer</strong>
								<span>Texto, créditos e colunas</span>
							</div>
						</div>

					<?php elseif ( 'publicidade' === $atual ) : ?>

						<?php mdc_painel_render_publicidade(); ?>

					<?php else : ?>

						<table class="form-table" role="presentation">
							<tbody>
								<?php foreach ( $abas[ $atual ]['campos'] as $chave => $campo ) : ?>
									<?php mdc_painel_campo( $chave, $campo ); ?>
								<?php endforeach; ?>
							</tbody>
						</table>

					<?php endif; ?>

					<?php if ( 'visao-geral' !== $atual ) : ?>
						<p class="submit">
							<button
								type="submit"
								class="button button-primary button-large"
							>
								Salvar alterações
							</button>
						</p>
					<?php endif; ?>

				</form>
			</main>
		</div>
	</div>
	<?php
}
