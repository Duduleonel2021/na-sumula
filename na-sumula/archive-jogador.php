<?php
/**
 * Na Súmula — Arquivo editorial.
 *
 * 12 registros por página: 4 colunas x 3 linhas no desktop.
 */

get_header();

$mdc_genero_atual = isset( $_GET['genero'] )
    ? sanitize_key( wp_unslash( $_GET['genero'] ) )
    : 'todos';

if ( ! in_array( $mdc_genero_atual, array( 'todos', 'masculino', 'feminino' ), true ) ) {
    $mdc_genero_atual = 'todos';
}

$mdc_filtros = array(
    'todos'     => 'Todos',
    'masculino' => 'Masculino',
    'feminino'  => 'Feminino',
);

$mdc_post_type = 'jogador';
$mdc_titulo    = 'Jogadores';
$mdc_kicker    = 'PERSONAGENS';
$mdc_descricao = 'Os craques e personagens que ajudaram a escrever a história dos Mundiais.';

$mdc_arquivo_url = get_post_type_archive_link( $mdc_post_type );
?>

<section class="mdc-archive-hero">
    <div class="mdc-container">

<span class="mdc-section-kicker"><?php echo esc_html( $mdc_kicker ); ?></span>

        <h1><?php echo esc_html( $mdc_titulo ); ?></h1>

        <p><?php echo esc_html( $mdc_descricao ); ?></p>

        <nav class="mdc-archive-filter" aria-label="Filtrar por gênero">
            <span class="mdc-archive-filter__label">Filtrar por gênero</span>

            <div class="mdc-archive-filter__buttons">
                <?php foreach ( $mdc_filtros as $mdc_slug => $mdc_rotulo ) : ?>
                    <?php
                    $mdc_url = add_query_arg(
                        'genero',
                        $mdc_slug,
                        $mdc_arquivo_url
                    );
                    $mdc_ativo = $mdc_genero_atual === $mdc_slug;
                    ?>
                    <a
                        class="<?php echo $mdc_ativo ? 'is-current' : ''; ?>"
                        href="<?php echo esc_url( $mdc_url ); ?>"
                        <?php echo $mdc_ativo ? 'aria-current="page"' : ''; ?>
                    >
                        <?php echo esc_html( $mdc_rotulo ); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </nav>

    </div>
</section>

<section class="mdc-section mdc-section--archive-results">
    <div class="mdc-container mdc-archive-layout">
        <div class="mdc-archive-main">
        <?php
        global $wp_query;

        $mdc_paged = max(
            1,
            (int) (
                get_query_var( 'paged' )
                    ? get_query_var( 'paged' )
                    : get_query_var( 'page' )
            )
        );

        $mdc_archive_args = array_merge(
            $wp_query->query_vars,
            array(
                'posts_per_page' => 12,
                'paged'          => $mdc_paged,
                'no_found_rows'  => false,
            )
        );

        $mdc_archive_query = new WP_Query( $mdc_archive_args );
        ?>

        <?php if ( $mdc_archive_query->have_posts() ) : ?>

            <div class="mdc-entity-grid">
                <?php while ( $mdc_archive_query->have_posts() ) : $mdc_archive_query->the_post(); ?>
                    <?php
                    get_template_part(
                        'template-parts/card-entity',
                        null,
                        array( 'id' => get_the_ID() )
                    );
                    ?>
                <?php endwhile; ?>
            </div>

            <div class="mdc-pagination">
                <?php
                $mdc_query_backup = $wp_query;
                $wp_query = $mdc_archive_query;

                the_posts_pagination(
                    array(
                        'mid_size'  => 1,
                        'prev_text' => '← Anterior',
                        'next_text' => 'Próxima →',
                        'add_args'  => array(
                            'genero' => $mdc_genero_atual,
                        ),
                    )
                );

                $wp_query = $mdc_query_backup;
                ?>
            </div>

            <?php wp_reset_postdata(); ?>

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
