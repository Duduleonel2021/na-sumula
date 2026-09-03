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
$info = isset( $labels[ $post_type ] ) ? $labels[ $post_type ] : array( 'kicker' => 'ARQUIVO', 'title' => wp_strip_all_tags( get_the_archive_title() ), 'desc' => wp_strip_all_tags( get_the_archive_description() ) );
?>
<section class="mdc-archive-hero">
	<div class="mdc-container">
		<span class="mdc-section-kicker"><?php echo esc_html( $info['kicker'] ); ?></span>
		<h1><?php echo esc_html( $info['title'] ); ?></h1>
		<?php if ( $info['desc'] ) : ?><p><?php echo esc_html( wp_strip_all_tags( $info['desc'] ) ); ?></p><?php endif; ?>
	</div>
</section>

<section class="mdc-section mdc-section--archive-results">
	<div class="mdc-container">
		<?php if ( have_posts() ) : ?>
			<div class="mdc-entity-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'template-parts/card-entity', null, array( 'id' => get_the_ID() ) ); ?>
				<?php endwhile; ?>
			</div>
			<div class="mdc-pagination"><?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '←', 'next_text' => '→' ) ); ?></div>
		<?php else : ?>
			<div class="mdc-empty-state"><strong>Nenhum registro encontrado.</strong><span>Cadastre os primeiros itens para começar a construir o arquivo.</span></div>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>
