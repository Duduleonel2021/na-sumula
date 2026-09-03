<?php get_header(); ?>
<section class="mdc-404">
	<div class="mdc-container">
		<span class="mdc-404__code">404</span>
		<span class="mdc-section-kicker">FORA DE CAMPO</span>
		<h1>Essa página saiu pela linha de fundo.</h1>
		<p>O endereço não existe ou mudou de lugar. Vamos voltar para onde a história continua.</p>
		<a class="mdc-button" href="<?php echo esc_url( home_url( '/' ) ); ?>">Voltar ao início</a>
	</div>
</section>
<?php get_footer(); ?>
