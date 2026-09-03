<?php
/**
 * Single de Posts — Mundo da Copa
 * Layout editorial: matéria + conteúdo + sidebar.
 */
get_header();

while ( have_posts() ) :
	the_post();

	$post_id   = get_the_ID();
	$categories = get_the_category();
	$category  = ! empty( $categories ) ? $categories[0] : null;
	$cat_name  = $category ? wp_strip_all_tags( $category->name ) : '';
	$cat_url   = $category ? get_category_link( $category->term_id ) : '';
	$author_id = (int) get_the_author_meta( 'ID' );
	$author_url = get_author_posts_url( $author_id );

	$colunista_id = function_exists( 'mdc_colunista_do_post' ) ? mdc_colunista_do_post( $post_id ) : 0;
	$colunista = $colunista_id && function_exists( 'mdc_dados_colunista' ) ? mdc_dados_colunista( $colunista_id ) : array();
	$em_atualizacao = function_exists( 'mdc_atualizacao_ativa' ) ? mdc_atualizacao_ativa( $post_id ) : ( '1' === (string) get_post_meta( $post_id, 'mdc_em_atualizacao', true ) );

	$summary = trim( (string) mdc_field( 'subtitulo', $post_id ) );
	if ( ! $summary ) {
		$summary = get_the_excerpt();
	}

	$indice = function_exists( 'mdc_prepara_sumario' ) ? mdc_prepara_sumario( $post_id ) : array();

	$gallery_value = mdc_field( 'galeria', $post_id );
	$gallery = array();
	if ( is_array( $gallery_value ) ) {
		$gallery = $gallery_value;
	} elseif ( is_string( $gallery_value ) && '' !== trim( $gallery_value ) ) {
		$gallery = preg_split( '/\s*,\s*/', trim( $gallery_value ) );
	}
	$gallery = array_values( array_filter( array_map( 'absint', (array) $gallery ) ) );

	$video_sources = array(
		mdc_field( 'video_url', $post_id ),
		mdc_field( 'videos', $post_id ),
	);
	$videos = array();
	foreach ( $video_sources as $source ) {
		if ( is_array( $source ) ) {
			$source = implode( "\n", $source );
		}
		$source = wp_strip_all_tags( (string) $source );
		if ( preg_match_all( '#https?://[^\s<>"\']+#i', $source, $matches ) ) {
			foreach ( $matches[0] as $url ) {
				$videos[] = trim( $url );
			}
		}
	}
	$videos = array_values( array_unique( array_filter( $videos ) ) );

	$audio_url = trim( (string) mdc_field( 'audio_url', $post_id ) );

	$patrocinado = '1' === (string) mdc_field( 'patrocinado', $post_id );
	$patrocinador = trim( (string) mdc_field( 'patrocinador_nome', $post_id ) );
	$patrocinador_url = trim( (string) mdc_field( 'patrocinador_url', $post_id ) );
	$patrocinador_logo = absint( mdc_field( 'patrocinador_logo', $post_id ) );
	$legenda = trim( (string) mdc_field( 'legenda_imagem', $post_id ) );

	$tags = get_the_tags( $post_id );
	$related_ids = function_exists( 'mdc_posts_relacionados' ) ? mdc_posts_relacionados( $post_id, 4 ) : array();
	$most_read_ids = function_exists( 'mdc_mais_lidas' ) ? mdc_mais_lidas( 5 ) : array();
?>
<article <?php post_class( 'mdc-article mdc-article--editorial' ); ?>>
	<header class="mdc-article__hero">
		<div class="mdc-container mdc-article__hero-inner">
			<?php if ( function_exists( 'mdc_breadcrumb' ) ) : ?>
				<div class="mdc-breadcrumbs"><?php mdc_breadcrumb(); ?></div>
			<?php endif; ?>

			<?php if ( $cat_name ) : ?>
				<a class="mdc-article__category" href="<?php echo esc_url( $cat_url ); ?>">
					<span class="mdc-section-kicker"><?php echo esc_html( $cat_name ); ?></span>
				</a>
			<?php endif; ?>

			<?php if ( $em_atualizacao ) : ?>
				<div class="mdc-live-badge"><span class="mdc-live-badge__dot" aria-hidden="true"></span> ATUALIZAÇÕES AO VIVO</div>
			<?php endif; ?>

			<h1><?php the_title(); ?></h1>

			<?php if ( $summary ) : ?>
				<p class="mdc-article__dek"><?php echo esc_html( wp_strip_all_tags( $summary ) ); ?></p>
			<?php endif; ?>

			<div class="mdc-article__meta" aria-label="Informações da publicação">
				<?php if ( $colunista ) : ?>
					<span class="mdc-article__meta-item"><strong>Coluna</strong><a href="<?php echo esc_url( $colunista['url'] ); ?>"><?php echo esc_html( $colunista['coluna'] ? $colunista['coluna'] : $colunista['nome'] ); ?></a></span>
					<span class="mdc-article__meta-separator" aria-hidden="true">•</span>
					<span class="mdc-article__meta-item"><strong>Colunista</strong><a href="<?php echo esc_url( $colunista['url'] ); ?>"><?php echo esc_html( $colunista['nome'] ); ?></a></span>
				<?php else : ?>
					<span class="mdc-article__meta-item"><strong>Por</strong><a href="<?php echo esc_url( $author_url ); ?>"><?php echo esc_html( get_the_author() ); ?></a></span>
				<?php endif; ?>

				<span class="mdc-article__meta-separator" aria-hidden="true">•</span>
				<span class="mdc-article__meta-item"><strong>Publicado em</strong><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( mdc_data( $post_id ) ); ?></time></span>
				<span class="mdc-article__meta-separator" aria-hidden="true">•</span>
				<span class="mdc-article__meta-item"><strong>Atualizado em</strong><time datetime="<?php echo esc_attr( get_the_modified_date( 'c' ) ); ?>"><?php echo esc_html( get_the_modified_date( 'j \d\e F \d\e Y' ) ); ?></time></span>
			</div>

			<?php if ( $colunista ) : ?>
				<div class="mdc-article__column-byline mdc-article__column-byline--compact" aria-label="Colunista da matéria">
					<a class="mdc-article__column-byline-photo" href="<?php echo esc_url( $colunista['url'] ); ?>" aria-label="Ver perfil de <?php echo esc_attr( $colunista['nome'] ); ?>">
						<?php if ( has_post_thumbnail( $colunista_id ) ) : ?>
							<?php echo get_the_post_thumbnail( $colunista_id, 'thumbnail', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => esc_attr( $colunista['nome'] ) ) ); ?>
						<?php else : ?>
							<span aria-hidden="true"><?php echo esc_html( mb_strtoupper( mb_substr( $colunista['nome'], 0, 1 ) ) ); ?></span>
						<?php endif; ?>
					</a>
					<div class="mdc-article__column-byline-data">
						<span>Coluna</span>
						<strong><a href="<?php echo esc_url( $colunista['url'] ); ?>"><?php echo esc_html( $colunista['nome'] ); ?></a></strong>
						<?php if ( ! empty( $colunista['coluna'] ) ) : ?><small><?php echo esc_html( $colunista['coluna'] ); ?></small><?php endif; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</header>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="mdc-article__cover">
			<div class="mdc-container">
				<?php the_post_thumbnail( 'mdc-hero', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async' ) ); ?>
				<?php if ( $legenda ) : ?><p class="mdc-article__caption"><?php echo esc_html( $legenda ); ?></p><?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( function_exists( 'mdc_render_ad' ) ) : ?><?php mdc_render_ad( 'post-inicio' ); ?><?php endif; ?>

	<?php if ( $patrocinado ) : ?>
		<section class="mdc-sponsored" aria-label="Conteúdo patrocinado">
			<div class="mdc-container mdc-sponsored__inner">
				<div class="mdc-sponsored__copy">
					<span class="mdc-sponsored__label">Conteúdo patrocinado</span>
					<?php if ( $patrocinador ) : ?><strong>Patrocinado por <?php echo esc_html( $patrocinador ); ?></strong><?php endif; ?>
				</div>
				<?php if ( $patrocinador_logo ) : ?><div class="mdc-sponsored__brand"><?php echo wp_get_attachment_image( $patrocinador_logo, 'medium', false, array( 'class' => 'mdc-sponsored__logo', 'alt' => $patrocinador ) ); ?></div><?php endif; ?>
				<?php if ( $patrocinador_url ) : ?><a class="mdc-sponsored__link" href="<?php echo esc_url( $patrocinador_url ); ?>" target="_blank" rel="sponsored noopener noreferrer">Saiba mais <span aria-hidden="true">→</span></a><?php endif; ?>
			</div>
		</section>
	<?php endif; ?>

	<div class="mdc-container mdc-article__layout <?php echo $indice ? 'mdc-article__layout--has-toc' : 'mdc-article__layout--no-toc'; ?>">
		<?php if ( $indice ) : ?>
			<aside class="mdc-toc" aria-label="Índice da matéria">
				<div class="mdc-toc__inner">
					<span class="mdc-sidebar-kicker">NESTA MATÉRIA</span>
					<strong>Índice</strong>
					<ol>
						<?php foreach ( $indice as $item ) : ?><li class="mdc-toc__level-<?php echo esc_attr( $item['nivel'] ); ?>"><a href="#<?php echo esc_attr( $item['id'] ); ?>"><?php echo esc_html( $item['texto'] ); ?></a></li><?php endforeach; ?>
					</ol>
				</div>
			</aside>
		<?php endif; ?>

		<main class="mdc-article__content">
			<div class="mdc-prose"><?php the_content(); ?></div>

			<?php if ( $em_atualizacao && function_exists( 'mdc_render_atualizacao' ) ) : ?><?php echo mdc_render_atualizacao( $post_id ); ?><?php endif; ?>

			<?php if ( $gallery ) : ?>
				<section class="mdc-media-section mdc-article-media"><div class="mdc-section-heading mdc-section-heading--compact"><div><span class="mdc-section-kicker">MÍDIA</span><h2>Galeria de imagens</h2></div></div><div class="mdc-gallery" data-mdc-gallery><?php foreach ( $gallery as $gallery_id ) : $full = wp_get_attachment_image_url( $gallery_id, 'full' ); if ( $full ) : ?><a class="mdc-gallery__item" href="<?php echo esc_url( $full ); ?>" data-mdc-lightbox aria-label="Ampliar imagem"><?php echo wp_get_attachment_image( $gallery_id, 'mdc-card', false, array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) ); ?></a><?php endif; endforeach; ?></div></section>
			<?php endif; ?>

			<?php if ( $videos ) : ?>
				<section class="mdc-media-section mdc-article-media"><div class="mdc-section-heading mdc-section-heading--compact"><div><span class="mdc-section-kicker">MÍDIA</span><h2>Vídeos</h2></div></div><div class="mdc-video-grid"><?php foreach ( $videos as $url ) : $embed = function_exists( 'mdc_video_embed' ) ? mdc_video_embed( $url ) : wp_oembed_get( esc_url_raw( $url ), array( 'width' => 900 ) ); ?><div class="mdc-video-card"><?php if ( $embed ) : ?><?php echo $embed; ?><?php else : ?><a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer">Assistir vídeo <span aria-hidden="true">→</span></a><?php endif; ?></div><?php endforeach; ?></div></section>
			<?php endif; ?>

			<?php if ( $audio_url ) : ?>
				<section class="mdc-media-section mdc-article-media"><div class="mdc-section-heading mdc-section-heading--compact"><div><span class="mdc-section-kicker">MÍDIA</span><h2>Áudio</h2></div></div><div class="mdc-audio-player"><?php echo function_exists( 'mdc_audio_embed' ) ? mdc_audio_embed( $audio_url ) : wp_audio_shortcode( array( 'src' => esc_url( $audio_url ) ) ); ?></div></section>
			<?php endif; ?>

			<?php if ( function_exists( 'mdc_links_compartilhamento' ) ) : $shares = mdc_links_compartilhamento( $post_id ); ?>
				<nav class="mdc-share" aria-label="Compartilhar matéria">
					<span class="mdc-share__label">Compartilhe</span>
					<div class="mdc-share__links">
						<?php foreach ( $shares as $key => $share ) : ?>
							<a href="<?php echo esc_url( $share['url'] ); ?>" target="<?php echo 'email' === $key ? '_self' : '_blank'; ?>" rel="<?php echo 'email' === $key ? '' : 'noopener noreferrer'; ?>" data-share-icon="<?php echo esc_attr( $key ); ?>" aria-label="Compartilhar no <?php echo esc_attr( $share['rotulo'] ); ?>" title="<?php echo esc_attr( $share['rotulo'] ); ?>">
								<?php if ( 'facebook' === $key ) : ?><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h3V4h-3c-3.1 0-5 1.9-5 5v3H6v4h3v4h4v-4h3l1-4h-4V9c0-.7.3-1 1-1Z"/></svg>
								<?php elseif ( 'x' === $key ) : ?><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4l14 16M19 4L5 20"/></svg>
								<?php elseif ( 'whatsapp' === $key ) : ?><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 11.5a8 8 0 0 1-11.8 7L4 20l1.5-4A8 8 0 1 1 20 11.5Z"/><path d="M8.5 8.5c.2-.4.5-.4.8-.4l.7 1.7c.1.3 0 .5-.2.7l-.5.5c.7 1.1 1.6 1.8 2.8 2.3l.5-.6c.2-.2.4-.3.7-.2l1.6.7c.3.1.4.4.3.7-.2.8-.9 1.4-1.7 1.4-2.1-.2-4.8-2.6-5.7-4.4-.4-.8-.3-1.6.7-2.4Z"/></svg>
								<?php elseif ( 'linkedin' === $key ) : ?><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="5" y="9" width="4" height="10" rx=".5" fill="currentColor" stroke="none"/><circle cx="7" cy="6" r="2" fill="currentColor" stroke="none"/><path d="M13 19V9m0 3c.7-2 5-2.7 5 1.5V19"/></svg>
								<?php else : ?><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg><?php endif; ?>
								<span class="screen-reader-text"><?php echo esc_html( $share['rotulo'] ); ?></span>
							</a>
						<?php endforeach; ?>
						<button type="button" class="mdc-share__copy" data-share-copy data-share-url="<?php echo esc_attr( get_permalink( $post_id ) ); ?>" aria-label="Copiar link da matéria" title="Copiar link"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="9" y="9" width="11" height="11" rx="2"/><path d="M15 9V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h3"/></svg><span class="screen-reader-text">Copiar link</span></button>
					</div>
				</nav>
			<?php endif; ?>

			<?php if ( $tags ) : ?><section class="mdc-article-tags" aria-label="Etiquetas"><strong>Etiquetas</strong><div><?php foreach ( $tags as $tag ) : ?><a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>"><?php echo esc_html( $tag->name ); ?></a><?php endforeach; ?></div></section><?php endif; ?>

			<?php if ( function_exists( 'mdc_render_leia_mais' ) ) : ?>
				<?php echo mdc_render_leia_mais( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
			<?php endif; ?>

			<?php if ( function_exists( 'mdc_render_ad' ) ) : ?><?php mdc_render_ad( 'post-final' ); ?><?php endif; ?>

			<div class="mdc-article__after"><span>Fim da matéria</span><span aria-hidden="true">✦</span></div>
		</main>

		<aside class="mdc-article-sidebar" aria-label="Conteúdo complementar">
			<?php if ( function_exists( 'mdc_render_ad' ) ) : ?><?php mdc_render_ad( 'sidebar' ); ?><?php endif; ?>
			<?php if ( $most_read_ids ) : ?>
				<section class="mdc-sidebar-card">
					<span class="mdc-sidebar-kicker">MAIS LIDAS</span>
					<h2>O que está despertando interesse</h2>
					<ol class="mdc-sidebar-ranked">
						<?php foreach ( $most_read_ids as $index => $read_id ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( $read_id ) ); ?>"><span class="mdc-sidebar-ranked__number"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><span class="mdc-sidebar-ranked__copy"><strong><?php echo esc_html( get_the_title( $read_id ) ); ?></strong><small><?php echo esc_html( get_the_date( 'j/m/Y', $read_id ) ); ?></small></span></a></li>
						<?php endforeach; ?>
					</ol>
				</section>
			<?php endif; ?>

			<section class="mdc-sidebar-card mdc-sidebar-card--explore">
				<span class="mdc-sidebar-kicker">EXPLORE</span>
				<h2>Continue navegando</h2>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/copas/' ) ); ?>">Copas do Mundo <span>→</span></a></li>
					<li><a href="<?php echo esc_url( home_url( '/selecoes/' ) ); ?>">Seleções <span>→</span></a></li>
					<li><a href="<?php echo esc_url( home_url( '/jogadores/' ) ); ?>">Jogadores <span>→</span></a></li>
					<li><a href="<?php echo esc_url( home_url( '/estadios/' ) ); ?>">Estádios <span>→</span></a></li>
				</ul>
			</section>
		</aside>
	</div>

	<?php if ( comments_open() || get_comments_number() ) : ?>
		<section class="mdc-comments" id="comentarios"><div class="mdc-comments__heading"><span class="mdc-section-kicker">CONVERSA</span><h2>Comentários</h2></div><?php comments_template(); ?></section>
	<?php endif; ?>

	<?php if ( $related_ids ) : ?>
		<section class="mdc-article-related"><div class="mdc-section-heading mdc-section-heading--compact"><div><span class="mdc-section-kicker">JORNALISMO</span><h2>Posts relacionados</h2></div></div><div class="mdc-related-posts-grid"><?php foreach ( $related_ids as $related_id ) : ?><article class="mdc-related-post"><a class="mdc-related-post__image" href="<?php echo esc_url( get_permalink( $related_id ) ); ?>" aria-label="<?php echo esc_attr( get_the_title( $related_id ) ); ?>"><?php if ( has_post_thumbnail( $related_id ) ) : ?><?php echo get_the_post_thumbnail( $related_id, 'mdc-card', array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) ); ?><?php else : ?><span class="mdc-card__placeholder"><span aria-hidden="true">⚽</span></span><?php endif; ?></a><div class="mdc-related-post__body"><h3><a href="<?php echo esc_url( get_permalink( $related_id ) ); ?>"><?php echo esc_html( get_the_title( $related_id ) ); ?></a></h3><time datetime="<?php echo esc_attr( get_the_date( 'c', $related_id ) ); ?>"><?php echo esc_html( mdc_data( $related_id ) ); ?></time></div></article><?php endforeach; ?></div></section>
	<?php endif; ?>
</article>
<?php endwhile; ?>
<?php get_footer(); ?>
