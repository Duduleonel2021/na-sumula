<?php
/**
 * Footer — Na Súmula.
 *
 * @package na-sumula
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$footer_columns  = absint( mdc_config( 'mdc_footer_colunas' ) );
$footer_columns  = max( 1, min( 4, $footer_columns ) );
$footer_texto    = mdc_config( 'mdc_footer_texto' );
$footer_creditos = mdc_config( 'mdc_footer_creditos' );

/*
 * Logo do rodapé.
 *
 * O rodapé é sempre escuro, então priorizamos o logo "modo escuro"
 * (mdc_logo_escuro) definido em Aparência > Marca. Se nenhum logo
 * tiver sido enviado no painel, caímos para o arquivo estático do
 * tema como último recurso, para o rodapé nunca ficar sem marca.
 */
$footer_logo_id  = absint( mdc_config( 'mdc_logo_escuro' ) );
$footer_logo_url = get_theme_file_uri( 'assets/images/logo-dark.svg' );
$footer_logo_png = get_theme_file_uri( 'assets/images/logo-dark.png' );
?>

</main>

<?php if ( function_exists( 'mdc_render_ad' ) ) : ?>
	<?php mdc_render_ad( 'antes-footer' ); ?>
<?php endif; ?>

<footer class="mdc-footer mdc-footer--columns-<?php echo esc_attr( $footer_columns ); ?>">
	<div class="mdc-container">
		<div class="mdc-footer__grid" style="--mdc-footer-columns: <?php echo esc_attr( $footer_columns ); ?>;">

			<div class="mdc-footer__brand">
				<a class="mdc-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					<?php if ( $footer_logo_id ) : ?>
						<?php
						echo wp_get_attachment_image(
							$footer_logo_id,
							'full',
							false,
							array(
								'class'    => 'mdc-footer__logo-image',
								'alt'      => get_bloginfo( 'name' ),
								'loading'  => 'lazy',
								'decoding' => 'async',
							)
						);
						?>
					<?php else : ?>
						<img
							class="mdc-footer__logo-image"
							src="<?php echo esc_url( $footer_logo_url ); ?>"
							data-fallback="<?php echo esc_url( $footer_logo_png ); ?>"
							alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
							width="260"
							height="72"
							loading="lazy"
							decoding="async"
							onerror="if(this.src!==this.dataset.fallback){this.src=this.dataset.fallback;}else{this.style.display='none';}"
						>
					<?php endif; ?>
				</a>

				<?php if ( $footer_texto ) : ?>
					<p><?php echo esc_html( $footer_texto ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( $footer_columns >= 2 ) : ?>
				<div class="mdc-footer__nav">
					<h2>Explore</h2>
					<ul class="mdc-footer__fixed-links">
						<li><a href="<?php echo esc_url( mdc_anuncio_page_url() ); ?>">Anuncie aqui</a></li>
					</ul>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => has_nav_menu( 'rodape_explore' ) ? 'rodape_explore' : 'rodape',
							'container'      => false,
							'fallback_cb'    => false,
							'menu_class'     => 'mdc-footer__menu',
						)
					);
					?>
				</div>
			<?php endif; ?>

			<?php if ( $footer_columns >= 3 ) : ?>
				<div class="mdc-footer__nav">
					<h2>Arquivo</h2>
					<?php if ( has_nav_menu( 'rodape_arquivo' ) ) : ?>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'rodape_arquivo',
								'container'      => false,
								'fallback_cb'    => false,
								'menu_class'     => 'mdc-footer__menu',
							)
						);
						?>
					<?php else : ?>
						<ul class="mdc-footer__menu">
							<?php
							foreach (
								array(
									'copa'    => 'Copas do Mundo',
									'selecao' => 'Seleções',
									'jogador' => 'Jogadores',
									'estadio' => 'Estádios',
								) as $tipo => $rotulo
							) :
								$link = post_type_exists( $tipo ) ? get_post_type_archive_link( $tipo ) : '';
								if ( $link ) :
									?>
									<li><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $rotulo ); ?></a></li>
									<?php
								endif;
							endforeach;
							?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $footer_columns >= 4 ) : ?>
				<div class="mdc-footer__nav mdc-footer__nav--institutional">
					<h2>Institucional</h2>
					<?php if ( has_nav_menu( 'rodape_institucional' ) ) : ?>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'rodape_institucional',
								'container'      => false,
								'fallback_cb'    => false,
								'menu_class'     => 'mdc-footer__menu',
							)
						);
						?>
					<?php else : ?>
						<ul class="mdc-footer__menu">
							<li><a href="<?php echo esc_url( mdc_anuncio_page_url() ); ?>">Anuncie aqui</a></li>
							<li><a href="<?php echo esc_url( home_url( '/politica-de-privacidade/' ) ); ?>">Política de privacidade</a></li>
							<li><a href="<?php echo esc_url( home_url( '/sobre-o-mundo-da-copa/' ) ); ?>">Sobre o portal</a></li>
						</ul>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>

		<div class="mdc-footer__bottom">
			<span>© <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></span>
			<?php if ( $footer_creditos ) : ?>
				<span><?php echo esc_html( $footer_creditos ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>

</body>
</html>
