/**
 * Mundo da Copa — seletor de mídia nos campos de imagem e galeria.
 *
 * Usa a biblioteca de mídia nativa do WordPress (wp.media). Nenhum plugin.
 */
(function ($) {
	'use strict';

	if (typeof wp === 'undefined' || !wp.media) {
		return;
	}

	/* ---- Campo de imagem única ---------------------------------------- */
	$(document).on('click', '[data-mdc-imagem] [data-mdc-escolher]', function (e) {
		e.preventDefault();

		var campo = $(this).closest('[data-mdc-imagem]');

		var frame = wp.media({
			title: 'Escolher imagem',
			button: { text: 'Usar esta imagem' },
			library: { type: 'image' },
			multiple: false
		});

		frame.on('select', function () {
			var anexo = frame.state().get('selection').first().toJSON();
			var url = (anexo.sizes && anexo.sizes.medium) ? anexo.sizes.medium.url : anexo.url;

			campo.find('input[type="hidden"]').val(anexo.id);
			campo.find('.mdc-campo-imagem__preview').html(
				$('<img>').attr('src', url).attr('alt', '').css({
					maxWidth: '220px',
					height: 'auto',
					borderRadius: '6px'
				})
			);
		});

		frame.open();
	});

	$(document).on('click', '[data-mdc-imagem] [data-mdc-remover]', function (e) {
		e.preventDefault();
		var campo = $(this).closest('[data-mdc-imagem]');
		campo.find('input[type="hidden"]').val('');
		campo.find('.mdc-campo-imagem__preview').empty();
	});

	/* ---- Campo de galeria --------------------------------------------- */
	$(document).on('click', '[data-mdc-galeria] [data-mdc-escolher]', function (e) {
		e.preventDefault();

		var campo = $(this).closest('[data-mdc-galeria]');
		var atuais = campo.find('input[type="hidden"]').val();
		var selecionados = atuais ? atuais.split(',') : [];

		var frame = wp.media({
			title: 'Escolher imagens da galeria',
			button: { text: 'Usar estas imagens' },
			library: { type: 'image' },
			multiple: 'add'
		});

		// Pré-seleciona o que já está no campo.
		frame.on('open', function () {
			var selecao = frame.state().get('selection');
			selecionados.forEach(function (id) {
				var anexo = wp.media.attachment(id);
				anexo.fetch();
				selecao.add(anexo ? [anexo] : []);
			});
		});

		frame.on('select', function () {
			var ids = [];
			var itens = $('<div>');

			frame.state().get('selection').each(function (anexo) {
				var dados = anexo.toJSON();
				var url = (dados.sizes && dados.sizes.thumbnail) ? dados.sizes.thumbnail.url : dados.url;

				ids.push(dados.id);
				itens.append(
					$('<img>').attr('src', url).attr('alt', '').css({
						width: '76px',
						height: '76px',
						objectFit: 'cover',
						borderRadius: '6px'
					})
				);
			});

			campo.find('input[type="hidden"]').val(ids.join(','));
			campo.find('.mdc-campo-galeria__itens').html(itens.children());
		});

		frame.open();
	});

	$(document).on('click', '[data-mdc-galeria] [data-mdc-remover]', function (e) {
		e.preventDefault();
		var campo = $(this).closest('[data-mdc-galeria]');
		campo.find('input[type="hidden"]').val('');
		campo.find('.mdc-campo-galeria__itens').empty();
	});

	/* ---- Seletor visual de modelos da homepage ------------------------ */
	$(document).on('change', '.mdc-modelo-card input[type=\"radio\"]', function () {
		var nome = $(this).attr('name');
		$('.mdc-modelo-card input[name=\"' + nome + '\"]').closest('.mdc-modelo-card').removeClass('is-active');
		$(this).closest('.mdc-modelo-card').addClass('is-active');
	});
})(jQuery);
