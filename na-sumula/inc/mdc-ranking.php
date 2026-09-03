<?php
/**
 * Mundo da Copa — Formato editorial: Ranking.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registra os campos do ranking.
 */
function mdc_ranking_metabox() {
	add_meta_box(
		'mdc-ranking-box',
		'Ranking — itens',
		'mdc_ranking_metabox_html',
		'post',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'mdc_ranking_metabox' );

function mdc_ranking_metabox_html( $post ) {
	wp_nonce_field( 'mdc_ranking_save', 'mdc_ranking_nonce' );

	$items = get_post_meta( $post->ID, '_mdc_ranking_items', true );
	$items = is_array( $items ) ? $items : array();

	?>
	<p><strong>Use este bloco somente quando o formato do post for “Ranking”.</strong></p>
	<p class="description">Adicione os itens na ordem do ranking. Você pode usar 5, 10, 15 ou quantos itens precisar.</p>

	<div id="mdc-ranking-items">
		<?php foreach ( $items as $i => $item ) : ?>
			<?php mdc_ranking_item_row( $i, $item ); ?>
		<?php endforeach; ?>
	</div>

	<p>
		<button type="button" class="button button-secondary" id="mdc-ranking-add">+ Adicionar item</button>
	</p>

	<script type="text/template" id="mdc-ranking-template">
		<?php mdc_ranking_item_row( '__INDEX__', array() ); ?>
	</script>

	<script>
	document.addEventListener('DOMContentLoaded', function () {
		const box = document.getElementById('mdc-ranking-items');
		const add = document.getElementById('mdc-ranking-add');
		const tpl = document.getElementById('mdc-ranking-template').innerHTML;
		let index = <?php echo (int) count( $items ); ?>;

		if (!box || !add) return;

		add.addEventListener('click', function () {
			box.insertAdjacentHTML('beforeend', tpl.replaceAll('__INDEX__', index));
			index++;
		});

		box.addEventListener('click', function (event) {
			if (event.target.classList.contains('mdc-ranking-remove')) {
				event.target.closest('.mdc-ranking-item').remove();
			}
		});
	});
	</script>
	<?php
}

function mdc_ranking_item_row( $index, $item ) {
	$item = wp_parse_args(
		$item,
		array(
			'nome'      => '',
			'imagem'    => '',
			'numero'    => '',
			'descricao' => '',
			'destaque'  => '',
		)
	);
	?>
	<div class="mdc-ranking-item" style="border:1px solid #ddd;padding:15px;margin:12px 0;background:#fafafa;">
		<p>
			<strong>Posição</strong><br>
			<input type="number" min="1" name="mdc_ranking[<?php echo esc_attr( $index ); ?>][numero]" value="<?php echo esc_attr( $item['numero'] ); ?>" style="width:90px;">
		</p>
		<p>
			<strong>Nome</strong><br>
			<input type="text" name="mdc_ranking[<?php echo esc_attr( $index ); ?>][nome]" value="<?php echo esc_attr( $item['nome'] ); ?>" class="widefat">
		</p>
		<p>
			<strong>Imagem</strong><br>
			<input type="url" name="mdc_ranking[<?php echo esc_attr( $index ); ?>][imagem]" value="<?php echo esc_attr( $item['imagem'] ); ?>" class="widefat" placeholder="URL da imagem">
		</p>
		<p>
			<strong>Informação de destaque</strong><br>
			<input type="text" name="mdc_ranking[<?php echo esc_attr( $index ); ?>][destaque]" value="<?php echo esc_attr( $item['destaque'] ); ?>" class="widefat" placeholder="Ex.: 15 gols em Copas">
		</p>
		<p>
			<strong>Texto</strong><br>
			<textarea name="mdc_ranking[<?php echo esc_attr( $index ); ?>][descricao]" rows="4" class="widefat"><?php echo esc_textarea( $item['descricao'] ); ?></textarea>
		</p>
		<button type="button" class="button mdc-ranking-remove">Remover item</button>
	</div>
	<?php
}

/**
 * Salva os itens.
 */
function mdc_ranking_save( $post_id ) {
	if ( ! isset( $_POST['mdc_ranking_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mdc_ranking_nonce'] ) ), 'mdc_ranking_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;
	if ( 'post' !== get_post_type( $post_id ) ) return;

	$items = isset( $_POST['mdc_ranking'] ) && is_array( $_POST['mdc_ranking'] ) ? $_POST['mdc_ranking'] : array();
	$clean = array();

	foreach ( $items as $item ) {
		$clean[] = array(
			'numero'    => isset( $item['numero'] ) ? absint( $item['numero'] ) : 0,
			'nome'      => isset( $item['nome'] ) ? sanitize_text_field( wp_unslash( $item['nome'] ) ) : '',
			'imagem'    => isset( $item['imagem'] ) ? esc_url_raw( wp_unslash( $item['imagem'] ) ) : '',
			'destaque'  => isset( $item['destaque'] ) ? sanitize_text_field( wp_unslash( $item['destaque'] ) ) : '',
			'descricao' => isset( $item['descricao'] ) ? wp_kses_post( wp_unslash( $item['descricao'] ) ) : '',
		);
	}

	update_post_meta( $post_id, '_mdc_ranking_items', $clean );
}
add_action( 'save_post', 'mdc_ranking_save' );

/**
 * Exibe o ranking dentro do conteúdo.
 */
function mdc_ranking_content( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$formato = get_post_meta( get_the_ID(), 'mdc_formato_post', true );
	if ( 'ranking' !== $formato ) {
		return $content;
	}

	$items = get_post_meta( get_the_ID(), '_mdc_ranking_items', true );
	if ( ! is_array( $items ) || empty( $items ) ) {
		return $content;
	}

	ob_start();
	?>
	<section class="mdc-editorial-ranking" aria-label="Ranking">
		<?php foreach ( $items as $index => $item ) : 
			$numero = ! empty( $item['numero'] ) ? $item['numero'] : ( $index + 1 );
		?>
			<article class="mdc-editorial-ranking__item">
				<div class="mdc-editorial-ranking__number"><?php echo esc_html( $numero . 'º' ); ?></div>

				<div class="mdc-editorial-ranking__content">
					<div class="mdc-editorial-ranking__top">
						<?php if ( ! empty( $item['imagem'] ) ) : ?>
							<div class="mdc-editorial-ranking__image">
								<img src="<?php echo esc_url( $item['imagem'] ); ?>" alt="<?php echo esc_attr( $item['nome'] ); ?>" loading="lazy">
							</div>
						<?php endif; ?>

						<div class="mdc-editorial-ranking__body">
							<h2><?php echo esc_html( $item['nome'] ); ?></h2>

							<?php if ( ! empty( $item['destaque'] ) ) : ?>
								<strong class="mdc-editorial-ranking__highlight"><?php echo esc_html( $item['destaque'] ); ?></strong>
							<?php endif; ?>
						</div>
					</div>

					<?php if ( ! empty( $item['descricao'] ) ) : ?>
						<div class="mdc-editorial-ranking__text"><?php echo wpautop( wp_kses_post( $item['descricao'] ) ); ?></div>
					<?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</section>
	<?php

	return $content . ob_get_clean();
}
add_filter( 'the_content', 'mdc_ranking_content', 30 );
