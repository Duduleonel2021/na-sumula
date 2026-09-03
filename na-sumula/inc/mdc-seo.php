<?php
/**
 * SEO técnico básico e seguro.
 *
 * O tema fornece uma camada mínima quando não há plugin SEO ativo.
 * Se Rank Math, Yoast, AIOSEO ou SEOPress estiverem ativos, o tema não
 * imprime meta description, Open Graph ou Schema duplicados.
 *
 * @package mundo-da-copa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function mdc_seo_plugin_active() {
	return defined( 'RANK_MATH_VERSION' )
		|| class_exists( 'WPSEO_Options' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'SEOPRESS_VERSION' );
}

function mdc_seo_description() {
	if ( is_singular() ) {
		$post = get_queried_object();

		if ( ! empty( $post->post_excerpt ) ) {
			return wp_trim_words( wp_strip_all_tags( $post->post_excerpt ), 30, '…' );
		}

		$content = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );

		if ( $content ) {
			return wp_trim_words( $content, 30, '…' );
		}

		$description = get_bloginfo( 'description' );
		return $description ? wp_strip_all_tags( $description ) : '';
	}

	if ( is_category() || is_tag() || is_tax() ) {
		$description = term_description();
		return $description ? wp_trim_words( wp_strip_all_tags( $description ), 30, '…' ) : '';
	}

	if ( is_front_page() || is_home() ) {
		return wp_strip_all_tags( get_bloginfo( 'description' ) );
	}

	return '';
}

function mdc_seo_basic_head() {
	if ( mdc_seo_plugin_active() ) {
		return;
	}

	$description = mdc_seo_description();

	if ( $description ) {
		printf(
			'<meta name="description" content="%s">' . "\n",
			esc_attr( $description )
		);
	}

	$title = wp_get_document_title();
	$url   = is_singular() ? get_permalink() : home_url( '/' );

	if ( is_singular() && has_post_thumbnail() ) {
		$image = get_the_post_thumbnail_url( get_queried_object_id(), 'full' );
	} else {
		$image = '';
	}

	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( is_singular( 'post' ) ? 'article' : 'website' ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );

	if ( $description ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	}

	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	}
}
add_action( 'wp_head', 'mdc_seo_basic_head', 2 );

function mdc_seo_schema() {
	if ( mdc_seo_plugin_active() ) {
		return;
	}

	$site_url = home_url( '/' );
	$site_name = get_bloginfo( 'name' );

	if ( is_front_page() || is_home() ) {
		$data = array(
			'@context' => 'https://schema.org',
			'@type'    => 'WebSite',
			'name'     => $site_name,
			'url'      => $site_url,
		);
	} elseif ( is_singular( 'post' ) ) {
		$post_id = get_queried_object_id();
		$data = array(
			'@context'         => 'https://schema.org',
			'@type'            => 'Article',
			'headline'         => get_the_title( $post_id ),
			'datePublished'    => get_the_date( DATE_W3C, $post_id ),
			'dateModified'     => get_the_modified_date( DATE_W3C, $post_id ),
			'mainEntityOfPage' => get_permalink( $post_id ),
			'author'           => array(
				'@type' => 'Person',
				'name'  => get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) ),
			),
			'publisher'        => array(
				'@type' => 'Organization',
				'name'  => $site_name,
				'url'   => $site_url,
			),
		);

		$image = get_the_post_thumbnail_url( $post_id, 'full' );
		if ( $image ) {
			$data['image'] = array( $image );
		}
	} elseif ( is_singular() ) {
		$post_id = get_queried_object_id();
		$data = array(
			'@context' => 'https://schema.org',
			'@type'    => 'WebPage',
			'name'     => get_the_title( $post_id ),
			'url'      => get_permalink( $post_id ),
		);
	} elseif ( is_archive() || is_tax() ) {
		$data = array(
			'@context' => 'https://schema.org',
			'@type'    => 'CollectionPage',
			'name'     => wp_strip_all_tags( wp_get_document_title() ),
			'url'      => home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) ),
		);
	} else {
		return;
	}

	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'mdc_seo_schema', 3 );
