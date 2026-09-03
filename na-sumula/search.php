<?php get_header(); ?>
<section class="mdc-search-hero">
	<div class="mdc-container">
		<span class="mdc-section-kicker">PESQUISA</span>
		<h1>Buscar na Na Súmula</h1>
		<form class="mdc-search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="mdc-search"><?php esc_html_e( 'Pesquisar', 'mundo-da-copa' ); ?></label>
			<input id="mdc-search" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Ex.: Pelé, 1970, Maracanã..." />
			<button type="submit">Buscar</button>
		</form>
	</div>
</section>
<section class="mdc-section">
	<div class="mdc-container">
		<?php if ( have_posts() ) : ?>
			<div class="mdc-search-results">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'template-parts/card-entity', null, array( 'id' => get_the_ID() ) ); ?>
				<?php endwhile; ?>
			</div>
			<div class="mdc-pagination"><?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '←', 'next_text' => '→' ) ); ?></div>
		<?php else : ?>
			<div class="mdc-empty-state"><strong>Nenhum resultado para “<?php echo esc_html( get_search_query() ); ?>”.</strong><span>Tente outro termo, nome de seleção, jogador, Copa ou estádio.</span></div>
		<?php endif; ?>
	</div>
</section>
<?php get_footer(); ?>
