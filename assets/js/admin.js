(function () {
	'use strict';

	function initAccordions() {
		document.querySelectorAll('.wltsscp-platform-row__header').forEach(function (header) {
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
		var row  = this.closest('.wltsscp-platform-row');
		var body = row.querySelector('.wltsscp-platform-row__body');
		var open = row.classList.contains('wltsscp-platform-row--open');

		if (open) {
			row.classList.remove('wltsscp-platform-row--open');
			body.hidden = true;
			this.setAttribute('aria-expanded', 'false');
		} else {
			row.classList.add('wltsscp-platform-row--open');
			body.hidden = false;
			this.setAttribute('aria-expanded', 'true');
		}
	}

	function initSortButtons() {
		var list = document.getElementById('wltsscp-platforms-list');
		if (!list) return;

		list.addEventListener('click', function (e) {
			var btn = e.target.closest('.wltsscp-sort-btn');
			if (!btn) return;
			e.stopPropagation();

			var row      = btn.closest('.wltsscp-platform-row');
			var isUp     = btn.classList.contains('wltsscp-sort-btn--up');
			var sibling  = isUp ? row.previousElementSibling : row.nextElementSibling;

			if (!sibling || !sibling.classList.contains('wltsscp-platform-row')) return;
			if (isUp) {
				list.insertBefore(row, sibling);
			} else {
				list.insertBefore(sibling, row);
			}
			reindexSortOrders();
		});
	}

	function reindexSortOrders() {
		var rows = document.querySelectorAll('.wltsscp-platform-row');
		rows.forEach(function (row, idx) {
			var input = row.querySelector('input[name$="[sort_order]"]');
			if (input) input.value = idx + 1;
		});
	}

	function initColorPickers() {
		document.querySelectorAll('input[type="color"]').forEach(function (input) {
			var display = input.nextElementSibling;
			if (!display || !display.classList.contains('wltsscp-color-value')) return;

			input.addEventListener('input', function () {
				display.textContent = input.value;
			});
		});
	}

	function initBrandColorToggle() {
		var cb    = document.getElementById('wltsscp-use-brand-colors');
		var field = document.getElementById('wltsscp-card-icon-color');
		if (!cb || !field) return;

		function sync() {
			field.disabled = cb.checked;
			if (cb.checked) {
				field.dataset.savedValue = field.value;
				field.name = '';
			} else {
				field.name = 'wltsscp[card_icon_color]';
				if (field.dataset.savedValue) {
					field.value = field.dataset.savedValue;
				}
			}
		}

		cb.addEventListener('change', sync);
		sync();
	}

	function initResetButton() {
		var btn = document.getElementById('wltsscp-reset-btn');
		if (!btn) return;

		btn.addEventListener('click', function () {
			if (!window.confirm(wltsscpAdmin.confirmReset)) return;
			var form = document.getElementById('wltsscp-settings-form');
			if (!form) return;
			var inputs = form.querySelectorAll('input, textarea, select');
			inputs.forEach(function (el) {
				if (el.name !== 'action' && el.name !== 'wltsscp_nonce' && el.name !== 'wltsscp_current_tab') {
					el.disabled = true;
				}
			});
			var resetFlag = document.createElement('input');
			resetFlag.type  = 'hidden';
			resetFlag.name  = 'wltsscp_reset';
			resetFlag.value = '1';
			form.appendChild(resetFlag);

			form.submit();
		});
	}

	function initExportButton() {
		var btn      = document.getElementById('wltsscp-export-btn');
		var textarea = document.getElementById('wltsscp-export-data');
		if (!btn || !textarea) return;

		btn.addEventListener('click', function () {
			var json = textarea.value;
			var blob = new Blob([json], { type: 'application/json' });
			var url  = URL.createObjectURL(blob);
			var a    = document.createElement('a');
			a.href     = url;
			a.download = 'wltsscp-settings-' + new Date().toISOString().slice(0, 10) + '.json';
			document.body.appendChild(a);
			a.click();
			document.body.removeChild(a);
			URL.revokeObjectURL(url);
		});
	}

	function initImportButton() {
		var fileInput   = document.getElementById('wltsscp-import-file');
		var dataField   = document.getElementById('wltsscp-import-data');
		var fileNameEl  = document.getElementById('wltsscp-import-filename');
		var messageEl   = document.getElementById('wltsscp-import-message');
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
					dataField.name    = 'wltsscp_import_json';
					messageEl.style.color = '#1a7431';
					messageEl.textContent = wltsscpAdmin.importLoaded;
				} catch (err) {
					dataField.value   = '';
					dataField.name    = '';
					messageEl.style.color = '#cc1818';
					messageEl.textContent = wltsscpAdmin.importInvalid;
				}
			};
			reader.readAsText(file);
		});
	}

	function initTabTracking() {
		var tabInput = document.getElementById('wltsscp-current-tab');
		if (!tabInput) return;

		document.querySelectorAll('.wltsscp-tab').forEach(function (link) {
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
