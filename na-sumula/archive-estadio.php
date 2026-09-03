<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<section class="mdc-archive-hero">
    <div class="mdc-container">

<span class="mdc-section-kicker"><?php echo esc_html('PALCOS'); ?></span>
        <h1><?php echo esc_html('Estádios'); ?></h1>
        <p><?php echo esc_html('Os lugares onde partidas, finais e momentos inesquecíveis aconteceram.'); ?></p>
        
    </div>
</section>

<section class="mdc-section mdc-section--archive-results">
    <div class="mdc-container mdc-archive-layout">
        <div class="mdc-archive-main">
        <?php
        global $wp_query;
        $mdc_paged = max(1, (int)(get_query_var('paged') ? get_query_var('paged') : get_query_var('page')));
        $mdc_archive_query = new WP_Query(array_merge(
            $wp_query->query_vars,
            array(
                'posts_per_page' => 12,
                'paged' => $mdc_paged,
            )
        ));
        ?>
        <?php if ($mdc_archive_query->have_posts()) : ?>
            <div class="mdc-entity-grid">
                <?php while ($mdc_archive_query->have_posts()) : $mdc_archive_query->the_post(); ?>
                    <?php get_template_part('template-parts/card-entity', null, array('id' => get_the_ID())); ?>
                <?php endwhile; ?>
            </div>
            <div class="mdc-pagination">
                <?php
                $mdc_query_backup = $wp_query;
                $wp_query = $mdc_archive_query;
                the_posts_pagination(array(
                    'mid_size' => 2,
                    'prev_text' => '←',
                    'next_text' => '→',
                    'add_args' => array('genero' => isset($mdc_genero_atual) ? $mdc_genero_atual : null),
                ));
                $wp_query = $mdc_query_backup;
                wp_reset_postdata();
                ?>
            </div>
        <?php else : ?>
            <div class="mdc-empty-state">
                <strong>Nenhum registro encontrado.</strong>
                <span>Não encontramos itens para este filtro.</span>
            </div>
        <?php endif; ?>
        </div>
        <?php get_sidebar(); ?>
    </div>
</section>
<?php get_footer(); ?>
