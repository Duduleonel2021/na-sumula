<?php
/**
 * Sidebar — Na Súmula.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<aside class="mdc-archive-sidebar" aria-label="Barra lateral">
    <?php if ( is_active_sidebar( 'mdc-lateral' ) ) : ?>
        <?php dynamic_sidebar( 'mdc-lateral' ); ?>
    <?php else : ?>
        <section class="mdc-sidebar-card">
            <span class="mdc-section-kicker">NAVEGUE</span>
            <h2>Explore o arquivo</h2>
            <nav aria-label="Arquivo">
                <?php
                $links = array(
                    'copa'    => 'Copas do Mundo',
                    'selecao' => 'Seleções',
                    'jogador' => 'Jogadores',
                    'estadio' => 'Estádios',
                );
                foreach ( $links as $type => $label ) :
                    $url = post_type_exists( $type ) ? get_post_type_archive_link( $type ) : '';
                    if ( $url ) :
                ?>
                    <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?><span>→</span></a>
                <?php
                    endif;
                endforeach;
                ?>
            </nav>
        </section>
    <?php endif; ?>
</aside>
