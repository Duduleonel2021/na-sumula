<?php get_header(); ?>
<?php
$object = get_queried_object();
$post_type = isset( $object->name ) ? $object->name : get_post_type();
$labels = array(
	'copa' => array( 'kicker' => 'ARQUIVO', 'title' => 'Copas do Mundo', 'desc' => 'Todas as edições, da primeira bola em 1930 aos capítulos mais recentes.' ),
	'selecao' => array( 'kicker' => 'GUIA', 'title' => 'Seleções', 'desc' => 'História, títulos, campanhas e personagens das seleções que disputaram a Copa.' ),
	'jogador' => array( 'kicker' => 'PERSONAGENS', 'title' => 'Jogadores', 'desc' => 'Os craques e personagens que ajudaram a escrever a história dos Mundiais.' ),
	'estadio' => array( 'kicker' => 'PALCOS', 'title' => 'Estádios', 'desc' => 'Os lugares onde partidas, finais e momentos inesquecíveis aconteceram.' ),
	'entidade' => array( 'kicker' => 'ESTRUTURA', 'title' => 'Entidades', 'desc' => 'FIFA, confederações continentais e entidades nacionais que organizam o futebol.' ),
	'colunista' => array( 'kicker' => 'OPINIÃO', 'title' => 'Colunistas', 'desc' => 'Vozes que ajudam a olhar para o futebol por outros ângulos.' ),
);
if ( is_category() ) {
    $info = array(
        'kicker' => 'EDITORIA',
        'title'  => wp_strip_all_tags( single_cat_title( '', false ) ),
        'desc'   => wp_strip_all_tags( category_description() ),
    );
} elseif ( is_tag() ) {
    $info = array(
        'kicker' => 'ASSUNTO',
        'title'  => wp_strip_all_tags( single_tag_title( '', false ) ),
        'desc'   => wp_strip_all_tags( tag_description() ),
    );
} elseif ( isset( $labels[ $post_type ] ) ) {
    $info = $labels[ $post_type ];
} else {
    $info = array(
        'kicker' => 'ARQUIVO',
        'title'  => wp_strip_all_tags( get_the_archive_title() ),
        'desc'   => wp_strip_all_tags( get_the_archive_description() ),
    );
}
?>
<section class="mdc-archive-hero">
	<div class="mdc-container">
		<span class="mdc-section-kicker"><?php echo esc_html( $info['kicker'] ); ?></span>
		<h1><?php echo esc_html( $info['title'] ); ?></h1>
		<?php if ( $info['desc'] ) : ?><p><?php echo esc_html( wp_strip_all_tags( $info['desc'] ) ); ?></p><?php endif; ?>
	</div>
</section>

<section class="mdc-section mdc-section--archive-results">
	<div class="mdc-container mdc-archive-layout">
		<div class="mdc-entity-content">
			<?php
			global $wp_query;
			$mdc_paged        = max( 1, (int) ( get_query_var( 'paged' ) ? get_query_var( 'paged' ) : get_query_var( 'page' ) ) );
			$mdc_archive_query = new WP_Query( array_merge(
				$wp_query->query_vars,
				array(
					'posts_per_page' => 12,
					'paged'          => $mdc_paged,
				)
			) );
			?>
			<?php if ( $mdc_archive_query->have_posts() ) : ?>
				<div class="mdc-entity-grid">
					<?php while ( $mdc_archive_query->have_posts() ) : $mdc_archive_query->the_post(); ?>
						<?php get_template_part( 'template-parts/card-entity', null, array( 'id' => get_the_ID() ) ); ?>
					<?php endwhile; ?>
				</div>
				<div class="mdc-pagination">
					<?php
					$mdc_query_backup = $wp_query;
					$wp_query         = $mdc_archive_query;
					the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '←', 'next_text' => '→' ) );
					$wp_query         = $mdc_query_backup;
					?>
				</div>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<div class="mdc-empty-state"><strong>Nenhum registro encontrado.</strong><span>Cadastre os primeiros itens para começar a construir o arquivo.</span></div>
			<?php endif; ?>
		</div>
		<?php get_sidebar(); ?>
	</div>
</section>
<?php get_footer(); ?>
