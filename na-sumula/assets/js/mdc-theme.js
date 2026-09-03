/**
 * Na Súmula — JavaScript principal do tema.
 *
 * Responsabilidades:
 * - tema claro/escuro;
 * - menu lateral;
 * - busca;
 * - contagem regressiva das próximas Copas.
 */

(function () {
	'use strict';

	function getPreferredTheme() {
		var configured =
			window.MDCThemeConfig &&
			typeof window.MDCThemeConfig.defaultTheme === 'string'
				? window.MDCThemeConfig.defaultTheme
				: 'system';

		var saved = null;

		try {
			saved = window.localStorage.getItem('mdc-theme');
		} catch (error) {
			saved = null;
		}

		if (saved === 'dark' || saved === 'light') {
			return saved;
		}

		if (configured === 'dark' || configured === 'light') {
			return configured;
		}

		if (
			window.matchMedia &&
			window.matchMedia('(prefers-color-scheme: dark)').matches
		) {
			return 'dark';
		}

		return 'light';
	}

	function applyTheme(theme, persist) {
		if (theme !== 'dark' && theme !== 'light') {
			theme = 'light';
		}

		document.documentElement.setAttribute('data-mdc-theme', theme);

		if (persist) {
			try {
				window.localStorage.setItem('mdc-theme', theme);
			} catch (error) {
				/* localStorage pode estar bloqueado. */
			}
		}

		document.querySelectorAll(
			'[data-mdc-theme-toggle], [data-theme-toggle]'
		).forEach(function (button) {
			button.setAttribute(
				'aria-label',
				theme === 'dark'
					? 'Ativar modo claro'
					: 'Ativar modo escuro'
			);
			button.setAttribute('aria-pressed', theme === 'dark' ? 'true' : 'false');
		});
	}

	function initTheme() {
		applyTheme(getPreferredTheme(), false);

		document.querySelectorAll(
			'[data-mdc-theme-toggle], [data-theme-toggle]'
		).forEach(function (button) {
			if (button.dataset.mdcThemeBound === 'true') {
				return;
			}

			button.dataset.mdcThemeBound = 'true';

			button.addEventListener('click', function (event) {
				event.preventDefault();

				var current =
					document.documentElement.getAttribute('data-mdc-theme') ||
					'light';

				applyTheme(current === 'dark' ? 'light' : 'dark', true);
			});
		});

		if (window.matchMedia) {
			var media = window.matchMedia('(prefers-color-scheme: dark)');

			var mediaHandler = function () {
				var saved = null;

				try {
					saved = window.localStorage.getItem('mdc-theme');
				} catch (error) {
					saved = null;
				}

				if (saved !== 'dark' && saved !== 'light') {
					applyTheme(
						media.matches ? 'dark' : 'light',
						false
					);
				}
			};

			if (typeof media.addEventListener === 'function') {
				media.addEventListener('change', mediaHandler);
			} else if (typeof media.addListener === 'function') {
				media.addListener(mediaHandler);
			}
		}
	}

	function initNavigation() {
		var body = document.body;
		var sidePanel = document.getElementById('mdc-side-panel');
		var sideOverlay = document.getElementById('mdc-side-overlay');

		var menuButtons = document.querySelectorAll(
			'[data-mdc-menu-toggle], [data-menu-toggle]'
		);

		var searchButtons = document.querySelectorAll(
			'[data-mdc-search-toggle], [data-search-toggle]'
		);

		var searchPanel = document.getElementById('mdc-search');

		function setMenu(open) {
			if (!sidePanel) {
				return;
			}

			body.classList.toggle('mdc-menu-open', open);

			sidePanel.setAttribute(
				'aria-hidden',
				open ? 'false' : 'true'
			);

			menuButtons.forEach(function (button) {
				button.setAttribute(
					'aria-expanded',
					open ? 'true' : 'false'
				);
			});

			if (sideOverlay) {
				sideOverlay.setAttribute(
					'aria-hidden',
					open ? 'false' : 'true'
				);
			}
		}

		function setSearch(open) {
			if (!searchPanel) {
				return;
			}

			searchPanel.hidden = !open;

			searchButtons.forEach(function (button) {
				button.setAttribute(
					'aria-expanded',
					open ? 'true' : 'false'
				);
			});

			if (open) {
				var input = searchPanel.querySelector(
					'input[type="search"]'
				);

				if (input) {
					window.setTimeout(function () {
						input.focus();
					}, 50);
				}
			}
		}

		menuButtons.forEach(function (button) {
			if (button.dataset.mdcMenuBound === 'true') {
				return;
			}

			button.dataset.mdcMenuBound = 'true';

			button.addEventListener('click', function (event) {
				event.preventDefault();

				var open = body.classList.contains('mdc-menu-open');

				setMenu(!open);

				if (!open) {
					setSearch(false);
				}
			});
		});

		if (sideOverlay) {
			sideOverlay.addEventListener('click', function () {
				setMenu(false);
			});
		}

		document.querySelectorAll('.ns-side-panel__close').forEach(function (button) {
			button.addEventListener('click', function () {
				setMenu(false);
			});
		});

		searchButtons.forEach(function (button) {
			if (button.dataset.mdcSearchBound === 'true') {
				return;
			}

			button.dataset.mdcSearchBound = 'true';

			button.addEventListener('click', function (event) {
				event.preventDefault();

				if (!searchPanel) {
					return;
				}

				var open = searchPanel.hidden === true;

				setSearch(open);

				if (open) {
					setMenu(false);
				}
			});
		});

		document.addEventListener('keydown', function (event) {
			if (event.key !== 'Escape') {
				return;
			}

			setMenu(false);
			setSearch(false);
		});
	}

	function parseCountdownDate(value) {
		if (!value) {
			return null;
		}

		var normalized = value.trim();

		if (/^\d{2}\/\d{2}\/\d{4}$/.test(normalized)) {
			var parts = normalized.split('/');
			normalized =
				parts[2] + '-' +
				parts[1] + '-' +
				parts[0] + 'T00:00:00';
		}

		if (/^\d{4}-\d{2}-\d{2}$/.test(normalized)) {
			normalized += 'T00:00:00';
		}

		var date = new Date(normalized);

		if (Number.isNaN(date.getTime())) {
			return null;
		}

		return date;
	}

	function initCountdown() {
		var countdowns = document.querySelectorAll('[data-countdown]');

		if (!countdowns.length) {
			return;
		}

		function updateCountdowns() {
			var now = new Date();

			countdowns.forEach(function (item) {
				var targetString = item.getAttribute('data-countdown');
				var target = parseCountdownDate(targetString);
				var output = item.querySelector('[data-countdown-days]');

				if (!output || !target) {
					return;
				}

				var difference = target.getTime() - now.getTime();

				if (difference <= 0) {
					output.textContent = '0';
					item.classList.add('mdc-countdown__item--started');
					return;
				}

				item.classList.remove('mdc-countdown__item--started');

				var days = Math.ceil(
					difference / (1000 * 60 * 60 * 24)
				);

				output.textContent = days.toLocaleString('pt-BR');
			});
		}

		updateCountdowns();

		window.setInterval(updateCountdowns, 60 * 1000);
	}

	function init() {
		initTheme();
		initNavigation();
		initCountdown();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
