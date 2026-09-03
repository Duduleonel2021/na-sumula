<?php
/**
 * Na Súmula — Página Anuncie aqui
 * Template público autossuficiente para a área comercial.
 *
 * @package na-sumula
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================
 * COMPATIBILIDADE DO MÓDULO COMERCIAL
 * ========================================================= */

if ( ! function_exists( 'ns_anuncie_duracoes_padrao' ) ) {
	function ns_anuncie_duracoes_padrao() {
		return array(
			7   => '7 dias',
			15  => '15 dias',
			30  => '30 dias',
			60  => '60 dias',
			90  => '90 dias',
		);
	}
}

if ( ! function_exists( 'ns_anuncie_config' ) ) {
	function ns_anuncie_config( $chave ) {
		$padroes = array(
			'email'     => get_option( 'admin_email' ),
			'media_kit' => 0,
			'intro'     => 'Apresente sua marca a um público interessado em futebol, história e informação. Escolha o espaço, envie seu material e nossa equipe retornará para alinhar disponibilidade, período e condições comerciais.',
			'duracoes'  => array_keys( ns_anuncie_duracoes_padrao() ),
		);

		$valor = get_theme_mod( 'ns_anuncie_' . $chave, $padroes[ $chave ] );

		if ( 'duracoes' === $chave ) {
			$valor = is_array( $valor ) ? $valor : $padroes['duracoes'];
			$valor = array_values( array_filter( array_map( 'absint', $valor ) ) );
			return $valor ? $valor : $padroes['duracoes'];
		}

		return $valor;
	}
}

if ( ! function_exists( 'ns_anuncie_locais' ) ) {
	function ns_anuncie_locais() {
		if ( function_exists( 'mdc_ad_locais' ) ) {
			return mdc_ad_locais();
		}

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
}

if ( ! function_exists( 'mdc_anuncio_page_url' ) ) {
	function mdc_anuncio_page_url() {
		$page_id = absint( get_theme_mod( 'ns_anuncie_page_id', 0 ) );

		if ( $page_id ) {
			$url = get_permalink( $page_id );
			if ( $url ) {
				return $url;
			}
		}

		return home_url( '/anuncie-aqui/' );
	}
}

/* =========================================================
 * MEDIA KIT — LEITURA ROBUSTA DA CONFIGURAÇÃO
 * ========================================================= */

if ( ! function_exists( 'ns_anuncie_media_kit_url' ) ) {
	function ns_anuncie_media_kit_url() {
		/*
		 * O painel atual grava o ID do anexo em ns_anuncie_media_kit.
		 * Algumas versões anteriores do tema utilizavam mdc_media_kit.
		 * Aceitamos os dois formatos para evitar que o Media Kit desapareça
		 * quando o template é atualizado.
		 */
		$valor = get_theme_mod( 'ns_anuncie_media_kit', 0 );

		if ( ! $valor && function_exists( 'mdc_config' ) ) {
			$valor = mdc_config( 'mdc_media_kit' );
		}

		if ( ! $valor ) {
			$mods = get_option( 'theme_mods_' . get_option( 'stylesheet' ), array() );

			if ( is_array( $mods ) ) {
				if ( ! empty( $mods['ns_anuncie_media_kit'] ) ) {
					$valor = $mods['ns_anuncie_media_kit'];
				} elseif ( ! empty( $mods['mdc_media_kit'] ) ) {
					$valor = $mods['mdc_media_kit'];
				}
			}
		}

		if ( is_numeric( $valor ) ) {
			$attachment_id = absint( $valor );

			if ( $attachment_id && get_post( $attachment_id ) ) {
				$url = wp_get_attachment_url( $attachment_id );

				if ( $url ) {
					return esc_url_raw( $url );
				}
			}
		}

		/*
		 * Fallback para instalações que eventualmente tenham guardado
		 * a URL do PDF em vez do ID do anexo.
		 */
		if ( is_string( $valor ) && filter_var( $valor, FILTER_VALIDATE_URL ) ) {
			return esc_url_raw( $valor );
		}

		return '';
	}
}

/* =========================================================
 * PROCESSAMENTO DIRETO DO FORMULÁRIO
 * =========================================================
 */

if ( ! function_exists( 'ns_anuncie_processar_formulario_direto' ) ) {
	function ns_anuncie_processar_formulario_direto() {
		if ( empty( $_POST['ns_anuncie_submit'] ) ) {
			return array();
		}

		if (
			empty( $_POST['ns_anuncie_nonce'] )
			|| ! wp_verify_nonce(
				sanitize_text_field( wp_unslash( $_POST['ns_anuncie_nonce'] ) ),
				'ns_anuncie_solicitacao'
			)
		) {
			return array( 'tipo' => 'erro', 'mensagem' => 'Não foi possível validar o formulário. Recarregue a página e tente novamente.' );
		}

		$empresa  = isset( $_POST['empresa'] ) ? sanitize_text_field( wp_unslash( $_POST['empresa'] ) ) : '';
		$contato  = isset( $_POST['contato'] ) ? sanitize_text_field( wp_unslash( $_POST['contato'] ) ) : '';
		$email    = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$telefone = isset( $_POST['telefone'] ) ? sanitize_text_field( wp_unslash( $_POST['telefone'] ) ) : '';
		$site     = isset( $_POST['site'] ) ? esc_url_raw( wp_unslash( $_POST['site'] ) ) : '';
		$local    = isset( $_POST['local'] ) ? sanitize_key( wp_unslash( $_POST['local'] ) ) : '';
		$duracao  = isset( $_POST['duracao'] ) ? absint( $_POST['duracao'] ) : 0;
		$inicio   = isset( $_POST['inicio'] ) ? sanitize_text_field( wp_unslash( $_POST['inicio'] ) ) : '';
		$link     = isset( $_POST['link'] ) ? esc_url_raw( wp_unslash( $_POST['link'] ) ) : '';
		$mensagem = isset( $_POST['mensagem'] ) ? sanitize_textarea_field( wp_unslash( $_POST['mensagem'] ) ) : '';
		$consent  = ! empty( $_POST['consentimento'] );

		$locais   = ns_anuncie_locais();
		$duracoes = array_map( 'absint', ns_anuncie_config( 'duracoes' ) );
		$erros    = array();

		if ( ! $empresa ) {
			$erros[] = 'Informe a empresa ou marca.';
		}
		if ( ! $contato ) {
			$erros[] = 'Informe o nome do responsável.';
		}
		if ( ! is_email( $email ) ) {
			$erros[] = 'Informe um e-mail válido.';
		}
		if ( ! isset( $locais[ $local ] ) ) {
			$erros[] = 'Escolha o local de veiculação.';
		}
		if ( ! in_array( $duracao, $duracoes, true ) ) {
			$erros[] = 'Escolha um prazo de veiculação válido.';
		}
		if ( ! $consent ) {
			$erros[] = 'É necessário autorizar o uso dos dados para contato comercial.';
		}

		if ( $erros ) {
			return array( 'tipo' => 'erro', 'mensagem' => implode( ' ', $erros ) );
		}

		$anexo_path = '';
		$anexo_nome = '';

		if ( ! empty( $_FILES['banner']['name'] ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			require_once ABSPATH . 'wp-admin/includes/image.php';

			$upload = wp_handle_upload(
				$_FILES['banner'],
				array(
					'test_form' => false,
					'mimes'     => array(
						'jpg|jpeg|jpe' => 'image/jpeg',
						'png'          => 'image/png',
						'webp'         => 'image/webp',
						'gif'          => 'image/gif',
					),
				)
			);

			if ( ! empty( $upload['error'] ) ) {
				return array( 'tipo' => 'erro', 'mensagem' => 'O banner não pôde ser enviado: ' . $upload['error'] );
			}

			$anexo_path = $upload['file'];
			$anexo_nome = basename( $upload['file'] );

			$attachment_id = wp_insert_attachment(
				array(
					'post_mime_type' => $upload['type'],
					'post_title'     => sanitize_file_name( pathinfo( $anexo_nome, PATHINFO_FILENAME ) ),
					'post_status'    => 'inherit',
				),
				$upload['file']
			);

			if ( ! is_wp_error( $attachment_id ) ) {
				$metadata = wp_generate_attachment_metadata( $attachment_id, $upload['file'] );
				if ( $metadata ) {
					wp_update_attachment_metadata( $attachment_id, $metadata );
				}
			}
		}

		$destinatario = sanitize_email( ns_anuncie_config( 'email' ) );
		if ( ! is_email( $destinatario ) ) {
			$destinatario = get_option( 'admin_email' );
		}

		$assunto = sprintf( 'Nova solicitação de publicidade — %s', $empresa );
		$corpo   = "Nova solicitação de publicidade no Na Súmula\n\n";
		$corpo  .= "Empresa/marca: {$empresa}\n";
		$corpo  .= "Responsável: {$contato}\n";
		$corpo  .= "E-mail: {$email}\n";
		$corpo  .= "Telefone: {$telefone}\n";
		$corpo  .= "Site: {$site}\n";
		$corpo  .= "Local de veiculação: " . $locais[ $local ] . "\n";
		$corpo  .= "Tempo solicitado: {$duracao} dias\n";
		$corpo  .= "Início pretendido: {$inicio}\n";
		$corpo  .= "Link de destino do banner: {$link}\n\n";
		$corpo  .= "Mensagem:\n{$mensagem}\n\n";
		$corpo  .= "Banner enviado: " . ( $anexo_path ? $anexo_nome : 'Não enviado' ) . "\n";

		$headers = array(
			'Content-Type: text/plain; charset=UTF-8',
			'Reply-To: ' . $contato . ' <' . $email . '>',
		);

		$enviado = wp_mail( $destinatario, $assunto, $corpo, $headers, $anexo_path ? array( $anexo_path ) : array() );

		if ( ! $enviado ) {
			return array( 'tipo' => 'erro', 'mensagem' => 'Não foi possível enviar a solicitação agora. Verifique a configuração de e-mail do WordPress.' );
		}

		wp_mail(
			$email,
			'Recebemos sua solicitação — Na Súmula',
			"Recebemos sua solicitação de publicidade no Na Súmula.\n\nLocal solicitado: " . $locais[ $local ] . "\nPeríodo solicitado: {$duracao} dias\n\nNossa equipe entrará em contato para confirmar disponibilidade e condições comerciais.",
			array( 'Content-Type: text/plain; charset=UTF-8' )
		);

		return array( 'tipo' => 'sucesso', 'mensagem' => 'Solicitação enviada com sucesso. Nossa equipe entrará em contato em breve.' );
	}
}

/* Processa antes da saída do header, sem depender de template_redirect. */
$ns_anuncie_resultado = ns_anuncie_processar_formulario_direto();

/* O tema guarda os CSS complementares em /assets/css/ (veja
 * O estilo desta página é carregado pelo functions.php.
 * apontar para a raiz do tema gerava 404 e a folha nunca era aplicada. */
$ns_anuncie_css_rel = 'assets/css/anuncie-aqui.css';

wp_enqueue_style(
	'na-sumula-anuncie-aqui',
	get_theme_file_uri( $ns_anuncie_css_rel ),
	array(),
	defined( 'MDC_THEME_VERSION' ) ? MDC_THEME_VERSION : '1.0.0'
);

/* Fallback: registra o CSS inline antes do wp_head(), caso o tema não imprima a folha externa como esperado. */
if ( function_exists( 'wp_add_inline_style' ) ) {
	$ns_anuncie_css_file = get_theme_file_path( $ns_anuncie_css_rel );
	if ( is_readable( $ns_anuncie_css_file ) ) {
		wp_add_inline_style( 'na-sumula-anuncie-aqui', file_get_contents( $ns_anuncie_css_file ) );
	}
}

get_header();

$locais       = ns_anuncie_locais();
$duracoes     = ns_anuncie_duracoes_padrao();
$permitidas   = array_map( 'absint', ns_anuncie_config( 'duracoes' ) );
$media_kit_url = ns_anuncie_media_kit_url();
$intro         = ns_anuncie_config( 'intro' );
?>

<main id="mdc-conteudo" class="ns-anuncie-page">
	<section class="ns-anuncie-hero">
		<div class="mdc-container">
			<span class="ns-anuncie-kicker">NA SÚMULA</span>
			<h1>Anuncie aqui</h1>
			<p>Coloque sua marca no caminho de quem acompanha futebol, história e grandes momentos.</p>
		</div>
	</section>

	<section class="ns-anuncie-intro">
		<div class="mdc-container ns-anuncie-intro__grid">
			<div>
				<span class="ns-anuncie-kicker">PUBLICIDADE</span>
				<h2>Uma audiência interessada em futebol e informação.</h2>
				<p><?php echo esc_html( $intro ); ?></p>
			</div>

			<div class="ns-anuncie-kit">
				<span class="ns-anuncie-kicker">MEDIA KIT</span>
				<h2>Conheça nossos formatos.</h2>
				<p>Consulte dimensões, posições e possibilidades de presença da sua marca no portal.</p>
				<?php if ( $media_kit_url ) : ?>
					<a
						class="ns-anuncie-button ns-anuncie-button--outline"
						href="<?php echo esc_url( $media_kit_url ); ?>"
						target="_blank"
						rel="noopener"
					>Baixar Media Kit <span aria-hidden="true">↗</span></a>
				<?php else : ?>
					<span class="ns-anuncie-kit__pending">O Media Kit está temporariamente indisponível.</span>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="ns-anuncie-form-section">
		<div class="mdc-container">
			<div class="ns-anuncie-section-heading">
				<span class="ns-anuncie-kicker">SOLICITAÇÃO</span>
				<h2>Conte como você quer aparecer no Na Súmula.</h2>
				<p>Preencha os dados abaixo. O envio não representa reserva automática de espaço.</p>
			</div>

			<?php if ( ! empty( $ns_anuncie_resultado['mensagem'] ) ) : ?>
				<div class="ns-anuncie-alert ns-anuncie-alert--<?php echo esc_attr( $ns_anuncie_resultado['tipo'] ); ?>">
					<?php echo esc_html( $ns_anuncie_resultado['mensagem'] ); ?>
				</div>
			<?php endif; ?>

			<form class="ns-anuncie-form" method="post" enctype="multipart/form-data">
				<?php wp_nonce_field( 'ns_anuncie_solicitacao', 'ns_anuncie_nonce' ); ?>

				<div class="ns-anuncie-form__group">
					<h3>Dados de contato</h3>
					<div class="ns-anuncie-form__grid ns-anuncie-form__grid--2">
						<label><span>Empresa ou marca *</span><input type="text" name="empresa" required></label>
						<label><span>Responsável *</span><input type="text" name="contato" required></label>
						<label><span>E-mail *</span><input type="email" name="email" required></label>
						<label><span>Telefone</span><input type="tel" name="telefone"></label>
						<label><span>Site</span><input type="url" name="site" placeholder="https://"></label>
					</div>
				</div>

				<div class="ns-anuncie-form__group">
					<h3>Campanha</h3>
					<div class="ns-anuncie-form__grid ns-anuncie-form__grid--2">
						<label>
							<span>Local de veiculação *</span>
							<select name="local" required>
								<option value="">Selecione um espaço</option>
								<?php foreach ( $locais as $slug => $label ) : ?>
									<option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></option>
								<?php endforeach; ?>
							</select>
							</label>
						<label>
							<span>Tempo de veiculação *</span>
							<select name="duracao" required>
								<option value="">Selecione o período</option>
								<?php foreach ( $duracoes as $dias => $rotulo ) : ?>
									<?php if ( in_array( (int) $dias, $permitidas, true ) ) : ?>
										<option value="<?php echo esc_attr( $dias ); ?>"><?php echo esc_html( $rotulo ); ?></option>
									<?php endif; ?>
								<?php endforeach; ?>
							</select>
						</label>
						<label><span>Início pretendido</span><input type="date" name="inicio" min="<?php echo esc_attr( wp_date( 'Y-m-d' ) ); ?>"></label>
						<label><span>Link de destino do banner</span><input type="url" name="link" placeholder="https://"></label>
					</div>
				</div>

				<div class="ns-anuncie-form__group">
					<h3>Material</h3>
					<label class="ns-anuncie-upload">
						<span>Banner da campanha</span>
						<input type="file" name="banner" accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif">
						<small>JPG, PNG, WEBP ou GIF. Envie o material no formato adequado ao espaço escolhido.</small>
					</label>
					<label><span>Mensagem</span><textarea name="mensagem" rows="6" placeholder="Conte um pouco sobre a campanha, período ou necessidade específica."></textarea></label>
				</div>

				<label class="ns-anuncie-consent">
					<input type="checkbox" name="consentimento" value="1" required>
					<span>Autorizo o Na Súmula a utilizar os dados enviados exclusivamente para tratar desta solicitação comercial.</span>
				</label>

				<div class="ns-anuncie-form__actions">
					<button type="submit" name="ns_anuncie_submit" value="1" class="ns-anuncie-button">Enviar solicitação <span aria-hidden="true">→</span></button>
				</div>
			</form>
		</div>
	</section>
</main>

<?php get_footer(); ?>
