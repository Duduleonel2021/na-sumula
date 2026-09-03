(function () {
	'use strict';

	const config = window.MDCInteractive || {};
	const ajaxUrl = config.ajaxUrl || (window.MDCTheme && window.MDCTheme.ajaxUrl) || '';

	function postAjax(data) {
		if (!ajaxUrl) return Promise.reject(new Error('AJAX indisponível.'));
		return fetch(ajaxUrl, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
			body: new URLSearchParams(data).toString()
		}).then(function (response) {
			return response.json();
		});
	}

	function initQuiz(root) {
		const dataNode = root.querySelector('.mdc-quiz__data');
		if (!dataNode) return;

		let questions = [];
		try { questions = JSON.parse(dataNode.textContent || '[]'); } catch (e) { return; }
		if (!Array.isArray(questions) || !questions.length) return;

		const counter = root.querySelector('[data-quiz-counter]');
		const progress = root.querySelector('[data-quiz-progress]');
		const questionNode = root.querySelector('[data-quiz-question]');
		const optionsNode = root.querySelector('[data-quiz-options]');
		const feedback = root.querySelector('[data-quiz-feedback]');
		const nextButton = root.querySelector('[data-quiz-next]');
		const stage = root.querySelector('[data-quiz-stage]');
		const result = root.querySelector('[data-quiz-result]');
		const scoreNode = root.querySelector('[data-quiz-score]');
		const messageNode = root.querySelector('[data-quiz-message]');
		const restartButton = root.querySelector('[data-quiz-restart]');
		if (!questionNode || !optionsNode || !stage || !result) return;

		let current = 0;
		let score = 0;
		let answers = [];
		let answered = false;

		function renderQuestion() {
			const item = questions[current];
			answered = false;
			questionNode.textContent = item.pergunta || '';
			optionsNode.innerHTML = '';
			if (feedback) { feedback.hidden = true; feedback.innerHTML = ''; }
			if (nextButton) nextButton.hidden = true;
			if (counter) counter.textContent = (current + 1) + ' de ' + questions.length;
			if (progress) progress.style.width = (((current + 1) / questions.length) * 100) + '%';

			Object.keys(item.opcoes || {}).forEach(function (letter) {
				const button = document.createElement('button');
				button.type = 'button';
				button.className = 'mdc-quiz__option';
				button.dataset.answer = letter;
				button.innerHTML = '<span class="mdc-quiz__letter">' + letter.toUpperCase() + '</span><span>' + escapeHtml(item.opcoes[letter]) + '</span>';
				button.addEventListener('click', function () { answer(letter, button); });
				optionsNode.appendChild(button);
			});
		}

		function answer(letter, selectedButton) {
			if (answered) return;
			answered = true;
			answers[current] = letter;
			const item = questions[current];
			const correct = letter === item.correta;
			if (correct) score++;

			optionsNode.querySelectorAll('button').forEach(function (button) {
				button.disabled = true;
				if (button.dataset.answer === item.correta) button.classList.add('is-correct');
				if (button === selectedButton && !correct) button.classList.add('is-wrong');
			});

			if (feedback) {
				feedback.hidden = false;
				feedback.className = 'mdc-interativo__feedback ' + (correct ? 'is-correct' : 'is-wrong');
				feedback.innerHTML = '<strong>' + (correct ? 'Resposta correta!' : 'Resposta incorreta.') + '</strong>' + (item.explicacao ? '<p>' + escapeHtml(item.explicacao) + '</p>' : '');
			}
			if (nextButton) {
				nextButton.hidden = false;
				nextButton.textContent = current === questions.length - 1 ? 'Ver resultado' : 'Próxima pergunta';
			}
		}

		function next() {
			if (!answered) return;
			if (current < questions.length - 1) {
				current++;
				renderQuestion();
				questionNode.focus && questionNode.focus();
			} else {
				finish();
			}
		}

		function finish() {
			stage.hidden = true;
			result.hidden = false;
			const pct = Math.round((score / questions.length) * 100);
			if (scoreNode) scoreNode.textContent = score + '/' + questions.length;
			if (messageNode) messageNode.textContent = pct >= 80 ? 'Mandou muito bem! Você conhece a história das Copas.' : (pct >= 50 ? 'Bom resultado. Mais uma rodada e você chega lá.' : 'Vale uma revisão na história dos Mundiais — e outra tentativa!');

			const postId = root.dataset.postId;
			const nonce = root.dataset.nonce;
			postAjax({ action: config.quizAction || 'mdc_quiz_resultado', post_id: postId, total: questions.length, answers: JSON.stringify(answers), nonce: nonce }).catch(function () {});
		}

		function restart() {
			current = 0;
			score = 0;
			answers = [];
			stage.hidden = false;
			result.hidden = true;
			renderQuestion();
		}

		if (nextButton) nextButton.addEventListener('click', next);
		if (restartButton) restartButton.addEventListener('click', restart);
		renderQuestion();
	}

	function initPoll(root) {
		const postId = root.dataset.postId;
		const nonce = root.dataset.nonce;
		const options = root.querySelectorAll('[data-enquete-option]');
		const results = root.querySelector('[data-enquete-results]');
		const resultsToggle = root.querySelector('[data-enquete-results-toggle]');
		const status = root.querySelector('[data-enquete-status]');
		if (!postId || !nonce || !options.length || !results || !resultsToggle) return;

		const storageKey = 'mdc_enquete_' + postId;
		let alreadyVoted = false;

		try {
			alreadyVoted = localStorage.getItem(storageKey) === '1';
		} catch (e) {}

		function showResults() {
			results.hidden = false;
			resultsToggle.textContent = 'Ocultar resultado parcial';
			resultsToggle.setAttribute('aria-expanded', 'true');
		}

		function hideResults() {
			results.hidden = true;
			resultsToggle.textContent = 'Ver resultado parcial';
			resultsToggle.setAttribute('aria-expanded', 'false');
		}

		resultsToggle.setAttribute('aria-expanded', 'false');
		resultsToggle.addEventListener('click', function () {
			if (results.hidden) {
				showResults();
			} else {
				hideResults();
			}
		});

		if (alreadyVoted) {
			options.forEach(function (button) { button.disabled = true; });
			if (status) status.textContent = 'Você já participou desta enquete.';
			// The partial result remains hidden until the reader asks for it.
			hideResults();
		}

		options.forEach(function (button) {
			button.addEventListener('click', function () {
				if (alreadyVoted) return;

				options.forEach(function (item) { item.disabled = true; });
				if (status) status.textContent = 'Registrando seu voto…';

				postAjax({
					action: config.voteAction || 'mdc_enquete_vote',
					post_id: postId,
					opcao: button.dataset.enqueteOption,
					nonce: nonce
				})
					.then(function (response) {
						if (!response || !response.success) throw new Error('Falha no voto');

						const percent = response.data.percent || {};
						Object.keys(percent).forEach(function (letter) {
							const percentNode = root.querySelector('[data-enquete-percent="' + letter + '"]');
							const bar = root.querySelector('[data-enquete-bar="' + letter + '"]');
							if (percentNode) percentNode.textContent = percent[letter] + '%';
							if (bar) bar.style.width = percent[letter] + '%';
						});

						alreadyVoted = true;
						try { localStorage.setItem(storageKey, '1'); } catch (e) {}

						if (status) status.textContent = 'Voto registrado.';
						// Do not expose the partial result automatically.
						hideResults();
					})
					.catch(function () {
						options.forEach(function (item) { item.disabled = false; });
						if (status) status.textContent = 'Não foi possível registrar agora. Tente novamente.';
					});
			});
		});
	}

	function escapeHtml(value) {
		const div = document.createElement('div');
		div.textContent = value == null ? '' : String(value);
		return div.innerHTML;
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('[data-mdc-quiz]').forEach(initQuiz);
		document.querySelectorAll('[data-mdc-enquete]').forEach(initPoll);
	});
})();
