<?php
/**
 * Mundo da Copa — Interativos editoriais.
 *
 * Quiz e Enquete usando apenas post_meta nativo.
 * A categoria "Interativos" é aplicada automaticamente.
 *
 * @package mundo-da-copa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* =========================================================
 * METABOX
 * ========================================================= */

function mdc_quiz_metabox() {
	add_meta_box(
		'mdc-quiz-box',
		'Interativo — conteúdo',
		'mdc_quiz_metabox_html',
		'post',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'mdc_quiz_metabox' );

/**
 * CSS e JS da área administrativa.
 */
function mdc_quiz_admin_assets( $hook ) {
	if ( ! in_array( $hook, array( 'post-new.php', 'post.php', 'edit.php' ), true ) ) {
		return;
	}
	?>
	<style>
		/* Painel do interativo */
		#mdc-quiz-box.mdc-interativo-hidden {
			display: none;
		}

		#mdc-quiz-box .inside {
			margin-top: 0;
			padding: 0;
		}

		.mdc-interativo-admin {
			padding: 20px;
			background: #fff;
		}

		.mdc-interativo-admin__intro {
			margin: 0 0 18px;
			padding: 14px 16px;
			border-left: 4px solid #009B4D;
			background: #f6f8fa;
		}

		.mdc-interativo-admin__intro strong {
			display: block;
			margin-bottom: 4px;
		}

		.mdc-interativo-admin__selector {
			display: flex;
			align-items: center;
			gap: 12px;
			margin-bottom: 20px;
			padding: 14px 16px;
			background: #f0f4f8;
			border: 1px solid #dcdcde;
			border-radius: 6px;
		}

		.mdc-interativo-admin__selector label {
			font-weight: 600;
		}

		.mdc-interativo-admin__selector select {
			min-width: 180px;
		}

		.mdc-interativo-panel {
			margin-top: 16px;
		}

		.mdc-interativo-panel__head {
			margin-bottom: 16px;
			padding-bottom: 12px;
			border-bottom: 1px solid #dcdcde;
		}

		.mdc-interativo-panel__head h3 {
			margin: 0 0 5px;
			font-size: 18px;
		}

		.mdc-quiz-item {
			margin: 18px 0;
			padding: 18px;
			border: 1px solid #dcdcde;
			border-radius: 8px;
			background: #f8f9fa;
		}

		.mdc-quiz-item__head {
			display: flex;
			align-items: center;
			justify-content: space-between;
			gap: 12px;
			margin-bottom: 14px;
		}

		.mdc-quiz-item__number {
			font-size: 15px;
			font-weight: 700;
		}

		.mdc-quiz-grid {
			display: grid;
			grid-template-columns: repeat(2, minmax(0, 1fr));
			gap: 14px;
		}

		.mdc-quiz-field {
			margin: 0;
		}

		.mdc-quiz-field--full {
			grid-column: 1 / -1;
		}

		.mdc-quiz-field label,
		.mdc-enquete-field label {
			display: block;
			margin-bottom: 6px;
			font-weight: 600;
		}

		.mdc-quiz-field textarea,
		.mdc-quiz-field input,
		.mdc-quiz-field select,
		.mdc-enquete-field textarea,
		.mdc-enquete-field input {
			width: 100%;
			box-sizing: border-box;
		}

		.mdc-quiz-field textarea {
			min-height: 88px;
		}

		.mdc-quiz-actions {
			display: flex;
			justify-content: flex-start;
			margin-top: 18px;
		}

		.mdc-enquete-options {
			display: grid;
			grid-template-columns: repeat(2, minmax(0, 1fr));
			gap: 14px;
			margin-top: 16px;
		}

		.mdc-enquete-field {
			margin: 0;
		}

		.mdc-enquete-message {
			margin-top: 18px;
			padding-top: 18px;
			border-top: 1px solid #dcdcde;
		}

		.mdc-interativo-admin__auto-category {
			margin-top: 18px;
			padding: 12px 14px;
			background: #f6f8fa;
			border: 1px solid #dcdcde;
			border-radius: 6px;
			color: #50575e;
		}

		.mdc-interativo-badge {
			display: inline-block;
			padding: 3px 8px;
			border-radius: 999px;
			background: #092B66;
			color: #fff;
			font-size: 11px;
			font-weight: 700;
			line-height: 1.4;
		}

		@media (max-width: 782px) {
			.mdc-quiz-grid,
			.mdc-enquete-options {
				grid-template-columns: 1fr;
			}

			.mdc-quiz-field--full {
				grid-column: auto;
			}

			.mdc-interativo-admin__selector {
				align-items: flex-start;
				flex-direction: column;
			}
		}
	</style>
	<?php

	if ( 'edit.php' === $hook ) {
		return;
	}
	?>
	<script>
	document.addEventListener('DOMContentLoaded', function () {
		var formato =
			document.getElementById('mdc-formato-post') ||
			document.querySelector('select[name="mdc_formato_post"]') ||
			document.querySelector('select[name="mdc_formato"]');

		var box = document.getElementById('mdc-quiz-box');

		if (!formato || !box) {
			return;
		}

		function atualizarPainel() {
			var valor = formato.value;
			var ativo = (valor === 'interativo' || valor === 'quiz');
			box.classList.toggle('mdc-interativo-hidden', !ativo);
		}

		formato.addEventListener('change', atualizarPainel);
		atualizarPainel();

		var tipo = document.getElementById('mdc-interativo-tipo');
		var panels = document.querySelectorAll('.mdc-interativo-panel');

		function alternarTipo() {
			if (!tipo) {
				return;
			}

			panels.forEach(function (panel) {
				panel.style.display =
					panel.getAttribute('data-panel') === tipo.value ? 'block' : 'none';
			});
		}

		if (tipo) {
			tipo.addEventListener('change', alternarTipo);
			alternarTipo();
		}

		var boxItems = document.getElementById('mdc-quiz-items');
		var addButton = document.getElementById('mdc-quiz-add');
		var template = document.getElementById('mdc-quiz-template');
		var index = template ? parseInt(template.getAttribute('data-index'), 10) || 0 : 0;

		if (boxItems && addButton && template) {
			addButton.addEventListener('click', function () {
				boxItems.insertAdjacentHTML(
					'beforeend',
					template.innerHTML.replaceAll('__INDEX__', index)
				);
				index++;
				atualizarNumeracao();
			});

			boxItems.addEventListener('click', function (event) {
				if (!event.target.classList.contains('mdc-quiz-remove')) {
					return;
				}

				var row = event.target.closest('.mdc-quiz-item');

				if (row) {
					row.remove();
					atualizarNumeracao();
				}
			});
		}

		function atualizarNumeracao() {
			if (!boxItems) {
				return;
			}

			boxItems.querySelectorAll('.mdc-quiz-item').forEach(function (item, i) {
				var number = item.querySelector('.mdc-quiz-item__number');

				if (number) {
					number.textContent = 'Pergunta ' + (i + 1);
				}
			});
		}

		atualizarNumeracao();
	});
	</script>
	<?php
}
add_action( 'admin_footer-post-new.php', 'mdc_quiz_admin_assets' );
add_action( 'admin_footer-post.php', 'mdc_quiz_admin_assets' );
add_action( 'admin_footer-edit.php', 'mdc_quiz_admin_assets' );

/* =========================================================
 * CAMPOS
 * ========================================================= */

function mdc_quiz_metabox_html( $post ) {
	wp_nonce_field( 'mdc_quiz_save', 'mdc_quiz_nonce' );

	$tipo = get_post_meta( $post->ID, '_mdc_interativo_tipo', true );

	if ( ! in_array( $tipo, array( 'quiz', 'enquete' ), true ) ) {
		$tipo = 'quiz';
	}

	$items = get_post_meta( $post->ID, '_mdc_quiz_items', true );
	$items = is_array( $items ) ? $items : array();

	$enquete = get_post_meta( $post->ID, '_mdc_enquete', true );
	$enquete = is_array( $enquete ) ? $enquete : array();

	$enquete = wp_parse_args(
		$enquete,
		array(
			'pergunta' => '',
			'a'        => '',
			'b'        => '',
			'c'        => '',
			'd'        => '',
			'mensagem' => 'Obrigado pelo voto!

Veja como os leitores do Mundo da Copa estão escolhendo a maior seleção da história dos Mundiais.',
		)
	);
	?>
	<div class="mdc-interativo-admin">

		<div class="mdc-interativo-admin__intro">
			<strong>Conteúdo interativo</strong>
			<span>Use este painel para criar quizzes e enquetes rápidos, sempre relacionados à Copa do Mundo.</span>
		</div>

		<div class="mdc-interativo-admin__selector">
			<label for="mdc-interativo-tipo">Tipo de interativo</label>

			<select name="mdc_interativo_tipo" id="mdc-interativo-tipo">
				<option value="quiz" <?php selected( $tipo, 'quiz' ); ?>>Quiz</option>
				<option value="enquete" <?php selected( $tipo, 'enquete' ); ?>>Enquete</option>
			</select>
		</div>

		<div class="mdc-interativo-panel" data-panel="quiz" <?php echo 'quiz' === $tipo ? '' : 'style="display:none;"'; ?>>
			<div class="mdc-interativo-panel__head">
				<h3>Quiz</h3>
				<p class="description">Cadastre perguntas com quatro alternativas. A explicação aparece depois que o leitor responde.</p>
			</div>

			<div id="mdc-quiz-items">
				<?php
				foreach ( $items as $i => $item ) {
					mdc_quiz_item_row( $i, $item );
				}
				?>
			</div>

			<div class="mdc-quiz-actions">
				<button type="button" class="button button-secondary" id="mdc-quiz-add">
					+ Adicionar pergunta
				</button>
			</div>

			<script type="text/template" id="mdc-quiz-template" data-index="<?php echo esc_attr( count( $items ) ); ?>"><?php mdc_quiz_item_row( '__INDEX__', array() ); ?></script>
		</div>

		<div class="mdc-interativo-panel" data-panel="enquete" <?php echo 'enquete' === $tipo ? '' : 'style="display:none;"'; ?>>
			<div class="mdc-interativo-panel__head">
				<h3>Enquete</h3>
				<p class="description">Uma pergunta com até quatro opções. Depois do voto, o leitor vê a distribuição das escolhas.</p>
			</div>

			<div class="mdc-enquete-field">
				<label for="mdc-enquete-pergunta">Pergunta</label>
				<textarea id="mdc-enquete-pergunta" name="mdc_enquete[pergunta]" rows="3" class="widefat" placeholder="Ex.: Qual foi a maior seleção da história das Copas?"><?php echo esc_textarea( $enquete['pergunta'] ); ?></textarea>
			</div>

			<div class="mdc-enquete-options">
				<?php foreach ( array( 'a', 'b', 'c', 'd' ) as $letra ) : ?>
					<div class="mdc-enquete-field">
						<label for="mdc-enquete-<?php echo esc_attr( $letra ); ?>">
							Opção <?php echo esc_html( strtoupper( $letra ) ); ?>
						</label>

						<input
							type="text"
							id="mdc-enquete-<?php echo esc_attr( $letra ); ?>"
							name="mdc_enquete[<?php echo esc_attr( $letra ); ?>]"
							value="<?php echo esc_attr( $enquete[ $letra ] ); ?>"
							class="widefat"
						>
					</div>
				<?php endforeach; ?>
			</div>

			<div class="mdc-enquete-field mdc-enquete-message">
				<label for="mdc-enquete-mensagem">Mensagem após votar</label>

				<textarea
					id="mdc-enquete-mensagem"
					name="mdc_enquete[mensagem]"
					rows="4"
					class="widefat"
					placeholder="Mensagem exibida depois que o leitor votar."
				><?php echo esc_textarea( $enquete['mensagem'] ); ?></textarea>

				<p class="description">
					Exibida junto com o resultado da enquete.
				</p>
			</div>
		</div>

		<div class="mdc-interativo-admin__auto-category">
			<strong>Categoria automática:</strong>
			Interativos
			<br>
			<span>Posts deste tipo recebem automaticamente essa categoria para facilitar a identificação no painel.</span>
		</div>

	</div>
	<?php
}

function mdc_quiz_item_row( $index, $item ) {
	$item = wp_parse_args(
		$item,
		array(
			'pergunta'  => '',
			'a'         => '',
			'b'         => '',
			'c'         => '',
			'd'         => '',
			'correta'   => 'a',
			'explicacao'=> '',
		)
	);
	?>
	<div class="mdc-quiz-item">
		<div class="mdc-quiz-item__head">
			<span class="mdc-quiz-item__number">Pergunta</span>
			<button type="button" class="button-link-delete mdc-quiz-remove">Remover pergunta</button>
		</div>

		<div class="mdc-quiz-grid">

			<div class="mdc-quiz-field mdc-quiz-field--full">
				<label>Pergunta</label>
				<textarea name="mdc_quiz[<?php echo esc_attr( $index ); ?>][pergunta]" rows="3" class="widefat" placeholder="Ex.: Em que país foi disputada a primeira Copa do Mundo?"><?php echo esc_textarea( $item['pergunta'] ); ?></textarea>
			</div>

			<?php foreach ( array( 'a', 'b', 'c', 'd' ) as $letra ) : ?>
				<div class="mdc-quiz-field">
					<label>Alternativa <?php echo esc_html( strtoupper( $letra ) ); ?></label>
					<input type="text" name="mdc_quiz[<?php echo esc_attr( $index ); ?>][<?php echo esc_attr( $letra ); ?>]" value="<?php echo esc_attr( $item[ $letra ] ); ?>" class="widefat">
				</div>
			<?php endforeach; ?>

			<div class="mdc-quiz-field">
				<label>Resposta correta</label>
				<select name="mdc_quiz[<?php echo esc_attr( $index ); ?>][correta]">
					<?php foreach ( array( 'a', 'b', 'c', 'd' ) as $letra ) : ?>
						<option value="<?php echo esc_attr( $letra ); ?>" <?php selected( $item['correta'], $letra ); ?>>
							<?php echo esc_html( 'Alternativa ' . strtoupper( $letra ) ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="mdc-quiz-field mdc-quiz-field--full">
				<label>Explicação após a resposta</label>
				<textarea name="mdc_quiz[<?php echo esc_attr( $index ); ?>][explicacao]" rows="3" class="widefat" placeholder="Texto exibido depois que o leitor responder."><?php echo esc_textarea( $item['explicacao'] ); ?></textarea>
			</div>

		</div>
	</div>
	<?php
}

/* =========================================================
 * SALVAMENTO + CATEGORIA AUTOMÁTICA
 * ========================================================= */

function mdc_quiz_categoria_interativos( $post_id, $ativo ) {
	$post_id = absint( $post_id );

	if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
		return;
	}

	$term = get_category_by_slug( 'interativos' );

	if ( ! $term ) {
		$criado = wp_insert_term( 'Interativos', 'category', array( 'slug' => 'interativos' ) );
		if ( is_wp_error( $criado ) ) {
			return;
		}
		$term_id = (int) $criado['term_id'];
	} else {
		$term_id = (int) $term->term_id;
	}

	$atuais = wp_get_post_categories( $post_id );
	$atuais = is_array( $atuais ) ? array_map( 'absint', $atuais ) : array();

	if ( $ativo ) {
		if ( ! in_array( $term_id, $atuais, true ) ) {
			$atuais[] = $term_id;
			wp_set_post_categories( $post_id, array_values( array_unique( $atuais ) ) );
		}
	} elseif ( in_array( $term_id, $atuais, true ) ) {
		$atuais = array_values( array_diff( $atuais, array( $term_id ) ) );
		wp_set_post_categories( $post_id, $atuais );
	}
}

function mdc_quiz_save( $post_id ) {
	if (
		! isset( $_POST['mdc_quiz_nonce'] ) ||
		! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_POST['mdc_quiz_nonce'] ) ),
			'mdc_quiz_save'
		)
	) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if (
		wp_is_post_revision( $post_id ) ||
		! current_user_can( 'edit_post', $post_id ) ||
		'post' !== get_post_type( $post_id )
	) {
		return;
	}

	$tipo = isset( $_POST['mdc_interativo_tipo'] )
		? sanitize_key( wp_unslash( $_POST['mdc_interativo_tipo'] ) )
		: 'quiz';

	if ( ! in_array( $tipo, array( 'quiz', 'enquete' ), true ) ) {
		$tipo = 'quiz';
	}

	update_post_meta( $post_id, '_mdc_interativo_tipo', $tipo );

	$items = isset( $_POST['mdc_quiz'] ) && is_array( $_POST['mdc_quiz'] )
		? $_POST['mdc_quiz']
		: array();

	$clean = array();

	foreach ( $items as $item ) {
		$pergunta = isset( $item['pergunta'] )
			? sanitize_textarea_field( wp_unslash( $item['pergunta'] ) )
			: '';

		$vals = array();

		foreach ( array( 'a', 'b', 'c', 'd' ) as $letra ) {
			$vals[ $letra ] = isset( $item[ $letra ] )
				? sanitize_text_field( wp_unslash( $item[ $letra ] ) )
				: '';
		}

		$correta = isset( $item['correta'] ) &&
			in_array( $item['correta'], array( 'a', 'b', 'c', 'd' ), true )
			? sanitize_key( $item['correta'] )
			: 'a';

		$explicacao = isset( $item['explicacao'] )
			? wp_kses_post( wp_unslash( $item['explicacao'] ) )
			: '';

		if ( '' === $pergunta || in_array( '', $vals, true ) ) {
			continue;
		}

		$clean[] = array(
			'pergunta'  => $pergunta,
			'a'         => $vals['a'],
			'b'         => $vals['b'],
			'c'         => $vals['c'],
			'd'         => $vals['d'],
			'correta'   => $correta,
			'explicacao'=> $explicacao,
		);
	}

	update_post_meta( $post_id, '_mdc_quiz_items', $clean );

	if ( ! get_post_meta( $post_id, '_mdc_quiz_stats', true ) ) {
		update_post_meta(
			$post_id,
			'_mdc_quiz_stats',
			array(
				'attempts'           => 0,
				'score_total'        => 0,
				'questions_total'    => 0,
				'score_distribution' => array(),
				'answer_distribution' => array(),
			)
		);
	}

	$eq = isset( $_POST['mdc_enquete'] ) && is_array( $_POST['mdc_enquete'] )
		? $_POST['mdc_enquete']
		: array();

	$mensagem_padrao = 'Obrigado pelo voto!

Veja como os leitores do Mundo da Copa estão escolhendo a maior seleção da história dos Mundiais.';

	$enquete = array(
		'pergunta' => isset( $eq['pergunta'] )
			? sanitize_textarea_field( wp_unslash( $eq['pergunta'] ) )
			: '',
		'mensagem' => isset( $eq['mensagem'] )
			? sanitize_textarea_field( wp_unslash( $eq['mensagem'] ) )
			: $mensagem_padrao,
	);

	foreach ( array( 'a', 'b', 'c', 'd' ) as $letra ) {
		$enquete[ $letra ] = isset( $eq[ $letra ] )
			? sanitize_text_field( wp_unslash( $eq[ $letra ] ) )
			: '';
	}

	update_post_meta( $post_id, '_mdc_enquete', $enquete );

	if ( ! get_post_meta( $post_id, '_mdc_enquete_votes', true ) ) {
		update_post_meta(
			$post_id,
			'_mdc_enquete_votes',
			array(
				'a' => 0,
				'b' => 0,
				'c' => 0,
				'd' => 0,
			)
		);
	}

	$formato = get_post_meta( $post_id, 'mdc_formato_post', true );
	$ativo   = in_array( $formato, array( 'interativo', 'quiz' ), true );

	mdc_quiz_categoria_interativos( $post_id, $ativo );
}
add_action( 'save_post', 'mdc_quiz_save' );

/* =========================================================
 * COLUNA NO PAINEL DE POSTS
 * ========================================================= */

function mdc_quiz_coluna( $columns ) {
	$novo = array();

	foreach ( $columns as $key => $label ) {
		$novo[ $key ] = $label;

		if ( 'categories' === $key ) {
			$novo['mdc_interativo'] = 'Interativo';
		}
	}

	return $novo;
}
add_filter( 'manage_post_posts_columns', 'mdc_quiz_coluna' );

function mdc_quiz_coluna_conteudo( $column, $post_id ) {
	if ( 'mdc_interativo' !== $column ) {
		return;
	}

	$tipo   = get_post_meta( $post_id, '_mdc_interativo_tipo', true );
	$formato = get_post_meta( $post_id, 'mdc_formato_post', true );

	if ( ! in_array( $formato, array( 'interativo', 'quiz' ), true ) ) {
		echo '—';
		return;
	}

	if ( 'enquete' === $tipo ) {
		echo '<span class="mdc-interativo-badge">Enquete</span>';
	} else {
		echo '<span class="mdc-interativo-badge">Quiz</span>';
	}
}
add_action( 'manage_post_posts_custom_column', 'mdc_quiz_coluna_conteudo', 10, 2 );

/* =========================================================
 * FRONT-END
 * ========================================================= */

function mdc_interativo_content( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	$formato = get_post_meta( get_the_ID(), 'mdc_formato_post', true );

	if ( ! in_array( $formato, array( 'interativo', 'quiz' ), true ) ) {
		return $content;
	}

	$tipo = get_post_meta( get_the_ID(), '_mdc_interativo_tipo', true );

	if ( 'enquete' === $tipo ) {
		return $content . mdc_enquete_markup( get_the_ID() );
	}

	return $content . mdc_quiz_markup( get_the_ID() );
}

function mdc_quiz_markup( $post_id ) {
	$items = get_post_meta( $post_id, '_mdc_quiz_items', true );

	if ( ! is_array( $items ) || empty( $items ) ) {
		return '';
	}

	$quiz = array();

	foreach ( $items as $item ) {
		if (
			empty( $item['pergunta'] ) ||
			empty( $item['a'] ) ||
			empty( $item['b'] ) ||
			empty( $item['c'] ) ||
			empty( $item['d'] )
		) {
			continue;
		}

		$quiz[] = array(
			'pergunta' => $item['pergunta'],
			'opcoes'   => array(
				'a' => $item['a'],
				'b' => $item['b'],
				'c' => $item['c'],
				'd' => $item['d'],
			),
			'correta'   => $item['correta'],
			'explicacao'=> $item['explicacao'],
		);
	}

	if ( empty( $quiz ) ) {
		return '';
	}

	ob_start();
	?>
	<section class="mdc-interativo mdc-quiz" data-mdc-quiz data-post-id="<?php echo esc_attr( $post_id ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'mdc_quiz_result_' . $post_id ) ); ?>" aria-label="Quiz">
		<div class="mdc-interativo__header">
			<span class="mdc-interativo__eyebrow">QUIZ</span>
			<div class="mdc-interativo__counter" data-quiz-counter>
				1 de <?php echo count( $quiz ); ?>
			</div>
		</div>

		<div class="mdc-interativo__progress">
			<span data-quiz-progress></span>
		</div>

		<div class="mdc-quiz__stage" data-quiz-stage>
			<h2 class="mdc-interativo__question" data-quiz-question></h2>

			<div class="mdc-quiz__options" data-quiz-options></div>

			<div
				class="mdc-interativo__feedback"
				data-quiz-feedback
				aria-live="polite"
				hidden
			></div>

			<button
				type="button"
				class="mdc-interativo__button"
				data-quiz-next
				hidden
			>
				Próxima pergunta
			</button>
		</div>

		<div class="mdc-interativo__result" data-quiz-result hidden>
			<span class="mdc-interativo__eyebrow">RESULTADO</span>
			<strong class="mdc-interativo__score" data-quiz-score></strong>
			<p data-quiz-message></p>

			<button type="button" class="mdc-interativo__button" data-quiz-restart>
				Refazer o quiz
			</button>
		</div>

		<script type="application/json" class="mdc-quiz__data"><?php echo wp_json_encode( $quiz, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?></script>
	</section>
	<?php

	return ob_get_clean();
}

function mdc_enquete_markup( $post_id ) {
	$e = get_post_meta( $post_id, '_mdc_enquete', true );

	if ( ! is_array( $e ) || empty( $e['pergunta'] ) ) {
		return '';
	}

	$votes = get_post_meta( $post_id, '_mdc_enquete_votes', true );
	$votes = is_array( $votes ) ? $votes : array();

	foreach ( array( 'a', 'b', 'c', 'd' ) as $letra ) {
		$votes[ $letra ] = isset( $votes[ $letra ] ) ? (int) $votes[ $letra ] : 0;
	}

	$options = array();

	foreach ( array( 'a', 'b', 'c', 'd' ) as $letra ) {
		if ( ! empty( $e[ $letra ] ) ) {
			$options[ $letra ] = $e[ $letra ];
		}
	}

	if ( count( $options ) < 2 ) {
		return '';
	}

	$mensagem = ! empty( $e['mensagem'] )
		? $e['mensagem']
		: 'Obrigado pelo voto!

Veja como os leitores do Mundo da Copa estão escolhendo a maior seleção da história dos Mundiais.';
	$total_votes = array_sum( $votes );

	ob_start();
	?>
	<section
		class="mdc-interativo mdc-enquete"
		data-mdc-enquete
		data-post-id="<?php echo esc_attr( $post_id ); ?>"
		data-nonce="<?php echo esc_attr( wp_create_nonce( 'mdc_enquete_vote_' . $post_id ) ); ?>"
		aria-label="Enquete"
	>
		<div class="mdc-interativo__header">
			<span class="mdc-interativo__eyebrow">ENQUETE</span>
			<span class="mdc-enquete__status" data-enquete-status>Escolha uma opção</span>
		</div>

		<h2 class="mdc-interativo__question">
			<?php echo esc_html( $e['pergunta'] ); ?>
		</h2>

		<div class="mdc-enquete__options" data-enquete-options>
			<?php foreach ( $options as $letra => $label ) : ?>
				<button
					type="button"
					class="mdc-enquete__option"
					data-enquete-option="<?php echo esc_attr( $letra ); ?>"
				>
					<span><?php echo esc_html( $label ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>

		<div class="mdc-enquete__results-actions">
			<button type="button" class="mdc-interativo__button mdc-enquete__results-toggle" data-enquete-results-toggle>
				Ver resultado parcial
			</button>
		</div>

		<div class="mdc-enquete__results" data-enquete-results hidden>
			<div class="mdc-enquete__results-title">Resultado parcial da enquete</div>

			<?php foreach ( $options as $letra => $label ) : ?>
				<div class="mdc-enquete__result-row">
					<div class="mdc-enquete__result-head">
						<strong><?php echo esc_html( $label ); ?></strong>
						<span data-enquete-percent="<?php echo esc_attr( $letra ); ?>"><?php echo esc_html( $total_votes ? round( ( $votes[ $letra ] / $total_votes ) * 100 ) : 0 ); ?>%</span>
					</div>

					<div class="mdc-enquete__bar">
						<span data-enquete-bar="<?php echo esc_attr( $letra ); ?>"></span>
					</div>
				</div>
			<?php endforeach; ?>

			<div class="mdc-enquete__message">
				<?php echo wpautop( esc_html( $mensagem ) ); ?>
			</div>
		</div>
	</section>
	<?php

	return ob_get_clean();
}

add_filter( 'the_content', 'mdc_interativo_content', 35 );

/* =========================================================
 * AJAX DA ENQUETE
 * ========================================================= */

function mdc_enquete_ajax() {
	$post_id = isset( $_POST['post_id'] )
		? absint( $_POST['post_id'] )
		: 0;

	$opcao = isset( $_POST['opcao'] )
		? sanitize_key( wp_unslash( $_POST['opcao'] ) )
		: '';

	$nonce = isset( $_POST['nonce'] )
		? sanitize_text_field( wp_unslash( $_POST['nonce'] ) )
		: '';

	if (
		! $post_id ||
		! wp_verify_nonce( $nonce, 'mdc_enquete_vote_' . $post_id ) ||
		! in_array( $opcao, array( 'a', 'b', 'c', 'd' ), true )
	) {
		wp_send_json_error(
			array(
				'message' => 'Não foi possível registrar o voto.',
			),
			400
		);
	}

	$e = get_post_meta( $post_id, '_mdc_enquete', true );

	if ( ! is_array( $e ) || empty( $e[ $opcao ] ) ) {
		wp_send_json_error(
			array(
				'message' => 'Opção inválida.',
			),
			400
		);
	}

	$votes = get_post_meta( $post_id, '_mdc_enquete_votes', true );
	$votes = is_array( $votes ) ? $votes : array();

	foreach ( array( 'a', 'b', 'c', 'd' ) as $letra ) {
		$votes[ $letra ] = isset( $votes[ $letra ] ) ? (int) $votes[ $letra ] : 0;
	}

	$votes[ $opcao ]++;

	update_post_meta( $post_id, '_mdc_enquete_votes', $votes );

	$total   = array_sum( $votes );
	$percent = array();

	foreach ( $votes as $letra => $votos ) {
		$percent[ $letra ] = $total ? round( ( $votos / $total ) * 100 ) : 0;
	}

	wp_send_json_success(
		array(
			'votes'   => $votes,
			'percent' => $percent,
			'total'   => $total,
		)
	);
}
add_action( 'wp_ajax_mdc_enquete_vote', 'mdc_enquete_ajax' );
add_action( 'wp_ajax_nopriv_mdc_enquete_vote', 'mdc_enquete_ajax' );


/* =========================================================
 * ESTATÍSTICAS DO QUIZ
 * ========================================================= */

function mdc_quiz_registrar_resultado() {
	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	$total   = isset( $_POST['total'] ) ? absint( $_POST['total'] ) : 0;
	$nonce   = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
	$raw_answers = isset( $_POST['answers'] ) ? wp_unslash( $_POST['answers'] ) : '';
	$answers = json_decode( $raw_answers, true );

	if (
		! $post_id ||
		! $total ||
		! is_array( $answers ) ||
		! wp_verify_nonce( $nonce, 'mdc_quiz_result_' . $post_id ) ||
		'post' !== get_post_type( $post_id ) ||
		'publish' !== get_post_status( $post_id ) ||
		'quiz' !== get_post_meta( $post_id, '_mdc_interativo_tipo', true )
	) {
		wp_send_json_error( array( 'message' => 'Resultado inválido.' ), 400 );
	}

	$items = get_post_meta( $post_id, '_mdc_quiz_items', true );
	$items = is_array( $items ) ? $items : array();

	$valid_items = array();

	foreach ( $items as $item ) {
		if (
			! empty( $item['pergunta'] ) &&
			! empty( $item['a'] ) &&
			! empty( $item['b'] ) &&
			! empty( $item['c'] ) &&
			! empty( $item['d'] ) &&
			isset( $item['correta'] ) &&
			in_array( $item['correta'], array( 'a', 'b', 'c', 'd' ), true )
		) {
			$valid_items[] = $item;
		}
	}

	if ( count( $answers ) !== count( $valid_items ) || $total !== count( $valid_items ) ) {
		wp_send_json_error( array( 'message' => 'Quantidade de respostas inválida.' ), 400 );
	}

	$score = 0;

	foreach ( $valid_items as $index => $item ) {
		$resposta = isset( $answers[ $index ] ) ? sanitize_key( $answers[ $index ] ) : '';

		if ( $resposta === $item['correta'] ) {
			$score++;
		}
	}

	$stats = get_post_meta( $post_id, '_mdc_quiz_stats', true );
	$stats = is_array( $stats ) ? $stats : array();

	$stats['attempts']        = isset( $stats['attempts'] ) ? (int) $stats['attempts'] : 0;
	$stats['score_total']     = isset( $stats['score_total'] ) ? (int) $stats['score_total'] : 0;
	$stats['questions_total'] = isset( $stats['questions_total'] ) ? (int) $stats['questions_total'] : 0;
	$stats['score_distribution'] = isset( $stats['score_distribution'] ) && is_array( $stats['score_distribution'] )
		? $stats['score_distribution']
		: array();

	$stats['answer_distribution'] = isset( $stats['answer_distribution'] ) && is_array( $stats['answer_distribution'] )
		? $stats['answer_distribution']
		: array();

	$stats['attempts']++;
	$stats['score_total'] += $score;
	$stats['questions_total'] += $total;

	$key = (string) $score;
	$stats['score_distribution'][ $key ] = isset( $stats['score_distribution'][ $key ] )
		? (int) $stats['score_distribution'][ $key ] + 1
		: 1;

	foreach ( $valid_items as $index => $item ) {
		if ( ! isset( $stats['answer_distribution'][ $index ] ) || ! is_array( $stats['answer_distribution'][ $index ] ) ) {
			$stats['answer_distribution'][ $index ] = array(
				'a' => 0,
				'b' => 0,
				'c' => 0,
				'd' => 0,
			);
		}

		$resposta = isset( $answers[ $index ] ) ? sanitize_key( $answers[ $index ] ) : '';

		if ( isset( $stats['answer_distribution'][ $index ][ $resposta ] ) ) {
			$stats['answer_distribution'][ $index ][ $resposta ]++;
		}
	}

	update_post_meta( $post_id, '_mdc_quiz_stats', $stats );

	wp_send_json_success(
		array(
			'recorded' => true,
			'score'    => $score,
			'total'    => $total,
		)
	);
}

add_action( 'wp_ajax_mdc_quiz_resultado', 'mdc_quiz_registrar_resultado' );
add_action( 'wp_ajax_nopriv_mdc_quiz_resultado', 'mdc_quiz_registrar_resultado' );
