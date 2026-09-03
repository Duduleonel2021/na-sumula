jQuery(function($){
	$('.mdc-campo-cor').wpColorPicker();

	$(document).on('click', '.mdc-media-select', function(e){
		e.preventDefault();
		const button = $(this);
		const target = $('#' + button.data('target'));
		const preview = button.closest('.mdc-media-control').find('.mdc-media-control__preview');

		const isFileField = button.data('target') === 'mdc_media_kit';
		const frame = wp.media({
			title: isFileField ? 'Selecionar Media Kit em PDF' : 'Selecionar imagem',
			button: { text: isFileField ? 'Usar PDF' : 'Usar imagem' },
			library: isFileField ? { type: 'application/pdf' } : { type: 'image' },
			multiple: false
		});

		frame.on('select', function(){
			const attachment = frame.state().get('selection').first().toJSON();
			target.val(attachment.id);
			const src = attachment.sizes && attachment.sizes.medium
				? attachment.sizes.medium.url
				: attachment.url;
			preview.html('<img src="' + src.replace(/"/g, '&quot;') + '" alt="">');
		});

		frame.open();
	});

	$(document).on('click', '.mdc-media-remove', function(e){
		e.preventDefault();
		const button = $(this);
		$('#' + button.data('target')).val('');
		button.closest('.mdc-media-control').find('.mdc-media-control__preview').html('<span>Nenhum arquivo selecionado</span>');
	});
});
