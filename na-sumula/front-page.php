<?php
/**
 * Front page editorial do Na Súmula.
 *
 * @package mundo-da-copa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/* =========================================================
 * AO VIVO
 * ========================================================= */

$ns_home_live_query = new WP_Query(
	array(
		'post_type'              => 'post',
		'post_status'            => 'publish',
		'posts_per_page'         => 1,
		'ignore_sticky_posts'    => true,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'orderby'                => 'modified',
		'order'                  => 'DESC',
		'meta_query'             => array(
			array(
				'key'     => 'mdc_em_atualizacao',
				'value'   => array( '1', 'true', 'yes', 'sim', 'on', 'ativo', 'active' ),
				'compare' => 'IN',
			),
		),
	)
);

if ( $ns_home_live_query->have_posts() ) :
	$ns_home_live_query->the_post();
	?>
	<div class="mdc-breaking mdc-breaking--live mdc-breaking--home" role="status" aria-label="<?php esc_attr_e( 'Atualização ao vivo', 'mundo-da-copa' ); ?>">
		<div class="mdc-container mdc-breaking__inner">
			<span class="mdc-breaking__label">
				<span class="mdc-breaking__live-icon" aria-hidden="true"><?php echo mdc_icon( 'radio', 16 ); ?></span>
				<?php esc_html_e( 'AO VIVO', 'mundo-da-copa' ); ?>
			</span>
			<span class="mdc-breaking__separator" aria-hidden="true">•</span>
			<a class="mdc-breaking__text" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</div>
	</div>
	<?php
	wp_reset_postdata();
endif;

/* =========================================================
 * LINKS EDITORIAIS
 * ========================================================= */

$posts_url = get_post_type_archive_link( 'post' );
$posts_url = $posts_url ? $posts_url : home_url( '/' );

$mdc_noticias_category = get_category_by_slug( 'noticias' );
if ( ! $mdc_noticias_category ) {
	$mdc_noticias_category = get_category_by_slug( 'noticia' );
}
$mdc_noticias_url = $mdc_noticias_category ? get_category_link( $mdc_noticias_category->term_id ) : $posts_url;

$mdc_historia_category = get_category_by_slug( 'historia' );
$mdc_historia_url = $mdc_historia_category ? get_category_link( $mdc_historia_category->term_id ) : home_url( '/historia/' );

/* =========================================================
 * MANCHETE
 * ========================================================= */

$hero_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 1,
		'ignore_sticky_posts' => false,
		'no_found_rows'       => true,
	)
);

$hero_id = $hero_query->have_posts() ? (int) $hero_query->posts[0]->ID : 0;

$mdc_home_hero_id = function_exists( 'mdc_config' ) ? absint( mdc_config( 'mdc_home_hero' ) ) : 0;

if ( $mdc_home_hero_id && wp_attachment_is_image( $mdc_home_hero_id ) ) {
	$mdc_home_hero_image = wp_get_attachment_image_url( $mdc_home_hero_id, 'full' );
} else {
	$mdc_home_hero_image = $hero_id && has_post_thumbnail( $hero_id ) ? get_the_post_thumbnail_url( $hero_id, 'full' ) : '';
}

$secondary_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 4,
		'post__not_in'        => $hero_id ? array( $hero_id ) : array(),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

/* =========================================================
 * PRÓXIMAS COPAS
 *
 * Os registros são escolhidos pela Data de início cadastrada.
 * Não existem datas, anos ou sedes fixados no template.
 * ========================================================= */

$ns_today = current_time( 'Y-m-d' );

$ns_next_cups_query = new WP_Query(
	array(
		'post_type'              => 'copa',
		'post_status'            => 'publish',
		'posts_per_page'         => 2,
		'no_found_rows'          => true,
		'ignore_sticky_posts'    => true,
		'meta_key'               => 'mdc_data_inicio',
		'orderby'                => 'meta_value',
		'order'                  => 'ASC',
		'meta_type'              => 'DATE',
		'meta_query'             => array(
			array(
				'key'     => 'mdc_data_inicio',
				'value'   => $ns_today,
				'compare' => '>=',
				'type'    => 'DATE',
			),
		),
	)
);

$ns_next_cups = $ns_next_cups_query->posts;

if ( count( $ns_next_cups ) < 2 ) {
	$ns_fallback_query = new WP_Query(
		array(
			'post_type'              => 'copa',
			'post_status'            => 'publish',
			'posts_per_page'         => 2,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'orderby'                => 'date',
			'order'                  => 'DESC',
		)
	);

	foreach ( $ns_fallback_query->posts as $ns_fallback_cup ) {
		if ( ! in_array( $ns_fallback_cup->ID, wp_list_pluck( $ns_next_cups, 'ID' ), true ) ) {
			$ns_next_cups[] = $ns_fallback_cup;
		}
		if ( count( $ns_next_cups ) >= 2 ) {
			break;
		}
	}
}

/**
 * Normaliza a data cadastrada para o atributo data-countdown.
 */
if ( ! function_exists( 'ns_home_cup_start_date' ) ) {
	function ns_home_cup_start_date( $post_id ) {
		$value = get_post_meta( $post_id, 'mdc_data_inicio', true );

		if ( ! $value ) {
			return '';
		}

		$value = trim( (string) $value );

		if ( preg_match( '/^\d{2}\/\d{2}\/\d{4}$/', $value ) ) {
			$parts = explode( '/', $value );
			return sprintf( '%04d-%02d-%02dT00:00:00', $parts[2], $parts[1], $parts[0] );
		}

		if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $value ) ) {
			return $value . 'T00:00:00';
		}

		$timestamp = strtotime( $value );

		return $timestamp ? wp_date( 'Y-m-d\TH:i:s', $timestamp ) : '';
	}
}

/**
 * Retorna ano da Copa a partir do campo oficial, com fallback no título.
 */
if ( ! function_exists( 'ns_home_cup_year' ) ) {
	function ns_home_cup_year( $post_id ) {
		$year = absint( get_post_meta( $post_id, 'mdc_ano', true ) );

		if ( $year ) {
			return $year;
		}

		if ( preg_match( '/(19|20)\d{2}/', get_the_title( $post_id ), $match ) ) {
			return absint( $match[0] );
		}

		return '';
	}
}

/**
 * Retorna o texto completo do campo Países e cidades-sede.
 */
if ( ! function_exists( 'ns_home_cup_venues' ) ) {
	function ns_home_cup_venues( $post_id ) {
		$value = get_post_meta( $post_id, 'mdc_sedes', true );

		if ( ! $value ) {
			return '';
		}

		$value = wp_strip_all_tags( (string) $value );
		$value = preg_replace( '/\s+/', ' ', $value );

		return trim( $value );
	}
}

/**
 * Identifica o gênero pela taxonomia da Copa.
 */
if ( ! function_exists( 'ns_home_cup_gender' ) ) {
	function ns_home_cup_gender( $post_id ) {
		$terms = get_the_terms( $post_id, 'categoria_copa' );

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$slug = sanitize_title( $term->slug );
				if ( in_array( $slug, array( 'feminino', 'feminina' ), true ) ) {
					return 'FEMININA';
				}
				if ( in_array( $slug, array( 'masculino', 'masculina' ), true ) ) {
					return 'MASCULINA';
				}
			}
		}

		$title = strtolower( get_the_title( $post_id ) );

		if ( false !== strpos( $title, 'feminina' ) || false !== strpos( $title, 'feminino' ) ) {
			return 'FEMININA';
		}

		return 'MASCULINA';
	}
}

/**
 * Resume a lista de países-sede para caber na faixa minimalista.
 * "Espanha, Portugal, Marrocos, Argentina, Paraguai e Uruguai" vira
 * "Espanha e mais 5 países". Sede única volta como veio.
 */
if ( ! function_exists( 'ns_home_bar_venue_short' ) ) {
	function ns_home_bar_venue_short( $venues ) {
		if ( ! $venues ) {
			return '';
		}

		$normalized = preg_replace( '/\s+e\s+/u', ', ', trim( $venues ) );
		$parts      = array_filter( array_map( 'trim', explode( ',', $normalized ) ) );
		$count      = count( $parts );

		if ( $count <= 1 ) {
			return $venues;
		}

		$rest = $count - 1;

		return sprintf( '%s e mais %d %s', $parts[0], $rest, 1 === $rest ? 'país' : 'países' );
	}
}

/**
 * Poster da Copa.
 */
if ( ! function_exists( 'ns_home_cup_poster' ) ) {
	function ns_home_cup_poster( $post_id ) {
		$poster = absint( get_post_meta( $post_id, 'mdc_capa', true ) );

		if ( $poster && wp_attachment_is_image( $poster ) ) {
			return $poster;
		}

		if ( function_exists( 'mdc_entity_image_id' ) ) {
			$poster = absint( mdc_entity_image_id( $post_id ) );
			if ( $poster ) {
				return $poster;
			}
		}

		$thumb = get_post_thumbnail_id( $post_id );

		return $thumb ? absint( $thumb ) : 0;
	}
}
?>

<main id="primary" class="mdc-home">

	<?php if ( ! empty( $ns_next_cups ) ) : ?>
	<section class="mdc-countdown mdc-countdown--bar" aria-label="Contagem regressiva para as próximas Copas do Mundo">
		<div class="mdc-container mdc-countdown__bar-inner">

			<span class="mdc-countdown__bar-kicker">
				<span class="mdc-countdown__bar-dot" aria-hidden="true"></span>
				Contagem regressiva
			</span>

			<div class="mdc-countdown__bar-items">
				<?php foreach ( $ns_next_cups as $ns_index => $ns_cup ) : ?>
					<?php
					$ns_cup_id        = (int) $ns_cup->ID;
					$ns_cup_year      = ns_home_cup_year( $ns_cup_id );
					$ns_cup_start     = ns_home_cup_start_date( $ns_cup_id );
					$ns_cup_venues    = ns_home_bar_venue_short( ns_home_cup_venues( $ns_cup_id ) );
					$ns_cup_gender    = ns_home_cup_gender( $ns_cup_id );
					$ns_cup_permalink = get_permalink( $ns_cup_id );
					$ns_gender_class  = 'FEMININA' === $ns_cup_gender ? 'mdc-countdown__bar-item--women' : 'mdc-countdown__bar-item--men';
					?>
					<?php if ( $ns_index > 0 ) : ?>
						<span class="mdc-countdown__bar-divider" aria-hidden="true"></span>
					<?php endif; ?>

					<a
						class="mdc-countdown__bar-item <?php echo esc_attr( $ns_gender_class ); ?>"
						href="<?php echo esc_url( $ns_cup_permalink ); ?>"
						<?php if ( $ns_cup_start ) : ?>data-countdown="<?php echo esc_attr( $ns_cup_start ); ?>"<?php endif; ?>
					>
						<strong data-countdown-days aria-live="polite">—</strong>
						<span class="mdc-countdown__bar-copy">
							dias para a Copa <?php echo esc_html( ucfirst( strtolower( $ns_cup_gender ) ) ); ?><?php if ( $ns_cup_year ) : ?> de <?php echo esc_html( $ns_cup_year ); ?><?php endif; ?>
							<?php if ( $ns_cup_venues ) : ?><em>(<?php echo esc_html( $ns_cup_venues ); ?>)</em><?php endif; ?>
						</span>
					</a>
				<?php endforeach; ?>
			</div>

		</div>
	</section>
	<?php endif; ?>

	<?php if ( $mdc_home_hero_image ) : ?>
	<section class="mdc-home-hero" style="background-image:url('<?php echo esc_url( $mdc_home_hero_image ); ?>');">
		<div class="mdc-home-hero__scrim" aria-hidden="true"></div>
		<div class="mdc-container mdc-home-hero__inner">
			<span class="mdc-home-hero__kicker">Na Súmula</span>
			<h1>O futebol como <em>memória</em>, história e emoção.</h1>
			<p class="mdc-home-hero__dek">Notícias, reportagens e um arquivo para consultar o que já aconteceu — e acompanhar o que ainda vai acontecer.</p>
			<div class="mdc-home-hero__actions">
				<a class="mdc-home-hero__btn mdc-home-hero__btn--primary" href="<?php echo esc_url( $mdc_historia_url ); ?>">Ir para a história</a>
				<a class="mdc-home-hero__btn mdc-home-hero__btn--ghost" href="<?php echo esc_url( $mdc_noticias_url ); ?>">Últimas notícias</a>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<section class="mdc-home-feature">
		<div class="mdc-container">
			<div class="mdc-home-section-head">
				<div>
					<span class="mdc-section-kicker">EM DESTAQUE</span>
					<h2>O que merece sua atenção</h2>
				</div>
				<a class="mdc-outline-link" href="<?php echo esc_url( $mdc_noticias_url ); ?>">Todas as notícias <span>→</span></a>
			</div>

			<div class="mdc-home-feature-grid">
				<?php if ( $hero_query->have_posts() ) : $hero_query->the_post(); ?>
					<article class="mdc-home-main-story">
						<a class="mdc-home-main-story__media" href="<?php the_permalink(); ?>">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'mdc-hero', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async' ) ); ?>
							<?php else : ?>
								<span class="mdc-card__placeholder"><span>⚽</span></span>
							<?php endif; ?>
						</a>

						<div class="mdc-home-main-story__content">
							<span class="mdc-kicker"><?php echo esc_html( ! empty( get_the_category() ) ? get_the_category()[0]->name : 'Em destaque' ); ?></span>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>

							<div class="mdc-card__meta" aria-label="Informações da publicação">
								<span class="mdc-card__meta-item">
									<span class="mdc-card__meta-label">Data</span>
									<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( function_exists( 'mdc_data' ) ? mdc_data( get_the_ID() ) : get_the_date( 'j \d\e F \d\e Y' ) ); ?></time>
								</span>
								<span class="mdc-card__meta-separator" aria-hidden="true">•</span>
								<span class="mdc-card__meta-item">
									<span class="mdc-card__meta-label">Atualizado</span>
									<time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( get_the_modified_date( 'j \d\e F \d\e Y' ) ); ?></time>
								</span>
							</div>
						</div>
					</article>
				<?php endif; wp_reset_postdata(); ?>

				<div class="mdc-home-secondary">
					<?php if ( $secondary_query->have_posts() ) : ?>
						<?php while ( $secondary_query->have_posts() ) : $secondary_query->the_post(); ?>
							<?php get_template_part( 'template-parts/card-post', null, array( 'id' => get_the_ID(), 'compact' => true ) ); ?>
						<?php endwhile; wp_reset_postdata(); ?>
					<?php else : ?>
						<div class="mdc-empty-state">
							<strong>Mais histórias em breve.</strong>
							<span>Publique novas reportagens para preencher este espaço.</span>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<?php
	$archive_query = new WP_Query(
		array(
			'post_type'              => 'copa',
			'post_status'            => 'publish',
			'posts_per_page'         => 8,
			'no_found_rows'          => true,
			'orderby'                => 'meta_value_num',
			'meta_key'               => 'mdc_ano',
			'order'                  => 'DESC',
		)
	);
	?>

	<?php if ( $archive_query->have_posts() ) : ?>
	<section class="mdc-home-archive">
		<div class="mdc-container">
			<div class="mdc-home-section-head">
				<div>
					<span class="mdc-section-kicker">MEMÓRIA</span>
					<h2>Uma história que continua</h2>
				</div>
				<a class="mdc-text-link" href="<?php echo esc_url( home_url( '/copas/' ) ); ?>">Explorar todas as Copas <span>→</span></a>
			</div>

			<div class="mdc-home-archive-showcase" aria-label="Edições da Copa do Mundo">
				<?php while ( $archive_query->have_posts() ) : $archive_query->the_post(); ?>
					<?php
					$ns_copa_id      = get_the_ID();
					$ns_copa_poster  = absint( get_post_meta( $ns_copa_id, 'mdc_capa', true ) );
					$ns_copa_country = function_exists( 'mdc_copa_sede' ) ? mdc_copa_sede( $ns_copa_id ) : ns_home_cup_venues( $ns_copa_id );
					?>
					<a class="mdc-home-archive-showcase__item" href="<?php echo esc_url( get_permalink( $ns_copa_id ) ); ?>">
						<span class="mdc-home-archive-showcase__poster">
							<?php if ( $ns_copa_poster ) : ?>
								<?php echo wp_get_attachment_image( $ns_copa_poster, 'mdc-poster', false, array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( get_the_title( $ns_copa_id ) ) ) ); ?>
							<?php elseif ( has_post_thumbnail( $ns_copa_id ) ) : ?>
								<?php echo get_the_post_thumbnail( $ns_copa_id, 'mdc-poster', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( get_the_title( $ns_copa_id ) ) ) ); ?>
							<?php else : ?>
								<span class="mdc-home-archive-showcase__placeholder" aria-hidden="true">✦</span>
							<?php endif; ?>
						</span>
						<span class="mdc-home-archive-showcase__title"><?php echo esc_html( get_the_title( $ns_copa_id ) ); ?></span>
						<?php if ( $ns_copa_country ) : ?>
							<span class="mdc-home-archive-showcase__country"><?php echo esc_html( $ns_copa_country ); ?></span>
						<?php endif; ?>
					</a>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<section class="mdc-home-explore">
		<div class="mdc-container">
			<div class="mdc-home-section-head mdc-home-section-head--light">
				<div>
					<span class="mdc-section-kicker">GUIA DA COPA</span>
					<h2>Explore o arquivo</h2>
				</div>
				<span class="mdc-heading-note">Memória para consultar sempre.</span>
			</div>

			<div class="mdc-home-explore-grid">
				<?php
				$explore = array(
					'copas'      => array( 'label' => 'ARQUIVO', 'title' => 'Copas do Mundo', 'url' => home_url( '/copas/' ) ),
					'selecoes'   => array( 'label' => 'GUIA', 'title' => 'Seleções', 'url' => home_url( '/selecoes/' ) ),
					'jogadores'  => array( 'label' => 'PERSONAGENS', 'title' => 'Jogadores', 'url' => home_url( '/jogadores/' ) ),
					'estadios'   => array( 'label' => 'PALCOS', 'title' => 'Estádios', 'url' => home_url( '/estadios/' ) ),
				);
				?>

				<?php foreach ( $explore as $type => $item ) : ?>
					<a class="mdc-home-explore-card mdc-home-explore-card--<?php echo esc_attr( $type ); ?>" href="<?php echo esc_url( $item['url'] ); ?>">
						<span class="mdc-kicker"><?php echo esc_html( $item['label'] ); ?></span>
						<strong><?php echo esc_html( $item['title'] ); ?></strong>
						<span class="mdc-home-explore-card__arrow" aria-hidden="true">↗</span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
