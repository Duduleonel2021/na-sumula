<?php
/** Ficha de Entidade. */
get_header();
while ( have_posts() ) : the_post();
	get_template_part( 'template-parts/single-entity' );
endwhile;
get_footer();
