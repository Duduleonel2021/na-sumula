<?php
/**
 * Mundo da Copa — Sistema universal de fichas.
 *
 * @package mundo-da-copa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mdc_modelos_ficha() {
	return array(
		'copa'         => 'Copa',
		'selecao'      => 'Seleção',
		'jogador'      => 'Jogador',
		'estadio'      => 'Estádio',
		'entidade'     => 'Entidade',
	);
}

function mdc_modelo_ficha_padrao( $post_id = 0 ) {
	$post_id   = $post_id ? (int) $post_id : get_the_ID();
	$post_type = get_post_type( $post_id );
	$modelos   = mdc_modelos_ficha();

	return isset( $modelos[ $post_type ] ) ? $post_type : '';
}

function mdc_modelo_ficha( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$padrao  = mdc_modelo_ficha_padrao( $post_id );
	$manual  = get_post_meta( $post_id, '_mdc_modelo_ficha', true );
	$modelos = mdc_modelos_ficha();

	if ( 'automatico' === $manual || ! isset( $modelos[ $manual ] ) ) {
		return $padrao;
	}

	/*
	 * O modelo manual controla a apresentação.
	 * Os campos continuam pertencendo ao CPT de origem.
	 */
	return $manual;
}

function mdc_modelo_ficha_label( $modelo ) {
	$modelos = mdc_modelos_ficha();
	return isset( $modelos[ $modelo ] ) ? $modelos[ $modelo ] : '';
}

function mdc_ficha_field( $name, $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	return mdc_field( $name, $post_id );
}

function mdc_ficha_image_id( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	/*
	 * Regra editorial: a imagem destacada é sempre a imagem do Hero.
	 * Campos como "capa" continuam disponíveis para a imagem de identidade
	 * (por exemplo, o pôster de uma Copa).
	 */
	return has_post_thumbnail( $post_id ) ? get_post_thumbnail_id( $post_id ) : 0;
}

function mdc_ficha_identity_image_id( $post_id = 0 ) {
	$post_id   = $post_id ? (int) $post_id : get_the_ID();
	$post_type = get_post_type( $post_id );

	$campos = array(
		'copa'         => array( 'logo', 'capa' ),
		'selecao'      => array( 'logo' ),
		'jogador'      => array(),
		'estadio'      => array(),
		'entidade'     => array( 'logo' ),
	);

	foreach ( $campos[ $post_type ] ?? array() as $campo ) {
		$id = absint( mdc_ficha_field( $campo, $post_id ) );
		if ( $id ) {
			return $id;
		}
	}

	return has_post_thumbnail( $post_id ) ? get_post_thumbnail_id( $post_id ) : 0;
}

function mdc_ficha_archive_link( $post_type ) {
	$link = get_post_type_archive_link( $post_type );
	return $link ? $link : home_url( '/' );
}

function mdc_ficha_breadcrumbs( $post_id = 0 ) {
	$post_id   = $post_id ? (int) $post_id : get_the_ID();
	$post_type = get_post_type( $post_id );
	$label     = mdc_post_type_label( $post_type, true );
	$archive   = mdc_ficha_archive_link( $post_type );

	return array(
		array( 'label' => 'Início', 'url' => home_url( '/' ) ),
		array( 'label' => $label, 'url' => $archive ),
		array( 'label' => get_the_title( $post_id ) ),
	);
}

function mdc_ficha_render_value( $value, $url = '' ) {
	if ( '' === $value || null === $value ) {
		return '';
	}

	if ( $url ) {
		return '<a href="' . esc_url( $url ) . '">' . esc_html( $value ) . '</a>';
	}

	return esc_html( $value );
}

function mdc_ficha_linhas( $post_id = 0 ) {
	$post_id   = $post_id ? (int) $post_id : get_the_ID();
	$post_type = get_post_type( $post_id );
	$linhas    = array();

	$add = static function( &$linhas, $rotulo, $valor, $url = '' ) {
		if ( '' !== $valor && null !== $valor ) {
			$linhas[] = array(
				'rotulo' => $rotulo,
				'valor'  => $valor,
				'url'    => $url,
			);
		}
	};

	switch ( $post_type ) {
		case 'copa':
			$inicio  = mdc_ficha_field( 'data_inicio', $post_id );
			$fim     = mdc_ficha_field( 'data_fim', $post_id );
			$campeao = mdc_field_id( 'campeao', $post_id );
			$vice    = mdc_field_id( 'vice', $post_id );
			$terceiro = mdc_field_id( 'terceiro', $post_id );
			$quarto  = mdc_field_id( 'quarto', $post_id );
			$artilheiro = mdc_field_id( 'artilheiro', $post_id );

			$add( $linhas, 'Ano', mdc_ficha_field( 'ano', $post_id ) );
			$add( $linhas, 'Sedes', wp_strip_all_tags( mdc_ficha_field( 'sedes', $post_id ) ) );
			$add( $linhas, 'Período', ( $inicio && $fim ) ? mdc_periodo( $inicio, $fim ) : '' );
			$add( $linhas, 'Campeã', $campeao ? mdc_title( $campeao ) : '', $campeao ? get_permalink( $campeao ) : '' );
			$add( $linhas, 'Vice-campeã', $vice ? mdc_title( $vice ) : '', $vice ? get_permalink( $vice ) : '' );
			$add( $linhas, '3º lugar', $terceiro ? mdc_title( $terceiro ) : '', $terceiro ? get_permalink( $terceiro ) : '' );
			$add( $linhas, '4º lugar', $quarto ? mdc_title( $quarto ) : '', $quarto ? get_permalink( $quarto ) : '' );
			$add( $linhas, 'Seleções', mdc_ficha_field( 'num_selecoes', $post_id ) );
			$add( $linhas, 'Partidas', mdc_ficha_field( 'num_jogos', $post_id ) );
			$add( $linhas, 'Gols', mdc_ficha_field( 'num_gols', $post_id ) );
			$add( $linhas, 'Artilheiro', $artilheiro ? mdc_title( $artilheiro ) : '', $artilheiro ? get_permalink( $artilheiro ) : '' );
			$add( $linhas, 'Gols do artilheiro', mdc_ficha_field( 'gols_artilheiro', $post_id ) );
			break;

		case 'entidade':
			$fundacao = mdc_ficha_field( 'fundacao', $post_id );
			$nivel = mdc_ficha_field( 'nivel_entidade', $post_id );
			$label_nivel = function_exists( 'mdc_entidade_nivel_label' ) ? mdc_entidade_nivel_label( $post_id ) : '';
			$add( $linhas, 'Tipo', $label_nivel );
			$add( $linhas, 'Sigla', mdc_ficha_field( 'sigla', $post_id ) );
			$add( $linhas, 'Nome oficial', mdc_ficha_field( 'nome_oficial', $post_id ) );
			$add( $linhas, 'Fundação', $fundacao ? mdc_data_campo( $fundacao ) : '' );
			$add( $linhas, 'Sede', mdc_ficha_field( 'sede', $post_id ) );
			$add( $linhas, 'Região', mdc_ficha_field( 'regiao', $post_id ) );
			$add( $linhas, 'Presidente', mdc_ficha_field( 'presidente', $post_id ) );
			$add( $linhas, 'Federações filiadas', mdc_ficha_field( 'membros', $post_id ) );
			$add( $linhas, 'Site oficial', mdc_ficha_field( 'site', $post_id ), mdc_ficha_field( 'site', $post_id ) );
			$add( $linhas, 'Endereço', wp_strip_all_tags( mdc_ficha_field( 'endereco', $post_id ) ) );
			break;

		case 'selecao':
			$fed   = mdc_field_id( 'federacao', $post_id );
			$conf  = mdc_field_id( 'confederacao', $post_id );
			$pais  = mdc_term_name( 'pais', $post_id );

			$add( $linhas, 'País', $pais, mdc_term_link( 'pais', $post_id ) );
			$add( $linhas, 'Sigla', mdc_ficha_field( 'sigla', $post_id ) );
			$add( $linhas, 'Títulos mundiais', mdc_ficha_field( 'titulos', $post_id ) );
			$add( $linhas, 'Participações em Copas', mdc_ficha_field( 'participacoes', $post_id ) );
			$add( $linhas, 'Melhor campanha', mdc_ficha_field( 'melhor_campanha', $post_id ) );
			$add( $linhas, 'Alcunhas', mdc_ficha_field( 'alcunhas', $post_id ) );
			$add( $linhas, 'Federação', $fed ? mdc_title( $fed ) : '', $fed ? get_permalink( $fed ) : '' );
			$add( $linhas, 'Confederação', $conf ? mdc_title( $conf ) : '', $conf ? get_permalink( $conf ) : '' );
			$add( $linhas, 'Treinador', mdc_ficha_field( 'treinador', $post_id ) );
			$add( $linhas, 'Capitão', mdc_ficha_field( 'capitao', $post_id ) );
			$add( $linhas, 'Ranking FIFA', mdc_ficha_field( 'ranking', $post_id ) ? '#' . mdc_ficha_field( 'ranking', $post_id ) : '' );
			break;

		case 'jogador':
			$sel = mdc_field_id( 'selecao', $post_id );
			$pos = mdc_term_name( 'posicao_jogador', $post_id );
			$nasc = mdc_ficha_field( 'data_nascimento', $post_id );

			$add( $linhas, 'Nome completo', mdc_ficha_field( 'nome_completo', $post_id ) );
			$add( $linhas, 'Posição', $pos );
			$add( $linhas, 'Seleção', $sel ? mdc_title( $sel ) : '', $sel ? get_permalink( $sel ) : '' );
			$add( $linhas, 'Nacionalidade', mdc_ficha_field( 'nacionalidade', $post_id ) );
			$add( $linhas, 'Nascimento', $nasc ? mdc_data_campo( $nasc ) : '' );
			$add( $linhas, 'Local de nascimento', mdc_ficha_field( 'local_nascimento', $post_id ) );
			$add( $linhas, 'Altura', mdc_ficha_field( 'altura', $post_id ) );
			$add( $linhas, 'Peso', mdc_ficha_field( 'peso', $post_id ) );
			$add( $linhas, 'Pé', mdc_ficha_field( 'pe', $post_id ) );
			$add( $linhas, 'Apelido', mdc_ficha_field( 'apelido', $post_id ) );
			$add( $linhas, 'Clube atual', mdc_ficha_field( 'clube_atual', $post_id ) );
			$add( $linhas, 'Copas disputadas', mdc_ficha_field( 'copas_disputadas', $post_id ) );
			$add( $linhas, 'Jogos em Copas', mdc_ficha_field( 'jogos_copas', $post_id ) );
			$add( $linhas, 'Gols em Copas', mdc_ficha_field( 'gols_copas', $post_id ) );
			$add( $linhas, 'Títulos', mdc_ficha_field( 'titulos', $post_id ) );
			$add( $linhas, 'Data de morte', mdc_ficha_field( 'data_morte', $post_id ) ? mdc_data_campo( mdc_ficha_field( 'data_morte', $post_id ) ) : '' );
			$add( $linhas, 'Local de morte', mdc_ficha_field( 'local_morte', $post_id ) );
			$add( $linhas, 'Clubes', wp_strip_all_tags( mdc_ficha_field( 'clubes', $post_id ) ) );
			$add( $linhas, 'Jogos na carreira', mdc_ficha_field( 'jogos_carreira', $post_id ) );
			$add( $linhas, 'Gols na carreira', mdc_ficha_field( 'gols_carreira', $post_id ) );
			$add( $linhas, 'Títulos na carreira', mdc_ficha_field( 'titulos_carreira', $post_id ) );
			$add( $linhas, 'Situação', mdc_ficha_field( 'status', $post_id ) );
			break;

		case 'estadio':
			$pais = mdc_term_name( 'pais', $post_id );
			$copas = mdc_field_ids( 'copas', $post_id );

			$add( $linhas, 'Cidade', mdc_ficha_field( 'cidade', $post_id ) );
			$add( $linhas, 'País', $pais, mdc_term_link( 'pais', $post_id ) );
			$add( $linhas, 'Capacidade', mdc_ficha_field( 'capacidade', $post_id ) ? number_format_i18n( (int) mdc_ficha_field( 'capacidade', $post_id ) ) . ' lugares' : '' );
			$add( $linhas, 'Copas recebidas', $copas ? count( $copas ) : '' );
			break;
	}

	return $linhas;
}

function mdc_ficha_stats( $post_id = 0 ) {
	$post_id   = $post_id ? (int) $post_id : get_the_ID();
	$post_type = get_post_type( $post_id );
	$stats     = array();

	$add = static function( &$stats, $icone, $valor, $rotulo, $texto = false ) {
		/*
		 * Número negativo em barra de estatística é sempre erro de cadastro —
		 * ninguém tem "-1 títulos mundiais". Some da barra em vez de aparecer.
		 */
		if ( ! $texto && is_numeric( $valor ) && (float) $valor < 0 ) {
			return;
		}

		if ( '' !== $valor && null !== $valor ) {
			$stats[] = array(
				'icone'  => $icone,
				'valor'  => $valor,
				'rotulo' => $rotulo,
				'texto'  => $texto,
			);
		}
	};

	switch ( $post_type ) {
		case 'copa':
			$campeao = mdc_field_id( 'campeao', $post_id );
			$add( $stats, 'trophy', $campeao ? mdc_title( $campeao ) : '', 'campeã', true );
			$add( $stats, 'users', mdc_ficha_field( 'num_selecoes', $post_id ), 'seleções' );
			$add( $stats, 'calendar', mdc_ficha_field( 'num_jogos', $post_id ), 'partidas' );
			$add( $stats, 'star', mdc_ficha_field( 'num_gols', $post_id ), 'gols' );
			break;

		case 'entidade':
			$fundacao = mdc_ficha_field( 'fundacao', $post_id );
			$add( $stats, 'calendar', $fundacao ? date_i18n( 'Y', strtotime( $fundacao ) ) : '', 'fundação' );
			$membros = mdc_ficha_field( 'membros', $post_id );
			$add( $stats, 'users', $membros, 'federações filiadas' );
			$regiao = mdc_ficha_field( 'regiao', $post_id );
			$add( $stats, 'globe', $regiao, 'região', true );
			break;

		case 'selecao':
			$add( $stats, 'trophy', mdc_ficha_field( 'titulos', $post_id ), 'títulos mundiais' );
			$add( $stats, 'calendar', mdc_ficha_field( 'participacoes', $post_id ), 'participações em Copas' );
			$add( $stats, 'star', mdc_ficha_field( 'melhor_campanha', $post_id ), 'melhor campanha', true );
			$ranking = mdc_ficha_field( 'ranking', $post_id );
			$add( $stats, 'globe', $ranking ? '#' . $ranking : '', 'ranking FIFA' );
			break;

		case 'jogador':
			$add( $stats, 'trophy', mdc_ficha_field( 'titulos', $post_id ), 'títulos mundiais' );
			$add( $stats, 'calendar', mdc_ficha_field( 'copas_disputadas', $post_id ), 'Copas disputadas' );
			$add( $stats, 'users', mdc_ficha_field( 'jogos_copas', $post_id ), 'jogos em Copas' );
			$add( $stats, 'star', mdc_ficha_field( 'gols_copas', $post_id ), 'gols em Copas' );
			break;

		case 'estadio':
			$cap = mdc_ficha_field( 'capacidade', $post_id );
			$copas = mdc_field_ids( 'copas', $post_id );
			$pais = mdc_term_name( 'pais', $post_id );
			$add( $stats, 'users', $cap ? number_format_i18n( (int) $cap ) : '', 'lugares' );
			$add( $stats, 'trophy', $copas ? count( $copas ) : '', 'Copas recebidas' );
			$add( $stats, 'globe', $pais, 'país', true );
			break;
	}

	return $stats;
}

function mdc_ficha_reportagens_relacionadas( $post_id = 0 ) {
	$post_id   = $post_id ? (int) $post_id : get_the_ID();
	$post_type = get_post_type( $post_id );

	$fallback = array(
		'copa'         => 'copa_relacionada',
		'selecao'      => 'selecoes_relacionadas',
		'jogador'      => 'jogadores_relacionados',
		'estadio'      => '',
		'entidade'     => '',
	);

	$campo_fallback = isset( $fallback[ $post_type ] ) ? $fallback[ $post_type ] : '';

	if ( function_exists( 'mdc_reportagens_relacionadas' ) ) {
		return mdc_reportagens_relacionadas(
			'reportagens_relacionadas',
			$campo_fallback,
			3
		);
	}

	return array();
}

/* Metabox universal de apresentação. */
function mdc_metabox_modelo_ficha() {
	$tipos = array_keys( mdc_modelos_ficha() );

	foreach ( $tipos as $post_type ) {
		add_meta_box(
			'mdc-modelo-ficha',
			'Modelo da ficha',
			'mdc_render_modelo_ficha_metabox',
			$post_type,
			'side',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'mdc_metabox_modelo_ficha', 20 );

function mdc_render_modelo_ficha_metabox( $post ) {
	$valor  = get_post_meta( $post->ID, '_mdc_modelo_ficha', true );
	$valor  = $valor ? $valor : 'automatico';
	$modelos = mdc_modelos_ficha();

	wp_nonce_field( 'mdc_modelo_ficha_save', 'mdc_modelo_ficha_nonce' );
	?>
	<p>
		<label for="mdc-modelo-ficha"><strong>Modelo de apresentação</strong></label>
	</p>
	<select id="mdc-modelo-ficha" name="mdc_modelo_ficha" style="width:100%;">
		<option value="automatico" <?php selected( $valor, 'automatico' ); ?>>Automático — <?php echo esc_html( mdc_modelo_ficha_label( mdc_modelo_ficha_padrao( $post->ID ) ) ); ?></option>
		<?php foreach ( $modelos as $key => $label ) : ?>
			<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $valor, $key ); ?>><?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>
	</select>
	<p class="description">O modelo altera a apresentação da ficha. Os campos continuam pertencendo ao tipo de conteúdo.</p>
	<?php
}

function mdc_salvar_modelo_ficha( $post_id ) {
	if (
		! isset( $_POST['mdc_modelo_ficha_nonce'] ) ||
		! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mdc_modelo_ficha_nonce'] ) ), 'mdc_modelo_ficha_save' )
	) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$valor = isset( $_POST['mdc_modelo_ficha'] )
		? sanitize_key( wp_unslash( $_POST['mdc_modelo_ficha'] ) )
		: 'automatico';

	if ( 'automatico' !== $valor && ! isset( mdc_modelos_ficha()[ $valor ] ) ) {
		$valor = 'automatico';
	}

	update_post_meta( $post_id, '_mdc_modelo_ficha', $valor );
}
add_action( 'save_post', 'mdc_salvar_modelo_ficha', 30 );

/* O campo História não é mais usado na apresentação. */
function mdc_ocultar_historia_admin() {
	$screen = get_current_screen();

	if ( ! $screen || ! in_array( $screen->post_type, array( 'copa', 'selecao', 'entidade' ), true ) ) {
		return;
	}
	?>
	<style>
		.post-type-copa .mdc-admin-field:has([name="mdc_historia"]),
		.post-type-selecao .mdc-admin-field:has([name="mdc_historia"]),
		.post-type-entidade .mdc-admin-field:has([name="mdc_historia"]) {
			display: none !important;
		}
	</style>
	<?php
}
add_action( 'admin_head', 'mdc_ocultar_historia_admin' );
