<?php
$card_id  = isset( $args['id'] ) ? (int) $args['id'] : get_the_ID();
$featured = ! empty( $args['featured'] );
$compact  = ! empty( $args['compact'] );
$tag      = get_the_category( $card_id );
$tag_name = ! empty( $tag ) ? wp_strip_all_tags( $tag[0]->name ) : 'Reportagem';
$published_date = function_exists( 'mdc_data' ) ? mdc_data( $card_id ) : get_the_date( 'j \d\e F \d\e Y', $card_id );
$em_atualizacao = function_exists( 'mdc_atualizacao_ativa' ) ? mdc_atualizacao_ativa( $card_id ) : ( '1' === (string) get_post_meta( $card_id, 'mdc_em_atualizacao', true ) );
$updated_date   = get_the_modified_date( 'j \d\e F \d\e Y', $card_id );
?>
<article class="mdc-card <?php echo $featured ? 'mdc-card--featured ' : ''; ?><?php echo $compact ? 'mdc-card--compact' : ''; ?>">
	<a class="mdc-card__media" href="<?php echo esc_url( get_permalink( $card_id ) ); ?>" aria-label="<?php echo esc_attr( get_the_title( $card_id ) ); ?>">
		<?php if ( has_post_thumbnail( $card_id ) ) : ?>
			<?php echo get_the_post_thumbnail( $card_id, $featured ? 'mdc-hero' : 'mdc-card', array( 'loading' => $featured ? 'eager' : 'lazy', 'decoding' => 'async', 'alt' => '' ) ); ?>
		<?php else : ?>
			<span class="mdc-card__placeholder"><span>⚽</span></span>
		<?php endif; ?>
		<span class="mdc-card__tag"><?php echo esc_html( $tag_name ); ?></span>
		<?php if ( $em_atualizacao ) : ?><span class="mdc-card__live"><?php echo mdc_icon( 'radio', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> AO VIVO</span><?php endif; ?>
	</a>

	<div class="mdc-card__body">
		<h2><a href="<?php echo esc_url( get_permalink( $card_id ) ); ?>"><?php echo esc_html( get_the_title( $card_id ) ); ?></a></h2>

		<?php if ( $featured || ( ! $compact && has_excerpt( $card_id ) ) ) : ?>
			<p><?php echo esc_html( wp_trim_words( get_the_excerpt( $card_id ), $featured ? 30 : 18 ) ); ?></p>
		<?php endif; ?>

		<div class="mdc-card__meta" aria-label="Informações da publicação">
			<span class="mdc-card__meta-item mdc-card__meta-item--date"><strong class="mdc-card__meta-label">Data</strong><time datetime="<?php echo esc_attr( get_the_date( 'c', $card_id ) ); ?>"><?php echo esc_html( $published_date ); ?></time></span>
			<span class="mdc-card__meta-separator" aria-hidden="true">•</span>
			<span class="mdc-card__meta-item mdc-card__meta-item--updated"><strong class="mdc-card__meta-label">Atualizado</strong><time datetime="<?php echo esc_attr( get_the_modified_date( 'c', $card_id ) ); ?>"><?php echo esc_html( $updated_date ); ?></time></span>
		</div>
	</div>
</article>
