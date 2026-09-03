<?php
/**
 * Na Súmula — Header principal
 *
 * Estrutura:
 * 01. Barra institucional
 * 02. Marca + banner 728x90 + ações
 * 03. Navegação principal
 * 04. Busca expansível
 * 05. Menu lateral
 *
 * @package mundo-da-copa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$ns_home_url    = home_url( '/' );
$ns_account_url = is_user_logged_in() ? mdc_conta_url() : mdc_conta_login_url( home_url( '/' ) );
$ns_is_logged   = is_user_logged_in();
$ns_menu_items = array(
	'copa' => 'Copas', 'selecao' => 'Seleções', 'jogador' => 'Jogadores', 'estadio' => 'Estádios', 'entidade' => 'Entidades',
);
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class( 'ns-site' ); ?>>
<?php wp_body_open(); ?>

<a class="mdc-skip-link" href="#mdc-conteudo"><?php esc_html_e( 'Ir para o conteúdo', 'mundo-da-copa' ); ?></a>

<header class="ns-header" data-mdc-header>

	<?php if ( function_exists( 'mdc_config' ) && mdc_config( 'mdc_barra_topo' ) ) : ?>
		<div class="ns-topbar">
			<div class="mdc-container ns-topbar__inner">
				<div class="ns-topbar__date">
					<?php echo esc_html( ucfirst( wp_date( 'l, j \d\e F \d\e Y' ) ) ); ?>
				</div>

				<div class="ns-topbar__nav">
					<?php if ( has_nav_menu( 'topo' ) ) : ?>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'topo',
								'container'      => false,
								'menu_class'     => 'ns-topbar__menu',
								'depth'          => 1,
								'fallback_cb'    => false,
							)
						);
						?>
					<?php else : ?>
						<span class="ns-topbar__slogan"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></span>
					<?php endif; ?>
				</div>

				<?php $ns_socials = function_exists( 'mdc_redes_sociais' ) ? mdc_redes_sociais() : array(); ?>
				<?php if ( $ns_socials ) : ?>
					<ul class="ns-topbar__socials" aria-label="<?php esc_attr_e( 'Redes sociais', 'mundo-da-copa' ); ?>">
						<?php foreach ( $ns_socials as $ns_slug => $ns_social ) : ?>
							<li>
								<a href="<?php echo esc_url( $ns_social['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $ns_social['rotulo'] ); ?>">
									<?php echo mdc_icon( $ns_slug, 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="ns-header__brand">
		<div class="mdc-container ns-header__brand-inner">

			<a class="ns-header__logo" href="<?php echo esc_url( $ns_home_url ); ?>" rel="home" aria-label="<?php bloginfo( 'name' ); ?>">
				<?php
				$ns_logo_light_id = function_exists( 'mdc_config' ) ? absint( mdc_config( 'mdc_logo_claro' ) ) : 0;
				$ns_logo_dark_id  = function_exists( 'mdc_config' ) ? absint( mdc_config( 'mdc_logo_escuro' ) ) : 0;
				if ( $ns_logo_light_id ) {
					echo wp_get_attachment_image( $ns_logo_light_id, 'full', false, array( 'class' => 'ns-header__logo-image ns-header__logo-image--light', 'alt' => get_bloginfo( 'name' ) ) );
				} else {
					$ns_logo_light_url = get_theme_file_uri( 'assets/images/logo-light.png' );
					echo '<img class="ns-header__logo-image ns-header__logo-image--light" src="' . esc_url( $ns_logo_light_url ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
				}
				if ( $ns_logo_dark_id ) {
					echo wp_get_attachment_image( $ns_logo_dark_id, 'full', false, array( 'class' => 'ns-header__logo-image ns-header__logo-image--dark', 'alt' => get_bloginfo( 'name' ) ) );
				} else {
					$ns_logo_dark_url = get_theme_file_uri( 'assets/images/logo-dark.png' );
					echo '<img class="ns-header__logo-image ns-header__logo-image--dark" src="' . esc_url( $ns_logo_dark_url ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
				}
				?>
			</a>

			<div class="ns-header__ad" aria-label="<?php esc_attr_e( 'Publicidade', 'mundo-da-copa' ); ?>">
				<?php if ( function_exists( 'mdc_ad_enabled' ) && mdc_ad_enabled( 'topo' ) ) : ?>
					<?php mdc_render_ad( 'topo' ); ?>
				<?php else : ?>
					<span class="ns-header__ad-label">PUBLICIDADE</span>
					<div class="ns-header__ad-placeholder">
						<span>ESPAÇO PUBLICITÁRIO</span>
						<strong>728 × 90</strong>
					</div>
				<?php endif; ?>
			</div>

			<div class="ns-header__brand-actions">
				<button type="button" class="ns-header__action ns-header__search-toggle" data-mdc-search-toggle aria-expanded="false" aria-controls="mdc-search" aria-label="<?php esc_attr_e( 'Buscar', 'mundo-da-copa' ); ?>">
					<?php echo mdc_icon( 'search', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span>Buscar</span>
				</button>

				<?php if ( function_exists( 'mdc_config' ) && mdc_config( 'mdc_botao_tema' ) ) : ?>
					<button type="button" class="ns-header__action" data-mdc-theme-toggle aria-label="<?php esc_attr_e( 'Alternar tema claro e escuro', 'mundo-da-copa' ); ?>">
						<span class="ns-theme-sun"><?php echo mdc_icon( 'sun', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						<span class="ns-theme-moon"><?php echo mdc_icon( 'moon', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					</button>
				<?php endif; ?>

				<button type="button" class="ns-header__action ns-header__menu-toggle" data-mdc-menu-toggle aria-expanded="false" aria-controls="ns-side-panel" aria-label="<?php esc_attr_e( 'Abrir menu lateral', 'mundo-da-copa' ); ?>">
					<span class="ns-menu-open"><?php echo mdc_icon( 'menu', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="ns-menu-close"><?php echo mdc_icon( 'close', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</button>

				<a class="ns-header__account" href="<?php echo esc_url( $ns_account_url ); ?>">
					<?php echo mdc_icon( 'user', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<span><?php echo $ns_is_logged ? esc_html__( 'Meu perfil', 'mundo-da-copa' ) : esc_html__( 'Minha conta', 'mundo-da-copa' ); ?></span>
				</a>
			</div>

		</div>
	</div>

	<div class="ns-header__nav">
		<div class="mdc-container ns-header__nav-inner">
			<nav class="ns-mainnav" aria-label="<?php esc_attr_e( 'Menu principal', 'mundo-da-copa' ); ?>">
				<?php
				if ( function_exists( 'mdc_header_menu_principal' ) ) {
					mdc_header_menu_principal();
				} elseif ( has_nav_menu( 'principal' ) ) {
					wp_nav_menu(
						array(
							'theme_location' => 'principal',
							'container'      => false,
							'menu_class'     => 'mdc-nav__list',
							'depth'          => 2,
							'fallback_cb'    => false,
						)
					);
				} else {
					?>
					<ul class="mdc-nav__list">
						<li><a href="<?php echo esc_url( $ns_home_url ); ?>">Início</a></li>
						<?php
						$ns_menu_items = array(
							'copa'         => 'Copas',
							'selecao'      => 'Seleções',
							'jogador'      => 'Jogadores',
							'estadio'      => 'Estádios',
							'entidade'     => 'Entidades',
						);
						foreach ( $ns_menu_items as $ns_type => $ns_label ) :
							$ns_link = get_post_type_archive_link( $ns_type );
							if ( $ns_link ) :
								?>
								<li><a href="<?php echo esc_url( $ns_link ); ?>"><?php echo esc_html( $ns_label ); ?></a></li>
								<?php
							endif;
						endforeach;
						?>
					</ul>
					<?php
				}
				?>
			</nav>

		</div>
	</div>

	<div class="ns-search-panel" id="mdc-search" hidden>
		<div class="mdc-container">
			<form role="search" method="get" class="ns-search-form" action="<?php echo esc_url( $ns_home_url ); ?>">
				<label for="ns-search-field"><?php esc_html_e( 'Pesquisar no Na Súmula', 'mundo-da-copa' ); ?></label>
				<div class="ns-search-field">
					<?php echo mdc_icon( 'search', 19 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					<input type="search" id="ns-search-field" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Busque por notícias, histórias, jogadores, Copas…', 'mundo-da-copa' ); ?>">
					<button type="submit">Buscar</button>
				</div>
			</form>
		</div>
	</div>

</header>

<div class="ns-side-overlay" data-mdc-menu-close aria-hidden="true"></div>

<aside id="ns-side-panel" class="ns-side-panel" aria-hidden="true" aria-label="<?php esc_attr_e( 'Menu lateral', 'mundo-da-copa' ); ?>">
	<div class="ns-side-panel__head">
		<a class="ns-side-panel__logo" href="<?php echo esc_url( $ns_home_url ); ?>" rel="home">
			<?php
			$ns_menu_logo_light_id = function_exists( 'mdc_config' ) ? absint( mdc_config( 'mdc_logo_claro' ) ) : 0;
			$ns_menu_logo_dark_id  = function_exists( 'mdc_config' ) ? absint( mdc_config( 'mdc_logo_escuro' ) ) : 0;

			if ( $ns_menu_logo_light_id ) {
				echo wp_get_attachment_image( $ns_menu_logo_light_id, 'full', false, array( 'class' => 'ns-side-panel__logo-image ns-side-panel__logo-image--light', 'alt' => get_bloginfo( 'name' ) ) );
			} else {
				$ns_menu_logo_light = get_theme_file_uri( 'assets/images/logo-light.png' );
				echo '<img class="ns-side-panel__logo-image ns-side-panel__logo-image--light" src="' . esc_url( $ns_menu_logo_light ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
			}

			if ( $ns_menu_logo_dark_id ) {
				echo wp_get_attachment_image( $ns_menu_logo_dark_id, 'full', false, array( 'class' => 'ns-side-panel__logo-image ns-side-panel__logo-image--dark', 'alt' => '' ) );
			} else {
				$ns_menu_logo_dark = get_theme_file_uri( 'assets/images/logo-dark.png' );
				echo '<img class="ns-side-panel__logo-image ns-side-panel__logo-image--dark" src="' . esc_url( $ns_menu_logo_dark ) . '" alt="">';
			}
			?>
		</a>

		<button type="button" class="ns-side-panel__close" data-mdc-menu-close aria-label="<?php esc_attr_e( 'Fechar menu lateral', 'mundo-da-copa' ); ?>">
			<?php echo mdc_icon( 'close', 22 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</button>
	</div>

	<div class="ns-side-panel__intro">
		<span>NA SÚMULA</span>
		<strong>História, futebol e emoção.</strong>
		<p>Explore as principais áreas do portal.</p>
	</div>

	<nav class="ns-side-panel__nav" aria-label="<?php esc_attr_e( 'Navegação lateral', 'mundo-da-copa' ); ?>">
		<?php if ( has_nav_menu( 'lateral' ) ) : ?>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'lateral',
					'container'      => false,
					'menu_class'     => 'ns-side-menu',
					'depth'          => 2,
					'fallback_cb'    => false,
				)
			);
			?>
		<?php elseif ( has_nav_menu( 'principal' ) ) : ?>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'principal',
					'container'      => false,
					'menu_class'     => 'ns-side-menu',
					'depth'          => 2,
					'fallback_cb'    => false,
				)
			);
			?>
		<?php else : ?>
			<ul class="ns-side-menu">
				<li><a href="<?php echo esc_url( $ns_home_url ); ?>">Início</a></li>
				<?php foreach ( $ns_menu_items as $ns_type => $ns_label ) : ?>
					<?php $ns_link = get_post_type_archive_link( $ns_type ); ?>
					<?php if ( $ns_link ) : ?>
						<li><a href="<?php echo esc_url( $ns_link ); ?>"><?php echo esc_html( $ns_label ); ?></a></li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</nav>


	<section class="ns-side-panel__section ns-side-panel__section--popular">
		<div class="ns-side-panel__section-head">
			<span class="ns-side-panel__section-kicker"><?php echo mdc_icon( 'trending', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?> MAIS LIDAS</span>
		</div>
		<?php $ns_side_read_ids = function_exists( 'mdc_mais_lidas' ) ? mdc_mais_lidas( 5, 0 ) : array(); ?>
		<?php if ( $ns_side_read_ids ) : ?>
			<ol class="ns-side-popular">
				<?php foreach ( $ns_side_read_ids as $ns_index => $ns_read_id ) : ?>
					<li><a href="<?php echo esc_url( get_permalink( $ns_read_id ) ); ?>"><span><?php echo esc_html( str_pad( (string) ( $ns_index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span><strong><?php echo esc_html( get_the_title( $ns_read_id ) ); ?></strong></a></li>
				<?php endforeach; ?>
			</ol>
		<?php endif; ?>
	</section>

	<?php if ( mdc_config( 'mdc_newsletter_ativo' ) ) : ?>
	<section class="ns-side-panel__section ns-side-panel__section--newsletter">
		<span class="ns-side-panel__section-kicker"><?php esc_html_e( 'NEWSLETTER', 'mundo-da-copa' ); ?></span>
		<h2><?php echo esc_html( mdc_config( 'mdc_newsletter_titulo' ) ); ?></h2>
		<p><?php echo esc_html( mdc_config( 'mdc_newsletter_texto' ) ); ?></p>
		<?php if ( ! empty( $_GET['newsletter'] ) && 'ok' === sanitize_key( wp_unslash( $_GET['newsletter'] ) ) ) : ?>
			<div class="ns-newsletter-notice" role="status">E-mail cadastrado com sucesso.</div>
		<?php endif; ?>
		<form class="ns-newsletter-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="mdc_newsletter_inscrever">
			<?php wp_nonce_field( 'mdc_newsletter_inscrever', 'mdc_newsletter_nonce' ); ?>
			<label class="screen-reader-text" for="ns-newsletter-email">Seu e-mail</label>
			<div class="ns-newsletter-field"><input id="ns-newsletter-email" type="email" name="email" placeholder="Seu melhor e-mail" autocomplete="email" required><button type="submit" aria-label="Assinar newsletter"><?php echo mdc_icon( 'arrow', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></button></div>
		</form>
	</section>
	<?php endif; ?>

	<section class="ns-side-panel__section ns-side-panel__section--social">
		<span class="ns-side-panel__section-kicker"><?php esc_html_e( 'SIGA O PORTAL', 'mundo-da-copa' ); ?></span>
		<?php $ns_side_socials = function_exists( 'mdc_redes_sociais' ) ? mdc_redes_sociais() : array(); ?>
		<?php if ( $ns_side_socials ) : ?>
			<div class="ns-side-socials">
				<?php foreach ( $ns_side_socials as $ns_social_slug => $ns_social ) : ?>
					<a href="<?php echo esc_url( $ns_social['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $ns_social['rotulo'] ); ?>"><?php echo mdc_icon( $ns_social_slug, 17 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</section>

	<section class="ns-side-panel__section ns-side-panel__section--commercial">
		<a class="ns-side-commercial" href="<?php echo esc_url( mdc_anuncio_page_url() ); ?>">
			<span class="ns-side-panel__section-kicker">PUBLICIDADE</span>
			<strong>Anuncie aqui</strong>
			<span>Conheça os formatos e fale com nossa equipe →</span>
		</a>
	</section>

	<div class="ns-side-panel__footer">
		<?php if ( $ns_is_logged ) : ?>
			<a href="<?php echo esc_url( $ns_account_url ); ?>">Meu perfil</a>
			<a href="<?php echo esc_url( wp_logout_url( $ns_home_url ) ); ?>">Sair</a>
		<?php else : ?>
			<a href="<?php echo esc_url( $ns_account_url ); ?>">Minha conta</a>
		<?php endif; ?>
	</div>
</aside>

<?php
if ( function_exists( 'mdc_render_ad' ) ) {
	mdc_render_ad( 'menu' );
	mdc_render_ad( 'mobile', 'mdc-ad--mobile-only' );
}

/* Breadcrumb global para páginas, arquivos, busca e 404.
 * Os templates de matérias e entidades já possuem breadcrumb editorial próprio. */
if ( function_exists( 'mdc_breadcrumb' ) && ( is_page() || is_search() || is_404() ) && ! is_front_page() && ! is_home() ) :
	?>
	<div class="mdc-global-breadcrumb">
		<div class="mdc-container">
			<?php mdc_breadcrumb(); ?>
		</div>
	</div>
	<?php
endif;
?>

<main id="mdc-conteudo" class="mdc-main">
