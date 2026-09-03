<?php
/**
 * Mundo da Copa — Shortcodes editoriais.
 *
 * Shortcodes:
 * [mdc_ranking]...[/mdc_ranking]
 * [mdc_copa_tabela]...[/mdc_copa_tabela]
 * [mdc_fase nome="..."]...[/mdc_fase]
 * [mdc_grupo nome="A"]...[/mdc_grupo]
 * [mdc_jogo]Brasil|1|Croácia|1|Pênaltis: 2-4[/mdc_jogo]
 * [mdc_fifa_ranking]...[/mdc_fifa_ranking]
 *
 * @package mundo-da-copa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================
 * HELPERS
 * ========================================================= */

if ( ! function_exists( 'mdc_sc_text' ) ) {
	function mdc_sc_text( $value ) {
		$value = (string) $value;

		/*
		 * Normaliza entidades que podem chegar ao shortcode
		 * duplamente codificadas pelo Gutenberg.
		 */
		$value = html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
		$value = html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

		return esc_html( trim( $value ) );
	}
}

if ( ! function_exists( 'mdc_sc_lines' ) ) {
	function mdc_sc_lines( $content ) {
		$content = shortcode_unautop( (string) $content );
		$content = trim( $content );

		if ( '' === $content ) {
			return array();
		}

		$lines = preg_split( '/\r\n|\r|\n/', $content );

		return array_values(
			array_filter(
				array_map( 'trim', $lines ),
				static function ( $line ) {
					return '' !== $line;
				}
			)
		);
	}
}

/* =========================================================
 * CSS
 * ========================================================= */

if ( ! function_exists( 'mdc_shortcodes_styles' ) ) {
	function mdc_shortcodes_styles() {
		$css = <<<'CSS'
.mdc-component {
	--mdc-blue: #092B66;
	--mdc-green: #009B4D;
	--mdc-ink: #10213A;
	--mdc-muted: #667085;
	--mdc-border: #E4EAF1;
	--mdc-soft: #F5F8FB;
	--mdc-white: #fff;
	margin: 34px 0;
	font-family: Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
	color: var(--mdc-ink);
}

.mdc-component *,
.mdc-component *::before,
.mdc-component *::after {
	box-sizing: border-box;
}

.mdc-component__title {
	margin: 0 0 18px;
	font-family: Manrope,Inter,sans-serif;
	font-size: 1.15rem;
	font-weight: 800;
	line-height: 1.2;
	color: var(--mdc-blue);
}

.mdc-component__note {
	margin: 0 0 16px;
	color: var(--mdc-muted);
	font-size: .86rem;
}

.mdc-table-wrap {
	width: 100%;
	overflow-x: auto;
	-webkit-overflow-scrolling: touch;
	border: 1px solid var(--mdc-border);
	border-radius: 14px;
	background: #fff;
}

.mdc-table {
	width: 100%;
	min-width: 700px;
	margin: 0;
	border-collapse: collapse;
	font-size: .86rem;
}

.mdc-table th {
	padding: 10px 12px;
	background: var(--mdc-blue);
	color: #fff;
	font-size: .70rem;
	font-weight: 800;
	letter-spacing: .04em;
	text-transform: uppercase;
	text-align: center;
	white-space: nowrap;
}

.mdc-table th:nth-child(2),
.mdc-table td:nth-child(2) {
	text-align: left;
}

.mdc-table td {
	padding: 9px 12px;
	border-bottom: 1px solid var(--mdc-border);
	text-align: center;
	vertical-align: middle;
}

.mdc-table tbody tr:last-child td {
	border-bottom: 0;
}

.mdc-table tbody tr:nth-child(even) td {
	background: var(--mdc-soft);
}

.mdc-rank-name {
	font-weight: 700;
}

.mdc-rank-pos {
	width: 54px;
	font-weight: 800;
	white-space: nowrap;
}

.mdc-rank-medal {
	margin-right: 4px;
}

.mdc-rank-top1 td:first-child {
	border-left: 3px solid #D6A629;
}

.mdc-rank-top2 td:first-child {
	border-left: 3px solid #A9B1BD;
}

.mdc-rank-top3 td:first-child {
	border-left: 3px solid #B8784A;
}

/* Fases */

.mdc-copa-tabela {
	margin-top: 38px;
}

.mdc-tabs {
	display: flex;
	gap: 5px;
	margin-bottom: 22px;
	padding: 5px;
	overflow-x: auto;
	border: 1px solid var(--mdc-border);
	border-radius: 12px;
	background: var(--mdc-soft);
	scrollbar-width: thin;
}

.mdc-tab {
	flex: 0 0 auto;
	padding: 9px 13px;
	border: 0;
	border-radius: 8px;
	background: transparent;
	color: var(--mdc-muted);
	font: 800 .72rem Inter,sans-serif;
	cursor: pointer;
	white-space: nowrap;
}

.mdc-tab.is-active {
	background: var(--mdc-blue);
	color: #fff;
}

.mdc-tab-panel {
	display: none;
	padding-top: 4px;
}

.mdc-tab-panel.is-active {
	display: block;
}

.mdc-fase {
	margin: 0;
}

.mdc-fase > .mdc-component__title {
	margin: 0 0 20px;
	padding-bottom: 10px;
	border-bottom: 1px solid var(--mdc-border);
}

.mdc-group {
	margin: 0 0 24px;
	border: 1px solid var(--mdc-border);
	border-radius: 14px;
	overflow: hidden;
	background: #fff;
}

.mdc-group__head {
	display: flex !important;
	flex-direction: column;
	gap: 3px;
	margin: 0;
	padding: 13px 16px 12px;
	background: var(--mdc-blue) !important;
	color: #fff !important;
}

.mdc-group__name {
	display: block !important;
	margin: 0 !important;
	color: #fff !important;
	font-family: Manrope,Inter,sans-serif;
	font-size: 1rem;
	font-weight: 800;
	line-height: 1.2;
}

.mdc-group__label {
	display: block;
	color: #42d58d;
	font-size: .62rem;
	font-weight: 800;
	letter-spacing: .12em;
	text-transform: uppercase;
}

.mdc-group .mdc-table-wrap {
	border: 0;
	border-radius: 0;
}

.mdc-match {
	position: relative;
	display: grid;
	grid-template-columns: minmax(0,1fr) 80px minmax(0,1fr);
	gap: 12px;
	align-items: center;
	margin: 0 0 6px;
	padding: 12px 14px;
	border: 1px solid var(--mdc-border);
	border-radius: 10px;
	background: #fff;
}

.mdc-match__team {
	font-size: .84rem;
	font-weight: 700;
}

.mdc-match__team--right {
	text-align: right;
}

.mdc-match__score {
	text-align: center;
	font-size: .90rem;
	font-weight: 900;
	color: var(--mdc-blue);
	white-space: nowrap;
}

.mdc-match__detail {
	grid-column: 1 / -1;
	padding-top: 4px;
	color: var(--mdc-muted);
	font-size: .72rem;
	text-align: center;
}

.mdc-fifa {
	border: 1px solid var(--mdc-border);
	border-radius: 14px;
	background: #fff;
	overflow: hidden;
}

.mdc-fifa__meta {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 14px;
	padding: 0 16px 14px;
}

.mdc-fifa__date {
	font-size: .78rem;
	color: var(--mdc-muted);
}

.mdc-fifa__search {
	width: min(100%,280px);
	padding: 9px 12px;
	border: 1px solid var(--mdc-border);
	border-radius: 9px;
	font: 400 .82rem Inter,sans-serif;
}

.mdc-fifa__filters {
	display: flex;
	gap: 6px;
	flex-wrap: wrap;
	padding: 12px 16px;
	border-top: 1px solid var(--mdc-border);
	border-bottom: 1px solid var(--mdc-border);
}

.mdc-fifa-filter {
	padding: 7px 10px;
	border: 1px solid var(--mdc-border);
	border-radius: 999px;
	background: #fff;
	color: var(--mdc-blue);
	font: 700 .7rem Inter,sans-serif;
	cursor: pointer;
}

.mdc-fifa-filter.is-active {
	background: var(--mdc-green);
	border-color: var(--mdc-green);
	color: #fff;
}

.mdc-fifa .mdc-table {
	min-width: 620px;
}

.mdc-fifa__change-up {
	color: #078B4A;
	font-weight: 800;
}

.mdc-fifa__change-down {
	color: #C24141;
	font-weight: 800;
}

.mdc-fifa__change-same {
	color: var(--mdc-muted);
}

html[data-mdc-theme="dark"] .mdc-component {
	--mdc-ink: #f2f6fb;
	--mdc-muted: #9aa8ba;
	--mdc-border: #26364c;
	--mdc-soft: #101d30;
}

html[data-mdc-theme="dark"] .mdc-table-wrap,
html[data-mdc-theme="dark"] .mdc-group,
html[data-mdc-theme="dark"] .mdc-match,
html[data-mdc-theme="dark"] .mdc-fifa {
	background: #101d30;
}

html[data-mdc-theme="dark"] .mdc-table tbody tr:nth-child(even) td {
	background: #0d1929;
}

html[data-mdc-theme="dark"] .mdc-match__score,
html[data-mdc-theme="dark"] .mdc-component__title {
	color: #fff;
}

html[data-mdc-theme="dark"] .mdc-fifa-filter {
	background: #101d30;
	color: #fff;
}

@media (max-width: 760px) {
	.mdc-component {
		margin: 28px 0;
	}

	.mdc-groups {
		grid-template-columns: 1fr;
	}

	.mdc-match {
		grid-template-columns: minmax(0,1fr) 60px minmax(0,1fr);
		gap: 8px;
		padding: 11px;
	}

	.mdc-fifa__meta {
		align-items: stretch;
		flex-direction: column;
	}

	.mdc-fifa__search {
		width: 100%;
	}
}
CSS;

		wp_register_style( 'mdc-shortcodes', false );
		wp_enqueue_style( 'mdc-shortcodes' );
		wp_add_inline_style( 'mdc-shortcodes', $css );
	}
	add_action( 'wp_enqueue_scripts', 'mdc_shortcodes_styles' );
}

/* =========================================================
 * RANKING FINAL
 * ========================================================= */

if ( ! function_exists( 'mdc_shortcode_ranking' ) ) {
	function mdc_shortcode_ranking( $atts, $content = null ) {
		$atts = shortcode_atts(
			array(
				'titulo' => 'Classificação final',
			),
			$atts,
			'mdc_ranking'
		);

		$rows = mdc_sc_lines( $content );

		ob_start();
		?>
		<section class="mdc-component mdc-ranking" aria-label="<?php echo esc_attr( $atts['titulo'] ); ?>">

			<?php if ( '' !== $atts['titulo'] ) : ?>
				<h3 class="mdc-component__title"><?php echo esc_html( $atts['titulo'] ); ?></h3>
			<?php endif; ?>

			<div class="mdc-table-wrap">
				<table class="mdc-table">
					<thead>
						<tr>
							<th>Pos.</th>
							<th>Seleção</th>
							<th>Pts</th>
							<th>J</th>
							<th>V</th>
							<th>E</th>
							<th>D</th>
							<th>GP</th>
							<th>GC</th>
							<th>SG</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $rows as $row ) : ?>

							<?php
							$cols = array_map( 'trim', explode( '|', $row ) );

							if ( count( $cols ) < 10 ) {
								continue;
							}

							$pos = (int) $cols[0];
							$class = '';
							$medal = '';

							if ( 1 === $pos ) {
								$class = 'mdc-rank-top1';
								$medal = '<span class="mdc-rank-medal">🥇</span>';
							} elseif ( 2 === $pos ) {
								$class = 'mdc-rank-top2';
								$medal = '<span class="mdc-rank-medal">🥈</span>';
							} elseif ( 3 === $pos ) {
								$class = 'mdc-rank-top3';
								$medal = '<span class="mdc-rank-medal">🥉</span>';
							}
							?>

							<tr class="<?php echo esc_attr( $class ); ?>">
								<td class="mdc-rank-pos">
									<?php echo $medal . esc_html( $pos . 'º' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</td>
								<td class="mdc-rank-name"><?php echo mdc_sc_text( $cols[1] ); ?></td>
								<td><?php echo mdc_sc_text( $cols[2] ); ?></td>
								<td><?php echo mdc_sc_text( $cols[3] ); ?></td>
								<td><?php echo mdc_sc_text( $cols[4] ); ?></td>
								<td><?php echo mdc_sc_text( $cols[5] ); ?></td>
								<td><?php echo mdc_sc_text( $cols[6] ); ?></td>
								<td><?php echo mdc_sc_text( $cols[7] ); ?></td>
								<td><?php echo mdc_sc_text( $cols[8] ); ?></td>
								<td><?php echo mdc_sc_text( $cols[9] ); ?></td>
							</tr>

						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

		</section>
		<?php

		return ob_get_clean();
	}

	add_shortcode( 'mdc_ranking', 'mdc_shortcode_ranking' );
}

/* =========================================================
 * JOGO
 * ========================================================= */

if ( ! function_exists( 'mdc_shortcode_jogo' ) ) {
	function mdc_shortcode_jogo( $atts, $content = null ) {
		$parts = array_map( 'trim', explode( '|', trim( (string) $content ) ) );

		if ( count( $parts ) < 4 ) {
			return '';
		}

		$home   = $parts[0];
		$score1 = $parts[1];
		$away   = $parts[2];
		$score2 = $parts[3];
		$detail = isset( $parts[4] ) ? $parts[4] : '';

		ob_start();
		?>
		<div class="mdc-match">
			<div class="mdc-match__team"><?php echo mdc_sc_text( $home ); ?></div>
			<div class="mdc-match__score">
				<?php echo mdc_sc_text( $score1 ); ?> × <?php echo mdc_sc_text( $score2 ); ?>
			</div>
			<div class="mdc-match__team mdc-match__team--right"><?php echo mdc_sc_text( $away ); ?></div>

			<?php if ( '' !== $detail ) : ?>
				<div class="mdc-match__detail"><?php echo mdc_sc_text( $detail ); ?></div>
			<?php endif; ?>
		</div>
		<?php

		return ob_get_clean();
	}

	add_shortcode( 'mdc_jogo', 'mdc_shortcode_jogo' );
}

/* =========================================================
 * GRUPO
 * ========================================================= */

if ( ! function_exists( 'mdc_shortcode_grupo' ) ) {
	function mdc_shortcode_grupo( $atts, $content = null ) {
		$atts = shortcode_atts(
			array(
				'nome' => '',
			),
			$atts,
			'mdc_grupo'
		);

		$rows = mdc_sc_lines( $content );
		$nome = trim( (string) $atts['nome'] );

		ob_start();
		?>
		<section class="mdc-group" aria-label="<?php echo esc_attr( 'Grupo ' . $nome ); ?>">

			<header class="mdc-group__head">

				<span class="mdc-group__label">
					<?php esc_html_e( 'Fase de grupos', 'mundo-da-copa' ); ?>
				</span>

				<h4 class="mdc-group__name">
					<?php echo mdc_sc_text( 'Grupo ' . $nome ); ?>
				</h4>

			</header>

			<div class="mdc-table-wrap">

				<table class="mdc-table">

					<thead>
						<tr>
							<th>#</th>
							<th>Seleção</th>
							<th>Pts</th>
							<th>J</th>
							<th>V</th>
							<th>E</th>
							<th>D</th>
							<th>GP</th>
							<th>GC</th>
							<th>SG</th>
						</tr>
					</thead>

					<tbody>

						<?php $position = 1; ?>

						<?php foreach ( $rows as $row ) : ?>

							<?php
							$cols = array_map( 'trim', explode( '|', $row ) );

							if ( count( $cols ) < 9 ) {
								continue;
							}
							?>

							<tr>
								<td><strong><?php echo esc_html( $position ); ?></strong></td>
								<td class="mdc-rank-name"><?php echo mdc_sc_text( $cols[0] ); ?></td>
								<td><?php echo mdc_sc_text( $cols[1] ); ?></td>
								<td><?php echo mdc_sc_text( $cols[2] ); ?></td>
								<td><?php echo mdc_sc_text( $cols[3] ); ?></td>
								<td><?php echo mdc_sc_text( $cols[4] ); ?></td>
								<td><?php echo mdc_sc_text( $cols[5] ); ?></td>
								<td><?php echo mdc_sc_text( $cols[6] ); ?></td>
								<td><?php echo mdc_sc_text( $cols[7] ); ?></td>
								<td><?php echo mdc_sc_text( $cols[8] ); ?></td>
							</tr>

							<?php $position++; ?>

						<?php endforeach; ?>

					</tbody>

				</table>

			</div>

		</section>
		<?php

		return ob_get_clean();
	}

	add_shortcode( 'mdc_grupo', 'mdc_shortcode_grupo' );
}

/* =========================================================
 * FASE
 * ========================================================= */

if ( ! function_exists( 'mdc_shortcode_fase' ) ) {
	function mdc_shortcode_fase( $atts, $content = null ) {
		$atts = shortcode_atts(
			array(
				'nome' => 'Fase',
			),
			$atts,
			'mdc_fase'
		);

		return '<div class="mdc-fase">' .
			'<h3 class="mdc-component__title">' . mdc_sc_text( $atts['nome'] ) . '</h3>' .
			do_shortcode( shortcode_unautop( $content ) ) .
			'</div>';
	}

	add_shortcode( 'mdc_fase', 'mdc_shortcode_fase' );
}

/* =========================================================
 * TABELA DA COPA — ABAS
 * ========================================================= */

if ( ! function_exists( 'mdc_shortcode_copa_tabela' ) ) {
	function mdc_shortcode_copa_tabela( $atts, $content = null ) {

		$content = shortcode_unautop( (string) $content );
		$matches = array();

		$pattern = '/\[mdc_fase\b([^\]]*)\](.*?)\[\/mdc_fase\]/is';

		if ( preg_match_all( $pattern, $content, $found, PREG_SET_ORDER ) ) {
			foreach ( $found as $match ) {
				$attrs = isset( $match[1] ) ? trim( $match[1] ) : '';
				$phase_content = isset( $match[2] ) ? trim( $match[2] ) : '';
				$phase_atts = shortcode_parse_atts( $attrs );

				$name = isset( $phase_atts['nome'] ) && '' !== trim( $phase_atts['nome'] )
					? $phase_atts['nome']
					: 'Fase ' . ( count( $matches ) + 1 );

				$matches[] = array( 'name' => $name, 'content' => $phase_content );
			}
		}

		if ( empty( $matches ) ) {
			return '<div class="mdc-component"><p>Não há fases cadastradas.</p></div>';
		}

		$tabs = array();
		$panels = array();

		foreach ( $matches as $index => $phase ) {
			$id = 'mdc-fase-' . wp_unique_id();
			$tabs[] = array( 'id' => $id, 'name' => $phase['name'] );
			$panels[] = array( 'id' => $id, 'content' => do_shortcode( shortcode_unautop( $phase['content'] ) ) );
		}

		ob_start();
		?>
		<section class="mdc-component mdc-copa-tabela" aria-label="Tabela da Copa">
			<nav class="mdc-tabs" role="tablist" aria-label="Fases da Copa">
				<?php foreach ( $tabs as $index => $tab ) : ?>
					<button type="button" class="mdc-tab<?php echo 0 === $index ? ' is-active' : ''; ?>" role="tab" aria-selected="<?php echo 0 === $index ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $tab['id'] ); ?>" data-mdc-tab="<?php echo esc_attr( $tab['id'] ); ?>"><?php echo mdc_sc_text( $tab['name'] ); ?></button>
				<?php endforeach; ?>
			</nav>

			<?php foreach ( $panels as $index => $panel ) : ?>
				<div id="<?php echo esc_attr( $panel['id'] ); ?>" class="mdc-tab-panel<?php echo 0 === $index ? ' is-active' : ''; ?>" role="tabpanel" aria-hidden="<?php echo 0 === $index ? 'false' : 'true'; ?>">
					<?php echo $panel['content']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endforeach; ?>
		</section>
		<?php

		return ob_get_clean();
	}

	add_shortcode( 'mdc_copa_tabela', 'mdc_shortcode_copa_tabela' );
}

/* =========================================================
 * JAVASCRIPT
 * ========================================================= */

if ( ! function_exists( 'mdc_shortcodes_script' ) ) {
	function mdc_shortcodes_script() {
		?>
		<script>
		document.addEventListener('click', function (event) {

			const tab = event.target.closest('.mdc-tab');

			if (tab) {

				const component = tab.closest('.mdc-copa-tabela');

				if (!component) {
					return;
				}

				component.querySelectorAll('.mdc-tab').forEach(function (button) {
					button.classList.remove('is-active');
					button.setAttribute('aria-selected', 'false');
				});

				component.querySelectorAll('.mdc-tab-panel').forEach(function (panel) {
					panel.classList.remove('is-active');
					panel.setAttribute('aria-hidden', 'true');
				});

				tab.classList.add('is-active');
				tab.setAttribute('aria-selected', 'true');

				const target = component.querySelector(
					'#' + CSS.escape(tab.dataset.mdcTab)
				);

				if (target) {
					target.classList.add('is-active');
					target.setAttribute('aria-hidden', 'false');
				}
			}

			const filter = event.target.closest('.mdc-fifa-filter');

			if (filter) {

				const fifa = filter.closest('.mdc-fifa');

				if (!fifa) {
					return;
				}

				const value = filter.dataset.confed || 'all';

				fifa.querySelectorAll('.mdc-fifa-filter').forEach(function (button) {
					button.classList.toggle('is-active', button === filter);
				});

				fifa.querySelectorAll('tbody tr[data-confed]').forEach(function (row) {
					row.style.display =
						value === 'all' || row.dataset.confed === value
							? ''
							: 'none';
				});
			}
		});

		document.addEventListener('input', function (event) {

			const search = event.target.closest('.mdc-fifa__search');

			if (!search) {
				return;
			}

			const fifa = search.closest('.mdc-fifa');

			if (!fifa) {
				return;
			}

			const query = search.value.toLowerCase().trim();

			fifa.querySelectorAll('tbody tr[data-team]').forEach(function (row) {
				row.style.display =
					row.dataset.team.includes(query)
						? ''
						: 'none';
			});
		});
		</script>
		<?php
	}

	add_action( 'wp_footer', 'mdc_shortcodes_script', 30 );
}

/* =========================================================
 * RANKING FIFA
 * ========================================================= */

if ( ! function_exists( 'mdc_shortcode_fifa_ranking' ) ) {
	function mdc_shortcode_fifa_ranking( $atts, $content = null ) {

		$atts = shortcode_atts(
			array(
				'data'   => '',
				'titulo' => 'Ranking Mundial da FIFA',
			),
			$atts,
			'mdc_fifa_ranking'
		);

		$rows = mdc_sc_lines( $content );

		ob_start();
		?>
		<section
			class="mdc-component mdc-fifa"
			aria-label="<?php echo esc_attr( $atts['titulo'] ); ?>"
		>

			<?php if ( '' !== $atts['titulo'] ) : ?>

				<div style="padding:16px 16px 0;">
					<h2 class="mdc-component__title">
						<?php echo esc_html( $atts['titulo'] ); ?>
					</h2>
				</div>

			<?php endif; ?>

			<div class="mdc-fifa__meta">

				<div class="mdc-fifa__date">

					<?php if ( '' !== $atts['data'] ) : ?>

						<strong>Atualização:</strong>
						<?php echo mdc_sc_text( $atts['data'] ); ?>

					<?php else : ?>

						<strong>Atualização:</strong>
						informe a data do ranking

					<?php endif; ?>

				</div>

				<input
					type="search"
					class="mdc-fifa__search"
					placeholder="Buscar seleção..."
					aria-label="Buscar seleção"
				>

			</div>

			<div class="mdc-fifa__filters">

				<?php
				$filtros = array(
					'all'      => 'Todas',
					'AFC'      => 'AFC',
					'CAF'      => 'CAF',
					'CONCACAF' => 'CONCACAF',
					'CONMEBOL' => 'CONMEBOL',
					'OFC'      => 'OFC',
					'UEFA'     => 'UEFA',
				);
				?>

				<?php foreach ( $filtros as $slug => $label ) : ?>

					<button
						type="button"
						class="mdc-fifa-filter<?php echo 'all' === $slug ? ' is-active' : ''; ?>"
						data-confed="<?php echo esc_attr( $slug ); ?>"
					>
						<?php echo esc_html( $label ); ?>
					</button>

				<?php endforeach; ?>

			</div>

			<div class="mdc-table-wrap" style="border:0;border-radius:0;">

				<table class="mdc-table">

					<thead>
						<tr>
							<th>Pos.</th>
							<th>Seleção</th>
							<th>Confed.</th>
							<th>Pontos</th>
							<th>Var.</th>
						</tr>
					</thead>

					<tbody>

						<?php $position = 1; ?>

						<?php foreach ( $rows as $row ) : ?>

							<?php
							$cols = array_map( 'trim', explode( '|', $row ) );

							if ( count( $cols ) < 5 ) {
								continue;
							}

							$team   = $cols[0];
							$code   = $cols[1];
							$conf   = strtoupper( $cols[2] );
							$score  = $cols[3];
							$change = $cols[4];

							$change_html = '<span class="mdc-fifa__change-same">—</span>';

							if ( is_numeric( $change ) ) {

								$change_number = (int) $change;

								if ( $change_number > 0 ) {
									$change_html =
										'<span class="mdc-fifa__change-up">↑ ' .
										abs( $change_number ) .
										'</span>';
								} elseif ( $change_number < 0 ) {
									$change_html =
										'<span class="mdc-fifa__change-down">↓ ' .
										abs( $change_number ) .
										'</span>';
								}
							}
							?>

							<tr
								data-confed="<?php echo esc_attr( $conf ); ?>"
								data-team="<?php echo esc_attr( strtolower( $team . ' ' . $code ) ); ?>"
							>
								<td class="mdc-rank-pos"><?php echo esc_html( $position ); ?></td>
								<td class="mdc-rank-name"><?php echo mdc_sc_text( $team ); ?></td>
								<td><?php echo mdc_sc_text( $conf ); ?></td>
								<td><?php echo mdc_sc_text( $score ); ?></td>
								<td><?php echo $change_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
							</tr>

							<?php $position++; ?>

						<?php endforeach; ?>

					</tbody>

				</table>

			</div>

		</section>
		<?php

		return ob_get_clean();
	}

	add_shortcode( 'mdc_fifa_ranking', 'mdc_shortcode_fifa_ranking' );
}
