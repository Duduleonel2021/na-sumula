<?php
/**
 * Mundo da Copa — Ranking FIFA Feminino
 *
 * Shortcode:
 * [ranking_fifa_feminino]
 *
 * Exibe somente o Top 30 do Ranking Mundial Feminino da FIFA.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'mdc_ranking_fifa_feminino_shortcode' ) ) {

	function mdc_ranking_fifa_feminino_shortcode() {

		$ranking = array(
			array( 1,  'Espanha',             '2.105,36' ),
			array( 2,  'Estados Unidos',      '2.057,92' ),
			array( 3,  'Alemanha',            '2.028,99' ),
			array( 4,  'Inglaterra',          '2.027,13' ),
			array( 5,  'Japão',               '1.998,83' ),
			array( 6,  'França',              '1.983,84' ),
			array( 7,  'Brasil',              '1.976,73' ),
			array( 8,  'Suécia',              '1.937,94' ),
			array( 9,  'Canadá',              '1.936,90' ),
			array( 10, 'Países Baixos',       '1.911,75' ),
			array( 11, 'Coreia do Norte',     '1.910,63' ),
			array( 12, 'Dinamarca',            '1.910,20' ),
			array( 13, 'Itália',               '1.891,83' ),
			array( 14, 'Noruega',              '1.878,52' ),
			array( 15, 'Austrália',            '1.830,66' ),
			array( 16, 'China',                '1.799,13' ),
			array( 17, 'Islândia',             '1.792,32' ),
			array( 18, 'Bélgica',              '1.786,01' ),
			array( 19, 'Coreia do Sul',        '1.780,68' ),
			array( 20, 'Colômbia',             '1.775,96' ),
			array( 21, 'Irlanda',              '1.769,74' ),
			array( 22, 'Portugal',             '1.751,11' ),
			array( 23, 'Áustria',              '1.749,66' ),
			array( 24, 'Finlândia',            '1.744,99' ),
			array( 25, 'Escócia',              '1.743,49' ),
			array( 26, 'Suíça',                '1.734,18' ),
			array( 27, 'Rússia',               '1.718,14' ),
			array( 28, 'México',               '1.715,13' ),
			array( 29, 'Polônia',              '1.694,17' ),
			array( 30, 'Argentina',            '1.683,00' ),
		);

		ob_start();
		?>
		<section class="mdc-ranking-feminino" aria-labelledby="mdc-ranking-feminino-titulo">

			<header class="mdc-ranking-feminino__header">
				<div>
					<span class="mdc-ranking-feminino__eyebrow">RANKING MUNDIAL</span>
					<h2 id="mdc-ranking-feminino-titulo">Ranking FIFA Feminino</h2>
				</div>

				<span class="mdc-ranking-feminino__badge">Top 30</span>
			</header>

			<div class="mdc-ranking-feminino__meta">
				<span>Futebol feminino</span>
				<span>Atualização: 16 de junho de 2026</span>
			</div>

			<div class="mdc-ranking-feminino__table-wrap">
				<table class="mdc-ranking-feminino__table">
					<thead>
						<tr>
							<th scope="col">Pos.</th>
							<th scope="col">Seleção</th>
							<th scope="col">Pontos</th>
						</tr>
					</thead>

					<tbody>
						<?php foreach ( $ranking as $item ) : ?>
							<tr class="<?php echo $item[0] <= 3 ? 'is-top' : ''; ?>">
								<td class="mdc-ranking-feminino__pos">
									<?php echo esc_html( $item[0] ); ?>
								</td>

								<td class="mdc-ranking-feminino__team">
									<?php echo esc_html( $item[1] ); ?>
								</td>

								<td class="mdc-ranking-feminino__points">
									<?php echo esc_html( $item[2] ); ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<p class="mdc-ranking-feminino__source">
				Fonte: Ranking Mundial Feminino da FIFA/Coca-Cola.
			</p>

		</section>
		<?php

		return ob_get_clean();
	}

	add_shortcode(
		'ranking_fifa_feminino',
		'mdc_ranking_fifa_feminino_shortcode'
	);
}

/**
 * CSS específico do ranking feminino.
 */
if ( ! function_exists( 'mdc_ranking_fifa_feminino_styles' ) ) {

	function mdc_ranking_fifa_feminino_styles() {

		$css = <<<'CSS'

.mdc-ranking-feminino {
	--rf-blue: #092B66;
	--rf-green: #009B4D;
	--rf-ink: #10213A;
	--rf-muted: #667085;
	--rf-border: #E4EAF1;
	--rf-soft: #F5F8FB;

	margin: 32px 0;
	font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
	color: var(--rf-ink);
	border: 1px solid var(--rf-border);
	border-radius: 16px;
	overflow: hidden;
	background: #fff;
}

.mdc-ranking-feminino__header {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 16px;
	padding: 18px 20px;
	background: var(--rf-blue);
	color: #fff;
}

.mdc-ranking-feminino__eyebrow {
	display: block;
	margin-bottom: 4px;
	font-size: .66rem;
	font-weight: 800;
	letter-spacing: .08em;
	opacity: .72;
}

.mdc-ranking-feminino__header h2 {
	margin: 0;
	font-family: Manrope, Inter, sans-serif;
	font-size: 1.25rem;
	line-height: 1.15;
	color: #fff;
}

.mdc-ranking-feminino__badge {
	padding: 6px 10px;
	border-radius: 999px;
	background: var(--rf-green);
	color: #fff;
	font-size: .7rem;
	font-weight: 800;
	white-space: nowrap;
}

.mdc-ranking-feminino__meta {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 12px;
	padding: 10px 20px;
	border-bottom: 1px solid var(--rf-border);
	color: var(--rf-muted);
	font-size: .76rem;
}

.mdc-ranking-feminino__table-wrap {
	overflow-x: auto;
}

.mdc-ranking-feminino__table {
	width: 100%;
	border-collapse: collapse;
	margin: 0;
	font-size: .86rem;
}

.mdc-ranking-feminino__table th {
	padding: 9px 14px;
	background: var(--rf-soft);
	color: var(--rf-blue);
	font-size: .68rem;
	font-weight: 800;
	letter-spacing: .04em;
	text-transform: uppercase;
	text-align: left;
}

.mdc-ranking-feminino__table th:first-child,
.mdc-ranking-feminino__table td:first-child {
	width: 70px;
	text-align: center;
}

.mdc-ranking-feminino__table th:last-child,
.mdc-ranking-feminino__table td:last-child {
	width: 130px;
	text-align: right;
}

.mdc-ranking-feminino__table td {
	padding: 9px 14px;
	border-bottom: 1px solid var(--rf-border);
}

.mdc-ranking-feminino__table tbody tr:nth-child(even) td {
	background: var(--rf-soft);
}

.mdc-ranking-feminino__table tbody tr:last-child td {
	border-bottom: 0;
}

.mdc-ranking-feminino__pos {
	font-weight: 800;
	color: var(--rf-blue);
}

.mdc-ranking-feminino__team {
	font-weight: 700;
}

.mdc-ranking-feminino__points {
	font-weight: 800;
	font-variant-numeric: tabular-nums;
}

.mdc-ranking-feminino__table tr.is-top td:first-child {
	border-left: 3px solid var(--rf-green);
}

.mdc-ranking-feminino__source {
	margin: 0;
	padding: 10px 20px 14px;
	color: var(--rf-muted);
	font-size: .7rem;
}

html[data-mdc-theme="dark"] .mdc-ranking-feminino {
	--rf-ink: #f2f6fb;
	--rf-muted: #9aa8ba;
	--rf-border: #26364c;
	--rf-soft: #101d30;
	background: #101d30;
}

html[data-mdc-theme="dark"] .mdc-ranking-feminino__table th {
	background: #0d1929;
	color: #dbe7f5;
}

html[data-mdc-theme="dark"] .mdc-ranking-feminino__table td {
	border-bottom-color: var(--rf-border);
}

html[data-mdc-theme="dark"] .mdc-ranking-feminino__table tbody tr:nth-child(even) td {
	background: #0d1929;
}

html[data-mdc-theme="dark"] .mdc-ranking-feminino__pos {
	color: #dbe7f5;
}

@media (max-width: 640px) {
	.mdc-ranking-feminino__header {
		padding: 16px;
	}

	.mdc-ranking-feminino__meta {
		align-items: flex-start;
		flex-direction: column;
		padding: 10px 16px;
	}

	.mdc-ranking-feminino__table {
		min-width: 480px;
	}
}

CSS;

		wp_register_style( 'mdc-ranking-fifa-feminino', false );
		wp_enqueue_style( 'mdc-ranking-fifa-feminino' );
		wp_add_inline_style( 'mdc-ranking-fifa-feminino', $css );
	}
	add_action( 'wp_enqueue_scripts', 'mdc_ranking_fifa_feminino_styles' );
}
