<?php
/**
 * Ficha universal das entidades do Mundo da Copa.
 *
 * Regra de imagens:
 * - imagem destacada do WordPress = fundo do Hero;
 * - campo mdc_capa = imagem específica da moldura do Hero.
 */
$post_id  = get_the_ID();
$type     = get_post_type( $post_id );
$label    = function_exists( 'mdc_entity_label' ) ? mdc_entity_label( $post_id ) : 'Conteúdo';
$featured = get_post_thumbnail_id( $post_id );
$frame_id = absint( mdc_field( 'capa', $post_id ) );
$rows     = function_exists( 'mdc_ficha_linhas' ) ? mdc_ficha_linhas( $post_id ) : array();
$stats    = function_exists( 'mdc_ficha_stats' ) ? mdc_ficha_stats( $post_id ) : array();
$gallery  = function_exists( 'mdc_galeria_ids' ) ? mdc_galeria_ids( $post_id ) : array();
$videos   = preg_split( '/\r\n|\r|\n/', (string) mdc_field( 'videos', $post_id ) );
$videos   = array_values( array_filter( array_map( 'trim', $videos ) ) );

$year = ( 'copa' === $type ) ? mdc_field( 'ano', $post_id ) : '';
$hero_style = $featured ? ' style="--mdc-hero-image:url(' . esc_url( wp_get_attachment_image_url( $featured, 'mdc-hero-wide' ) ) . ')"' : '';

$text_sections = array();
$relations     = array();

switch ( $type ) {
	case 'copa':
		$text_sections = array(
			'A edição'             => mdc_field( 'historia', $post_id ),
			'Campanha do campeão'  => mdc_field( 'campanha', $post_id ),
			'A final'              => mdc_field( 'final', $post_id ),
			'Curiosidades'         => mdc_field( 'curiosidades', $post_id ),
		);
		$relations = array(
			array( 'title' => 'Seleções participantes', 'ids' => mdc_field_ids( 'selecoes_participantes', $post_id ) ),
			array( 'title' => 'Jogadores em destaque', 'ids' => mdc_field_ids( 'jogadores_destaque', $post_id ) ),
			array( 'title' => 'Estádios', 'ids' => mdc_field_ids( 'estadios', $post_id ) ),
		);
		break;
	case 'selecao':
		$text_sections = array(
			'História'           => mdc_field( 'historia', $post_id ),
			'Principais títulos' => mdc_field( 'principais_titulos', $post_id ),
		);
		$relations = array(
			array( 'title' => 'Copas disputadas', 'ids' => mdc_field_ids( 'copas', $post_id ) ),
			array( 'title' => 'Jogadores de destaque', 'ids' => mdc_field_ids( 'jogadores_destaque', $post_id ) ),
		);
		break;
	case 'jogador':
		$text_sections = array(
			'Biografia'          => mdc_field( 'biografia', $post_id ),
			'Principais momentos'=> mdc_field( 'momentos', $post_id ),
			'Curiosidades'       => mdc_field( 'curiosidades', $post_id ),
		);
		$relations = array(
			array( 'title' => 'Copas relacionadas', 'ids' => mdc_field_ids( 'copas_relacionadas', $post_id ) ),
		);
		break;
	case 'estadio':
		$text_sections = array(
			'Sobre o estádio' => mdc_field( 'descricao', $post_id ),
			'Curiosidades'    => mdc_field( 'curiosidades', $post_id ),
		);
		$relations = array(
			array( 'title' => 'Copas relacionadas', 'ids' => mdc_field_ids( 'copas', $post_id ) ),
		);
		break;
	case 'entidade':
		$text_sections = array( 'História' => mdc_field( 'historia', $post_id ) );
		break;
}

$relations = array_values( array_filter( $relations, static function( $group ) { return ! empty( $group['ids'] ); } ) );
$related_posts = mdc_field_ids( 'reportagens_relacionadas', $post_id );
?>

<article <?php post_class( 'mdc-entity-single mdc-entity-single--' . sanitize_html_class( $type ) ); ?>>
	<header class="mdc-entity-hero"<?php echo $hero_style; ?>>
		<div class="mdc-entity-hero__backdrop" aria-hidden="true"></div>
		<div class="mdc-container mdc-entity-hero__inner">
			<div class="mdc-entity-hero__copy">
				<div class="mdc-breadcrumbs"><?php if ( function_exists( 'mdc_breadcrumb' ) ) { mdc_breadcrumb(); } ?></div>
				<?php
				$tax_name = 'categoria_' . $type;
				$terms = taxonomy_exists( $tax_name ) ? get_the_terms( $post_id, $tax_name ) : array();
				if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) :
					?><span class="mdc-entity-hero__taxonomy"><?php echo esc_html( $terms[0]->name ); ?></span><?php
				endif;
				?>
				<span class="mdc-section-kicker"><?php echo esc_html( $label ); ?></span>
				<h1><?php the_title(); ?><?php if ( $year ) : ?> <span><?php echo esc_html( $year ); ?></span><?php endif; ?></h1>
			</div>

			<div class="mdc-entity-hero__visual">
				<?php if ( $frame_id ) : ?>
					<?php echo wp_get_attachment_image( $frame_id, 'mdc-poster', false, array( 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async', 'alt' => get_the_title( $post_id ) ) ); ?>
				<?php else : ?>
					<div class="mdc-entity-hero__fallback" aria-hidden="true"><?php echo function_exists( 'mdc_icon' ) ? mdc_icon( 'trophy', 64 ) : '✦'; ?></div>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<?php if ( $stats ) : ?>
		<section class="mdc-statbar" aria-label="Principais números">
			<div class="mdc-container mdc-statbar__grid">
				<?php foreach ( $stats as $stat ) : ?>
					<div class="mdc-stat">
						<span class="mdc-stat__icon" aria-hidden="true"><?php echo function_exists( 'mdc_icon' ) ? mdc_icon( $stat['icone'], 18 ) : ''; ?></span>
						<strong><?php echo wp_kses_post( $stat['valor'] ); ?></strong>
						<span><?php echo esc_html( $stat['rotulo'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<div class="mdc-container mdc-entity-layout">
		<div class="mdc-entity-content">
			<?php if ( get_the_content() ) : ?>
				<section class="mdc-entity-lead mdc-editorial" style="margin-top:0;">
					<div class="mdc-section-heading mdc-section-heading--compact">
						<div><span class="mdc-section-kicker">CONTEXTO</span><h2>Conteúdo</h2></div>
					</div>
					<div class="mdc-prose"><?php the_content(); ?></div>
				</section>
			<?php endif; ?>

			<?php foreach ( $text_sections as $heading => $value ) : if ( ! trim( wp_strip_all_tags( (string) $value ) ) ) { continue; } ?>
				<section class="mdc-editorial mdc-entity-text-section">
					<div class="mdc-section-heading mdc-section-heading--compact"><div><span class="mdc-section-kicker">CONTEÚDO</span><h2><?php echo esc_html( $heading ); ?></h2></div></div>
					<div class="mdc-prose"><?php echo wp_kses_post( wpautop( $value ) ); ?></div>
				</section>
			<?php endforeach; ?>

			<?php foreach ( $relations as $group ) : ?>
				<section class="mdc-related-entities">
					<div class="mdc-section-heading mdc-section-heading--compact"><div><span class="mdc-section-kicker">ARQUIVO</span><h2><?php echo esc_html( $group['title'] ); ?></h2></div></div>
					<div class="mdc-entity-grid mdc-entity-grid--inline">
						<?php foreach ( $group['ids'] as $related_id ) : get_template_part( 'template-parts/card-entity', null, array( 'id' => $related_id ) ); endforeach; ?>
					</div>
				</section>
			<?php endforeach; ?>

			<?php if ( $gallery ) : ?>
				<section class="mdc-media-section">
					<div class="mdc-section-heading mdc-section-heading--compact"><div><span class="mdc-section-kicker">MÍDIA</span><h2>Galeria de imagens</h2></div></div>
					<div class="mdc-gallery" data-mdc-gallery>
						<?php foreach ( $gallery as $gallery_id ) : $full = wp_get_attachment_image_url( $gallery_id, 'full' ); ?>
							<a class="mdc-gallery__item" href="<?php echo esc_url( $full ); ?>" data-mdc-lightbox aria-label="Ampliar imagem">
								<?php echo wp_get_attachment_image( $gallery_id, 'mdc-card', false, array( 'loading' => 'lazy', 'decoding' => 'async' ) ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $videos ) : ?>
				<section class="mdc-media-section">
					<div class="mdc-section-heading mdc-section-heading--compact"><div><span class="mdc-section-kicker">MÍDIA</span><h2>Vídeos</h2></div></div>
					<div class="mdc-video-grid">
						<?php foreach ( $videos as $video_url ) : $embed = wp_oembed_get( esc_url_raw( $video_url ), array( 'width' => 900 ) ); ?>
							<div class="mdc-video-card">
								<?php if ( $embed ) : echo wp_kses_post( $embed ); else : ?><a href="<?php echo esc_url( $video_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo function_exists( 'mdc_icon' ) ? mdc_icon( 'youtube', 22 ) : ''; ?> Assistir vídeo</a><?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $related_posts ) : ?>
				<section class="mdc-related-entities">
					<div class="mdc-section-heading mdc-section-heading--compact"><div><span class="mdc-section-kicker">JORNALISMO</span><h2>Reportagens relacionadas</h2></div></div>
					<div class="mdc-post-grid mdc-post-grid--related">
						<?php foreach ( $related_posts as $related_id ) : get_template_part( 'template-parts/card-post', null, array( 'id' => $related_id ) ); endforeach; ?>
					</div>
				</section>
			<?php endif; ?>
		</div>

		<aside class="mdc-entity-sidebar">
			<?php if ( $rows ) : ?>
				<section class="mdc-info-card">
					<div class="mdc-section-heading mdc-section-heading--compact">
						<div><span class="mdc-section-kicker">FICHA</span><h2>Dados essenciais</h2></div>
					</div>
					<dl class="mdc-facts">
						<?php foreach ( $rows as $row ) : if ( empty( $row['valor'] ) ) { continue; } ?>
							<div class="mdc-fact">
								<dt><?php echo esc_html( $row['rotulo'] ); ?></dt>
								<dd><?php echo wp_kses_post( ! empty( $row['url'] ) ? '<a href="' . esc_url( $row['url'] ) . '">' . esc_html( $row['valor'] ) . '</a>' : esc_html( $row['valor'] ) ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				</section>
			<?php endif; ?>

			<div class="mdc-sidebar-card">
				<span class="mdc-section-kicker">NAVEGUE</span>
				<h2>Continue explorando</h2>
				<ul>
					<li><a href="<?php echo esc_url( home_url( '/copas/' ) ); ?>">Copas do Mundo <span>→</span></a></li>
					<li><a href="<?php echo esc_url( home_url( '/selecoes/' ) ); ?>">Seleções <span>→</span></a></li>
					<li><a href="<?php echo esc_url( home_url( '/jogadores/' ) ); ?>">Jogadores <span>→</span></a></li>
					<li><a href="<?php echo esc_url( home_url( '/estadios/' ) ); ?>">Estádios <span>→</span></a></li>
				</ul>
			</div>
		</aside>
	</div>
</article>
