<?php
/**
 * Na Súmula — functions.php
 * Versão 1.5.1
 *
 * Núcleo do tema.
 * Mantém os módulos em /inc e concentra aqui apenas as funções
 * realmente pertencentes ao funcionamento global do tema.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================
 * CONSTANTES
 * ========================================================= */

if ( ! defined( 'MDC_THEME_VERSION' ) ) {
	define( 'MDC_THEME_VERSION', '1.5.1' );
}

if ( ! defined( 'MDC_THEME_DIR' ) ) {
	define( 'MDC_THEME_DIR', get_stylesheet_directory() );
}

if ( ! defined( 'MDC_THEME_URI' ) ) {
	define( 'MDC_THEME_URI', get_stylesheet_directory_uri() );
}

/* =========================================================
 * MÓDULOS DO TEMA
 * ========================================================= */

$mdc_core_files = array(
	'inc/mdc-config.php',
	'inc/mdc-seo.php',
	'inc/mdc-migrations.php',
	'inc/helpers.php',
	'inc/mdc-missing-functions.php',
	'inc/artigo.php',
	'inc/mdc-cpts.php',
	'inc/mdc-taxonomias.php',
	'inc/mdc-metaboxes.php',
	'inc/mdc-relacionamentos.php',
	'inc/mdc-ficha.php',
	'inc/mdc-colunistas.php',
	'inc/mdc-conta.php',
	'inc/mdc-atualizacao.php',
	'inc/mdc-quiz.php',
	'inc/mdc-ranking.php',
	'inc/mdc-ranking-fifa-feminino.php',
	'inc/mdc-shortcodes.php',
	'inc/mdc-painel.php',
	'inc/mdc-anuncie.php',
	'inc/mdc-newsletter.php',
);

foreach ( $mdc_core_files as $mdc_file ) {
	$mdc_path = MDC_THEME_DIR . '/' . $mdc_file;

	if ( file_exists( $mdc_path ) ) {
		require_once $mdc_path;
	}
}

/* =========================================================
 * CONFIGURAÇÃO DO TEMA
 * ========================================================= */

function mdc_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'elementor' );
	add_theme_support( 'customize-selective-refresh-widgets' );

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 300,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'topo'                   => __( 'Barra superior', 'mundo-da-copa' ),
			'principal'              => __( 'Menu principal', 'mundo-da-copa' ),
			'lateral'                => __( 'Menu lateral', 'mundo-da-copa' ),
			'rodape'                 => __( 'Menu do rodapé', 'mundo-da-copa' ),
			'rodape_explore'         => __( 'Rodapé — Explore', 'mundo-da-copa' ),
			'rodape_arquivo'         => __( 'Rodapé — Arquivo', 'mundo-da-copa' ),
			'rodape_institucional'   => __( 'Rodapé — Institucional', 'mundo-da-copa' ),
		)
	);

	add_image_size( 'mdc-card', 900, 560, true );
	add_image_size( 'mdc-hero', 1600, 900, true );
	add_image_size( 'mdc-hero-wide', 1920, 640, true );
	add_image_size( 'mdc-entity', 720, 480, true );
	add_image_size( 'mdc-player', 600, 750, true );
	add_image_size( 'mdc-selection', 720, 480, true );
	add_image_size( 'mdc-crest', 600, 600, false );
	add_image_size( 'mdc-poster', 720, 960, false );
}
add_action( 'after_setup_theme', 'mdc_theme_setup' );

/* =========================================================
 * COMENTÁRIOS
 * ========================================================= */

function mdc_enable_post_comments( $open, $post_id ) {
	if ( is_admin() || ! $post_id || 'post' !== get_post_type( $post_id ) ) {
		return $open;
	}

	return true;
}
add_filter( 'comments_open', 'mdc_enable_post_comments', 20, 2 );

/* =========================================================
 * SVG
 * ========================================================= */

function mdc_permitir_svg_upload( $mimes ) {
	if ( current_user_can( 'unfiltered_html' ) ) {
		$mimes['svg']  = 'image/svg+xml';
		$mimes['svgz'] = 'image/svg+xml';
	}

	return $mimes;
}
add_filter( 'upload_mimes', 'mdc_permitir_svg_upload' );

function mdc_corrigir_tipo_svg( $data, $file, $filename, $mimes, $real_mime ) {
	if ( current_user_can( 'unfiltered_html' ) && preg_match( '/\.svgz?$/i', $filename ) ) {
		$data['ext']  = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );
		$data['type'] = 'image/svg+xml';
	}

	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'mdc_corrigir_tipo_svg', 10, 5 );

/* =========================================================
 * CPTS E TAXONOMIAS
 * ========================================================= */

function mdc_base_register_content() {
	if ( function_exists( 'mdc_register_cpts' ) ) {
		mdc_register_cpts();
	}

	if ( function_exists( 'mdc_register_taxonomias' ) ) {
		mdc_register_taxonomias();
	}
}
add_action( 'init', 'mdc_base_register_content', 5 );

/* =========================================================
 * REGRAS DE URL
 * ========================================================= */

function mdc_base_flush_rewrites_once() {
	if ( get_option( 'mdc_base_rewrites_150' ) ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'mdc_base_rewrites_150', 1, false );
}
add_action( 'after_switch_theme', 'mdc_base_flush_rewrites_once' );

/* =========================================================
 * BUSCA
 * ========================================================= */

function mdc_include_cpts_in_search( $query ) {
	if (
		is_admin()
		|| ! $query->is_main_query()
		|| ! $query->is_search()
	) {
		return;
	}

	$query->set(
		'post_type',
		array(
			'post',
			'copa',
			'selecao',
			'jogador',
			'estadio',
			'entidade',
			'colunista',
		)
	);
}
add_action( 'pre_get_posts', 'mdc_include_cpts_in_search' );

/* =========================================================
 * TAXONOMIA CONTINENTE
 * ========================================================= */

function mdc_template_continente( $template ) {
	if ( is_tax( 'continente' ) ) {
		$file = MDC_THEME_DIR . '/taxonomy-continente.php';

		if ( file_exists( $file ) ) {
			return $file;
		}
	}

	return $template;
}
add_filter( 'template_include', 'mdc_template_continente', 9999 );

function mdc_continente_posts_per_page( $query ) {
	if (
		! is_admin()
		&& $query->is_main_query()
		&& $query->is_tax( 'continente' )
	) {
		$query->set( 'posts_per_page', 12 );
	}
}
add_action( 'pre_get_posts', 'mdc_continente_posts_per_page' );

/* =========================================================
 * SIDEBAR
 * ========================================================= */

function mdc_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Barra lateral', 'mundo-da-copa' ),
			'id'            => 'mdc-lateral',
			'description'   => __( 'Área de widgets do tema.', 'mundo-da-copa' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'mdc_widgets_init' );

/* =========================================================
 * EXCERTOS
 * ========================================================= */

function mdc_excerpt_length() {
	return 22;
}
add_filter( 'excerpt_length', 'mdc_excerpt_length' );

function mdc_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'mdc_excerpt_more' );

/* =========================================================
 * PÁGINA "ANUNCIE AQUI"
 * ========================================================= */

function mdc_is_anuncie_page() {
	if ( is_admin() ) {
		return false;
	}

	if ( is_page( 'anuncie-aqui' ) ) {
		return true;
	}

	if ( is_page_template( 'page-anuncie-aqui.php' ) ) {
		return true;
	}

	if ( is_page_template( 'page-anuncie.php' ) ) {
		return true;
	}

	$page_id = absint( get_theme_mod( 'ns_anuncie_page_id', 0 ) );

	return $page_id && is_page( $page_id );
}

/* =========================================================
 * BODY CLASS
 * ========================================================= */

function mdc_body_class( $classes ) {
	if ( is_post_type_archive() || is_category() || is_tag() || is_tax() ) {
		$classes[] = 'mdc-archive-page';
	}

	if ( is_singular() ) {
		$classes[] = 'mdc-single-' . sanitize_html_class( get_post_type() );
	}

	if ( is_tax( 'continente' ) ) {
		$term = get_queried_object();

		if ( ! empty( $term->slug ) ) {
			$classes[] = 'mdc-continent-' . sanitize_html_class( $term->slug );
		}
	}

	if ( is_front_page() || is_home() ) {
		$classes[] = 'mdc-home';
	}

	if ( mdc_is_anuncie_page() ) {
		$classes[] = 'mdc-anuncie-page';
	}

	return $classes;
}
add_filter( 'body_class', 'mdc_body_class' );

/* =========================================================
 * IMAGENS DAS ENTIDADES
 * ========================================================= */

function mdc_entity_image_id( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();

	$cover = (int) get_post_meta( $post_id, 'mdc_capa', true );

	if ( $cover ) {
		return $cover;
	}

	return (int) get_post_thumbnail_id( $post_id );
}

function mdc_entity_image( $post_id = 0, $size = 'large', $attr = array() ) {
	$post_id  = $post_id ? (int) $post_id : get_the_ID();
	$image_id = mdc_entity_image_id( $post_id );

	if ( ! $image_id ) {
		return '';
	}

	$default = array(
		'loading'  => 'lazy',
		'decoding' => 'async',
		'alt'      => get_the_title( $post_id ),
	);

	return wp_get_attachment_image(
		$image_id,
		$size,
		false,
		array_merge( $default, $attr )
	);
}

function mdc_entity_label( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$type    = get_post_type( $post_id );

	$labels = array(
		'copa'         => 'Copa do Mundo',
		'selecao'      => 'Seleção',
		'jogador'      => 'Jogador',
		'estadio'      => 'Estádio',
		'entidade'      => 'Entidade',
		'colunista'    => 'Colunista',
		'post'         => 'Reportagem',
	);

	return isset( $labels[ $type ] ) ? $labels[ $type ] : 'Conteúdo';
}

function mdc_has_entity_image( $post_id = 0 ) {
	return mdc_entity_image_id( $post_id ) > 0;
}

/**
 * Busca a Copa (CPT "copa") de um ano específico. Primeiro tenta o
 * campo "mdc_ano" (comparação numérica, tolerante a formatação).
 * Se não encontrar — por exemplo, quando o campo "Ano" ainda não foi
 * preenchido — cai para uma busca pelo ano no título do post (ex.:
 * "Copa do Mundo FIFA 2030"), que é como os registros já nomeiam a
 * edição na prática.
 */
function mdc_copa_por_ano( $ano ) {
	$ano = (int) $ano;

	$posts = get_posts( array(
		'post_type'      => 'copa',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'no_found_rows'  => true,
		'meta_query'     => array(
			array(
				'key'     => 'mdc_ano',
				'value'   => $ano,
				'compare' => '=',
				'type'    => 'NUMERIC',
			),
		),
	) );

	if ( ! empty( $posts ) ) {
		return (int) $posts[0]->ID;
	}

	$posts = get_posts( array(
		'post_type'      => 'copa',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'no_found_rows'  => true,
		's'              => (string) $ano,
	) );

	return ! empty( $posts ) ? (int) $posts[0]->ID : 0;
}

/**
 * País (ou países) sede de uma Copa, usado no bloco "Memória" da Home.
 * Lê o campo real do metabox da Copa, "mdc_sedes" ("Países e cidades-
 * sede"), e retorna apenas o primeiro item — o suficiente para a
 * linha curta exibida sob o pôster.
 */
function mdc_copa_sede( $post_id ) {
	$valor = get_post_meta( $post_id, 'mdc_sedes', true );

	if ( ! $valor ) {
		return '';
	}

	$linhas = preg_split( '/[\r\n,]+/', trim( $valor ) );
	$linhas = array_values( array_filter( array_map( 'trim', $linhas ) ) );

	return ! empty( $linhas ) ? $linhas[0] : '';
}

/* =========================================================
 * FILTRO DE GÊNERO
 * ========================================================= */

function mdc_aplicar_filtro_genero_arquivo( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! is_post_type_archive() ) {
		return;
	}

	$post_type = $query->get( 'post_type' );

	if ( is_array( $post_type ) ) {
		$post_type = reset( $post_type );
	}

	$map = array(
		'copa'    => 'categoria_copa',
		'selecao' => 'categoria_selecao',
		'jogador' => 'categoria_jogador',
	);

	if ( ! isset( $map[ $post_type ] ) ) {
		return;
	}

	$filtro = isset( $_GET['genero'] )
		? sanitize_key( wp_unslash( $_GET['genero'] ) )
		: 'todos';

	if ( ! in_array( $filtro, array( 'todos', 'masculino', 'feminino' ), true ) || 'todos' === $filtro ) {
		return;
	}

	$slugs = 'feminino' === $filtro
		? array( 'feminino', 'feminina' )
		: array( 'masculino', 'masculina' );

	$tax_query   = (array) $query->get( 'tax_query' );
	$tax_query[] = array(
		'taxonomy' => $map[ $post_type ],
		'field'    => 'slug',
		'terms'    => $slugs,
		'operator' => 'IN',
	);

	$query->set( 'tax_query', $tax_query );
}
add_action( 'pre_get_posts', 'mdc_aplicar_filtro_genero_arquivo' );

/* =========================================================
 * CSS DINÂMICO
 * ========================================================= */

function mdc_front_dynamic_css() {
	if ( ! function_exists( 'mdc_config' ) ) {
		return;
	}

	$get = static function( $key, $default = '' ) {
		$value = mdc_config( $key );

		return ( '' !== $value && null !== $value ) ? $value : $default;
	};

	$cores = array(
		'ns-black'      => $get( 'mdc_cor_primaria', '#111111' ),
		'ns-red'        => $get( 'mdc_cor_destaque', '#C83A32' ),
		'ns-white'      => $get( 'mdc_cor_fundo', '#FFFFFF' ),
		'ns-gray'       => $get( 'mdc_cor_fundo_suave', '#F2F2F2' ),
		'ns-muted'      => $get( 'mdc_cor_texto_suave', '#757575' ),
		'ns-dark'       => $get( 'mdc_cor_fundo_escuro', '#111111' ),
		'ns-dark-text'  => $get( 'mdc_cor_texto_escuro', '#FFFFFF' ),
		'ns-text'       => $get( 'mdc_cor_texto', '#111111' ),
	);

	$fontes_titulos = array(
		'manrope'    => 'Manrope, sans-serif',
		'inter'      => 'Inter, sans-serif',
		'montserrat' => 'Montserrat, sans-serif',
	);

	$fontes_textos = array(
		'inter'      => 'Inter, sans-serif',
		'manrope'    => 'Manrope, sans-serif',
		'montserrat' => 'Montserrat, sans-serif',
	);

	$titulos = $fontes_titulos[ $get( 'mdc_fonte_titulos', 'manrope' ) ] ?? $fontes_titulos['manrope'];
	$textos  = $fontes_textos[ $get( 'mdc_fonte_textos', 'inter' ) ] ?? $fontes_textos['inter'];

	$site    = absint( $get( 'mdc_largura_site', 1200 ) );
	$artigo  = absint( $get( 'mdc_largura_artigo', 760 ) );
	$sidebar = absint( $get( 'mdc_largura_sidebar', 300 ) );

	$site    = $site >= 960 ? $site : 1200;
	$artigo  = $artigo >= 600 ? $artigo : 760;
	$sidebar = $sidebar >= 240 ? $sidebar : 300;

	$h1 = absint( $get( 'mdc_tamanho_h1', 64 ) );
	$h2 = absint( $get( 'mdc_tamanho_h2', 40 ) );
	$h3 = absint( $get( 'mdc_tamanho_h3', 28 ) );
	$body = absint( $get( 'mdc_tamanho_corpo', 17 ) );
	$weight = absint( $get( 'mdc_peso_titulos', 800 ) );

	$h1 = $h1 > 0 ? $h1 : 64;
	$h2 = $h2 > 0 ? $h2 : 40;
	$h3 = $h3 > 0 ? $h3 : 28;
	$body = $body > 0 ? $body : 17;
	$weight = $weight > 0 ? $weight : 800;

	$css = ':root{';

	foreach ( $cores as $key => $value ) {
		$css .= '--' . $key . ':' . sanitize_text_field( $value ) . ';';
	}

	$css .= '--mdc-site-width:' . $site . 'px;';
	$css .= '--mdc-article-width:' . $artigo . 'px;';
	$css .= '--mdc-sidebar-width:' . $sidebar . 'px;';
	$css .= '--mdc-title-font:' . $titulos . ';';
	$css .= '--mdc-body-font:' . $textos . ';';
	$css .= '--mdc-title-weight:' . $weight . ';';
	$css .= '--mdc-h1-size:' . $h1 . 'px;';
	$css .= '--mdc-h2-size:' . $h2 . 'px;';
	$css .= '--mdc-h3-size:' . $h3 . 'px;';
	$css .= '--mdc-body-size:' . $body . 'px;';
	$css .= '}';

	$css .= 'body{font-family:var(--mdc-body-font);font-size:var(--mdc-body-size);color:var(--ns-text);}';
	$css .= 'h1,h2,h3,h4,h5,h6{font-family:var(--mdc-title-font);font-weight:var(--mdc-title-weight);}';
	$css .= '.mdc-container{max-width:var(--mdc-site-width);}';
	$css .= '.mdc-article__content{max-width:var(--mdc-article-width);}';
	$css .= '.mdc-article-sidebar{flex-basis:var(--mdc-sidebar-width);width:var(--mdc-sidebar-width);}';

	$css .= '@media(max-width:820px){';
	$css .= 'h1{font-size:clamp(34px,9vw,var(--mdc-h1-size));}';
	$css .= 'h2{font-size:clamp(26px,7vw,var(--mdc-h2-size));}';
	$css .= 'h3{font-size:clamp(20px,6vw,var(--mdc-h3-size));}';
	$css .= '}';

	printf(
		'<style id="mdc-dynamic-theme">%s</style>',
		wp_strip_all_tags( $css )
	);
}
add_action( 'wp_head', 'mdc_front_dynamic_css', 20 );

/* =========================================================
 * PRECONNECT
 * ========================================================= */

function mdc_resource_hints( $urls, $relation ) {
	if ( 'preconnect' !== $relation ) {
		return $urls;
	}

	$urls[] = 'https://fonts.googleapis.com';

	$urls[] = array(
		'href'        => 'https://fonts.gstatic.com',
		'crossorigin' => 'anonymous',
	);

	return $urls;
}
add_filter( 'wp_resource_hints', 'mdc_resource_hints', 10, 2 );

/* =========================================================
 * TEMA CLARO / ESCURO
 * ========================================================= */

function mdc_front_config_script() {
	if ( ! function_exists( 'mdc_config' ) ) {
		return;
	}

	$theme = mdc_config( 'mdc_modo_padrao' );
	$theme = in_array( $theme, array( 'light', 'dark', 'system' ), true )
		? $theme
		: 'system';

	/*
	 * Este script é inserido somente depois que o handle mdc-theme
	 * já foi registrado/enfileirado por mdc_base_front_assets().
	 */
	wp_add_inline_script(
		'mdc-theme',
		'window.MDCThemeConfig = ' . wp_json_encode(
			array(
				'defaultTheme' => $theme,
			)
		) . ';',
		'before'
	);
}
add_action( 'wp_enqueue_scripts', 'mdc_front_config_script', 30 );

function mdc_theme_mode_script() {
	$default_theme = function_exists( 'mdc_config' )
		? mdc_config( 'mdc_modo_padrao' )
		: 'system';

	$default_theme = in_array(
		$default_theme,
		array( 'light', 'dark', 'system' ),
		true
	) ? $default_theme : 'system';
	?>
	<script>
	(function () {
		try {
			var saved = localStorage.getItem('mdc-theme');
			var configured = <?php echo wp_json_encode( $default_theme ); ?>;

			var theme = (saved === 'dark' || saved === 'light')
				? saved
				: (
					(configured === 'dark' || configured === 'light')
						? configured
						: (
							window.matchMedia &&
							window.matchMedia('(prefers-color-scheme: dark)').matches
								? 'dark'
								: 'light'
						)
				);

			document.documentElement.setAttribute('data-mdc-theme', theme);
		} catch (e) {
			document.documentElement.setAttribute('data-mdc-theme', 'light');
		}
	})();
	</script>
	<?php
}
add_action( 'wp_head', 'mdc_theme_mode_script', 1 );

/* =========================================================
 * ASSETS — FRONT-END
 *
 * Ordem importante:
 * 1. style.css
 * 2. identidade
 * 3. header
 * 4. layout
 *
 * O CSS de identidade e os ajustes de layout ficam nas folhas
 * principais, evitando dependências e correções isoladas.
 * ========================================================= */

function mdc_base_front_assets() {
	$css_files = array(
		'mdc-theme' => array(
			'style.css',
			array(),
		),
		'mdc-header' => array(
			'assets/css/mdc-header.css',
			array( 'mdc-theme' ),
		),
		'mdc-layout' => array(
			'assets/css/mdc-layout.css',
			array( 'mdc-header' ),
		),
	);

	if (
		is_front_page()
		|| is_home()
		|| is_singular( 'post' )
		|| is_singular( 'colunista' )
		|| is_post_type_archive( 'colunista' )
	) {
		$css_files['mdc-colunistas'] = array(
			'assets/css/colunistas.css',
			array( 'mdc-theme' ),
		);
	}


	if ( is_post_type_archive() || is_category() || is_tag() || is_tax() ) {
		$css_files['mdc-arquivos'] = array(
			'assets/css/mdc-arquivos.css',
			array( 'mdc-layout' ),
		);
	}

	if ( is_singular( 'post' ) ) {
		$css_files['mdc-single'] = array(
			'assets/css/single.css',
			array( 'mdc-theme', 'mdc-colunistas' ),
		);
	}

	foreach ( $css_files as $handle => $item ) {
		$file = MDC_THEME_DIR . '/' . $item[0];

		if ( ! file_exists( $file ) ) {
			continue;
		}

		wp_enqueue_style(
			$handle,
			MDC_THEME_URI . '/' . $item[0],
			$item[1],
			(string) filemtime( $file )
		);
	}

	/*
	 * Página comercial.
	 * O módulo inc/mdc-anuncie.php é a fonte dessa folha quando disponível.
	 * O fallback abaixo evita que a página fique sem estilo em instalações
	 * nas quais o módulo ainda não tenha carregado o asset.
	 */
	if ( mdc_is_anuncie_page() ) {
		$anuncie_css = MDC_THEME_DIR . '/assets/css/anuncie-aqui.css';

		if ( file_exists( $anuncie_css ) && ! wp_style_is( 'na-sumula-anuncie-aqui', 'enqueued' ) ) {
			wp_enqueue_style(
				'na-sumula-anuncie-aqui',
				MDC_THEME_URI . '/assets/css/anuncie-aqui.css',
				array( 'mdc-layout' ),
				(string) filemtime( $anuncie_css )
			);
		}
	}


	/* JavaScript principal do tema. */
	$theme_js = MDC_THEME_DIR . '/assets/js/mdc-theme.js';

	if ( file_exists( $theme_js ) ) {
		wp_enqueue_script(
			'mdc-theme',
			MDC_THEME_URI . '/assets/js/mdc-theme.js',
			array(),
			(string) filemtime( $theme_js ),
			true
		);

		wp_localize_script(
			'mdc-theme',
			'MDCTheme',
			array(
				'homeUrl' => home_url( '/' ),
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/* Interativos dos posts. */
	if ( is_singular( 'post' ) ) {
		$formato = get_post_meta( get_queried_object_id(), 'mdc_formato_post', true );

		if ( in_array( $formato, array( 'interativo', 'quiz' ), true ) ) {
			$interactive_js = MDC_THEME_DIR . '/assets/js/mdc-interativos.js';

			if ( file_exists( $interactive_js ) ) {
				wp_enqueue_script(
					'mdc-interativos',
					MDC_THEME_URI . '/assets/js/mdc-interativos.js',
					array(),
					(string) filemtime( $interactive_js ),
					true
				);

				wp_localize_script(
					'mdc-interativos',
					'MDCInteractive',
					array(
						'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
						'voteAction'  => 'mdc_enquete_vote',
						'quizAction'  => 'mdc_quiz_resultado',
						'liveAction'  => 'mdc_atualizacao_feed',
					)
				);
			}
		}
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'mdc_base_front_assets', 20 );

/* =========================================================
 * COMPATIBILIDADE ELEMENTOR FREE
 * ========================================================= */

add_theme_support( 'elementor' );
add_theme_support( 'customize-selective-refresh-widgets' );

/* =========================================================
 * TEMPLATE ELEMENTOR
 * ========================================================= */

function mdc_page_elementor_template( $template ) {
	return $template;
}
add_filter( 'template_include', 'mdc_page_elementor_template' );
