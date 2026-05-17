(function () {
	'use strict';

	function initAccordions() {
		document.querySelectorAll('.rasascp-platform-row__header').forEach(function (header) {
			header.addEventListener('click', toggleRow);
			header.addEventListener('keydown', function (e) {
				if (e.key === 'Enter' || e.key === ' ') {
					e.preventDefault();
					toggleRow.call(header, e);
				}
			});
		});
	}

	function toggleRow() {
		var row  = this.closest('.rasascp-platform-row');
		var body = row.querySelector('.rasascp-platform-row__body');
		var open = row.classList.contains('rasascp-platform-row--open');

		if (open) {
			row.classList.remove('rasascp-platform-row--open');
			body.hidden = true;
			this.setAttribute('aria-expanded', 'false');
		} else {
			row.classList.add('rasascp-platform-row--open');
			body.hidden = false;
			this.setAttribute('aria-expanded', 'true');
		}
	}

	function initSortButtons() {
		var list = document.getElementById('rasascp-platforms-list');
		if (!list) return;

		list.addEventListener('click', function (e) {
			var btn = e.target.closest('.rasascp-sort-btn');
			if (!btn) return;
			e.stopPropagation();

			var row      = btn.closest('.rasascp-platform-row');
			var isUp     = btn.classList.contains('rasascp-sort-btn--up');
			var sibling  = isUp ? row.previousElementSibling : row.nextElementSibling;

			if (!sibling || !sibling.classList.contains('rasascp-platform-row')) return;
			if (isUp) {
				list.insertBefore(row, sibling);
			} else {
				list.insertBefore(sibling, row);
			}
			reindexSortOrders();
		});
	}

	function reindexSortOrders() {
		var rows = document.querySelectorAll('.rasascp-platform-row');
		rows.forEach(function (row, idx) {
			var input = row.querySelector('input[name$="[sort_order]"]');
			if (input) input.value = idx + 1;
		});
	}

	function initColorPickers() {
		document.querySelectorAll('input[type="color"]').forEach(function (input) {
			var display = input.nextElementSibling;
			if (!display || !display.classList.contains('rasascp-color-value')) return;

			input.addEventListener('input', function () {
				display.textContent = input.value;
			});
		});
	}

	function initBrandColorToggle() {
		var cb    = document.getElementById('rasascp-use-brand-colors');
		var field = document.getElementById('rasascp-card-icon-color');
		if (!cb || !field) return;

		function sync() {
			field.disabled = cb.checked;
			if (cb.checked) {
				field.dataset.savedValue = field.value;
				field.name = '';
			} else {
				field.name = 'rasascp[card_icon_color]';
				if (field.dataset.savedValue) {
					field.value = field.dataset.savedValue;
				}
			}
		}

		cb.addEventListener('change', sync);
		sync();
	}

	function initResetButton() {
		var btn = document.getElementById('rasascp-reset-btn');
		if (!btn) return;

		btn.addEventListener('click', function () {
			if (!window.confirm(rasascpAdmin.confirmReset)) return;
			var form = document.getElementById('rasascp-settings-form');
			if (!form) return;
			var inputs = form.querySelectorAll('input, textarea, select');
			inputs.forEach(function (el) {
				if (el.name !== 'action' && el.name !== 'rasascp_nonce' && el.name !== 'rasascp_current_tab') {
					el.disabled = true;
				}
			});
			var resetFlag = document.createElement('input');
			resetFlag.type  = 'hidden';
			resetFlag.name  = 'rasascp_reset';
			resetFlag.value = '1';
			form.appendChild(resetFlag);

			form.submit();
		});
	}

	function initExportButton() {
		var btn      = document.getElementById('rasascp-export-btn');
		var textarea = document.getElementById('rasascp-export-data');
		if (!btn || !textarea) return;

		btn.addEventListener('click', function () {
			var json = textarea.value;
			var blob = new Blob([json], { type: 'application/json' });
			var url  = URL.createObjectURL(blob);
			var a    = document.createElement('a');
			a.href     = url;
			a.download = 'rasascp-settings-' + new Date().toISOString().slice(0, 10) + '.json';
			document.body.appendChild(a);
			a.click();
			document.body.removeChild(a);
			URL.revokeObjectURL(url);
		});
	}

	function initImportButton() {
		var fileInput   = document.getElementById('rasascp-import-file');
		var dataField   = document.getElementById('rasascp-import-data');
		var fileNameEl  = document.getElementById('rasascp-import-filename');
		var messageEl   = document.getElementById('rasascp-import-message');
		if (!fileInput) return;

		fileInput.addEventListener('change', function () {
			var file = fileInput.files[0];
			if (!file) return;

			fileNameEl.textContent = file.name;

			var reader = new FileReader();
			reader.onload = function (evt) {
				try {
					var parsed = JSON.parse(evt.target.result);
					if (typeof parsed !== 'object' || Array.isArray(parsed) || !parsed.platforms) {
						throw new Error('invalid');
					}

					dataField.value   = evt.target.result;
					dataField.name    = 'rasascp_import_json';
					messageEl.style.color = '#1a7431';
					messageEl.textContent = rasascpAdmin.importLoaded;
				} catch (err) {
					dataField.value   = '';
					dataField.name    = '';
					messageEl.style.color = '#cc1818';
					messageEl.textContent = rasascpAdmin.importInvalid;
				}
			};
			reader.readAsText(file);
		});
	}

	function initTabTracking() {
		var tabInput = document.getElementById('rasascp-current-tab');
		if (!tabInput) return;

		document.querySelectorAll('.rasascp-tab').forEach(function (link) {
			link.addEventListener('click', function () {
				var url    = new URL(link.href);
				var tab    = url.searchParams.get('tab') || 'general';
				tabInput.value = tab;
			});
		});
	}

	function init() {
		initAccordions();
		initSortButtons();
		initColorPickers();
		initBrandColorToggle();
		initResetButton();
		initExportButton();
		initImportButton();
		initTabTracking();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
