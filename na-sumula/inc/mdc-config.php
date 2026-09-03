<?php
/**
 * Na Súmula — Configuração central do tema.
 *
 * Fonte única das configurações usadas pelo núcleo e pelo painel.
 * O painel administrativo apenas edita theme_mods; não redefine
 * as funções de configuração.
 *
 * @package na-sumula
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Valores padrão do Na Súmula.
 *
 * @return array
 */
function mdc_config_padrao() {
	return array(
		'mdc_post_leiamais'       => true,
		'mdc_post_capitular'      => false,

		'mdc_site_nome'           => 'Na Súmula',
		'mdc_site_tagline'        => 'As histórias que o futebol não esqueceu.',
		'mdc_logo_claro'          => 0,
		'mdc_logo_escuro'         => 0,
		'mdc_home_hero'          => 0,

		'mdc_cor_primaria'        => '#111111',
		'mdc_cor_destaque'        => '#C83A32',
		'mdc_cor_fundo'           => '#FFFFFF',
		'mdc_cor_fundo_suave'     => '#F2F2F2',
		'mdc_cor_texto'           => '#111111',
		'mdc_cor_texto_suave'     => '#757575',
		'mdc_cor_fundo_escuro'    => '#111111',
		'mdc_cor_texto_escuro'    => '#FFFFFF',

		'mdc_fonte_titulos'       => 'manrope',
		'mdc_fonte_textos'        => 'inter',
		'mdc_peso_titulos'        => 800,
		'mdc_tamanho_h1'          => 64,
		'mdc_tamanho_h2'          => 40,
		'mdc_tamanho_h3'          => 28,
		'mdc_tamanho_corpo'       => 17,
		'mdc_largura_site'        => 1200,
		'mdc_largura_artigo'      => 760,
		'mdc_largura_sidebar'     => 300,

		'mdc_modo_padrao'         => 'system',
		'mdc_botao_tema'          => true,

		'mdc_footer_texto'        => 'As histórias que o futebol não esqueceu. Jornalismo, memória, personagens e informação para entender o futebol antes, durante e depois da Copa.',
		'mdc_footer_creditos'     => 'Conteúdo editorial independente.',
		'mdc_footer_colunas'      => 3,

		'mdc_ads'                 => array(),
		'mdc_anuncio_email'       => '',
		'mdc_media_kit'           => 0,
		'mdc_anuncio_periodos'    => "7 dias\n15 dias\n30 dias\n60 dias\n90 dias",
		'mdc_newsletter_ativo'    => true,
		'mdc_newsletter_titulo'   => 'Receba as histórias da Copa.',
		'mdc_newsletter_texto'    => 'Uma seleção de notícias, memória e conteúdo especial direto no seu e-mail.',
		'mdc_newsletter_email'    => '',
	);
}

/**
 * Retorna uma configuração do tema.
 *
 * @param string $chave Chave da configuração.
 * @return mixed
 */
function mdc_config( $chave ) {
	$padroes = mdc_config_padrao();

	if ( array_key_exists( $chave, $padroes ) ) {
		return get_theme_mod( $chave, $padroes[ $chave ] );
	}

	return get_theme_mod( $chave, '' );
}
