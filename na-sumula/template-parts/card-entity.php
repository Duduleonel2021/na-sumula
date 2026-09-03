<?php
$card_id = isset( $args['id'] ) ? (int) $args['id'] : get_the_ID();
$type    = get_post_type( $card_id );
$label   = function_exists( 'mdc_entity_label' ) ? mdc_entity_label( $card_id ) : 'Conteúdo';

$image        = get_post_thumbnail_id( $card_id );
$image_size   = 'mdc-entity';
$card_modifier = '';

if ( 'copa' === $type ) {
    $image = absint( get_post_meta( $card_id, 'mdc_capa', true ) );
    if ( ! $image ) {
        $image = get_post_thumbnail_id( $card_id );
    }
    $image_size    = 'mdc-poster';
    $card_modifier = ' mdc-entity-card--poster';
}
?>
<article class="mdc-entity-card mdc-entity-card--<?php echo esc_attr( $type ); ?><?php echo esc_attr( $card_modifier ); ?>">
    <a class="mdc-entity-card__link" href="<?php echo esc_url( get_permalink( $card_id ) ); ?>">
        <div class="mdc-entity-card__media">
            <?php if ( $image ) : ?>
                <?php echo wp_get_attachment_image( $image, $image_size, false, array( 'loading' => 'lazy', 'decoding' => 'async', 'alt' => '' ) ); ?>
            <?php else : ?>
                <span class="mdc-entity-card__placeholder" aria-hidden="true">✦</span>
            <?php endif; ?>
        </div>

        <div class="mdc-entity-card__body">
            <?php if ( 'copa' === $type ) : ?>
                <?php
                $ano_copa = absint( mdc_field( 'mdc_ano', $card_id ) );
                if ( ! $ano_copa && preg_match( '/(19|20)\d{2}/', get_the_title( $card_id ), $matches ) ) {
                    $ano_copa = absint( $matches[0] );
                }
                $sede_copa = function_exists( 'mdc_copa_sede' ) ? mdc_copa_sede( $card_id ) : '';
                ?>
                <?php if ( $sede_copa ) : ?>
                    <span class="mdc-entity-card__country"><?php echo esc_html( $sede_copa ); ?></span>
                <?php endif; ?>
                <h3 class="mdc-entity-card__copa-title">
                    Copa do Mundo<?php echo $ano_copa ? ' — ' . esc_html( $ano_copa ) : ''; ?>
                </h3>
            <?php else : ?>
                <span class="mdc-kicker"><?php echo esc_html( $label ); ?></span>
                <h3><?php echo esc_html( get_the_title( $card_id ) ); ?></h3>
                <?php
                $sub = '';
                if ( 'selecao' === $type ) {
                    $sub = mdc_term_name( 'pais', $card_id );
                } elseif ( 'jogador' === $type ) {
                    $sub = mdc_term_name( 'posicao_jogador', $card_id );
                } elseif ( 'estadio' === $type ) {
                    $sub = mdc_field( 'mdc_cidade', $card_id );
                }
                if ( $sub ) :
                    ?>
                    <span class="mdc-entity-card__sub"><?php echo esc_html( $sub ); ?></span>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </a>
</article>
