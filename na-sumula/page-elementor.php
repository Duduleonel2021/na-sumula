<?php get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>
<article class="mdc-page">
	<header class="mdc-page__hero">
		<div class="mdc-container">
			<span class="mdc-section-kicker">NA SÚMULA</span>
			<h1><?php the_title(); ?></h1>
			<?php if ( has_excerpt() ) : ?><p><?php the_excerpt(); ?></p><?php endif; ?>
		</div>
	</header>
	<div class="mdc-container mdc-page__content">
		<div class="mdc-prose"><?php the_content(); ?></div>
	</div>
</article>
<?php endwhile; ?>
<?php get_footer(); ?>
