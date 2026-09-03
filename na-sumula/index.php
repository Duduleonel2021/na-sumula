<?php get_header(); ?>
<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) : the_post(); ?>
		<article <?php post_class(); ?>>
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php if ( has_post_thumbnail() ) : the_post_thumbnail(); endif; ?>
			<?php the_excerpt(); ?>
		</article>
	<?php endwhile; ?>
	<?php the_posts_pagination(); ?>
<?php else : ?>
	<p><?php esc_html_e( 'Nenhum conteúdo encontrado.', 'mundo-da-copa' ); ?></p>
<?php endif; ?>
<?php get_footer(); ?>
