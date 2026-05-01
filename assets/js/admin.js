/* global sscpAdmin */
(function () {
	'use strict';

	/* ------------------------------------------------------------------
	   Platform accordion rows
	   ------------------------------------------------------------------ */

	function initAccordions() {
		document.querySelectorAll('.sscp-platform-row__header').forEach(function (header) {
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
		var row  = this.closest('.sscp-platform-row');
		var body = row.querySelector('.sscp-platform-row__body');
		var open = row.classList.contains('sscp-platform-row--open');

		if (open) {
			row.classList.remove('sscp-platform-row--open');
			body.hidden = true;
			this.setAttribute('aria-expanded', 'false');
		} else {
			row.classList.add('sscp-platform-row--open');
			body.hidden = false;
			this.setAttribute('aria-expanded', 'true');
		}
	}

	/* ------------------------------------------------------------------
	   Sort order — up/down arrow buttons
	   ------------------------------------------------------------------ */

	function initSortButtons() {
		var list = document.getElementById('sscp-platforms-list');
		if (!list) return;

		list.addEventListener('click', function (e) {
			var btn = e.target.closest('.sscp-sort-btn');
			if (!btn) return;
			e.stopPropagation();

			var row      = btn.closest('.sscp-platform-row');
			var isUp     = btn.classList.contains('sscp-sort-btn--up');
			var sibling  = isUp ? row.previousElementSibling : row.nextElementSibling;

			if (!sibling || !sibling.classList.contains('sscp-platform-row')) return;

			// Swap DOM positions
			if (isUp) {
				list.insertBefore(row, sibling);
			} else {
				list.insertBefore(sibling, row);
			}

			// Recalculate sort_order inputs
			reindexSortOrders();
		});
	}

	function reindexSortOrders() {
		var rows = document.querySelectorAll('.sscp-platform-row');
		rows.forEach(function (row, idx) {
			var input = row.querySelector('input[name$="[sort_order]"]');
			if (input) input.value = idx + 1;
		});
	}

	/* ------------------------------------------------------------------
	   Color picker — live hex value display
	   ------------------------------------------------------------------ */

	function initColorPickers() {
		document.querySelectorAll('input[type="color"]').forEach(function (input) {
			var display = input.nextElementSibling;
			if (!display || !display.classList.contains('sscp-color-value')) return;

			input.addEventListener('input', function () {
				display.textContent = input.value;
			});
		});
	}

	/* ------------------------------------------------------------------
	   "Use brand colors" checkbox for icon color
	   ------------------------------------------------------------------ */

	function initBrandColorToggle() {
		var cb    = document.getElementById('sscp-use-brand-colors');
		var field = document.getElementById('sscp-card-icon-color');
		if (!cb || !field) return;

		function sync() {
			field.disabled = cb.checked;
			// When brand colors re-enabled, clear the hidden field value
			// by setting to empty string so PHP sanitizer produces ''
			if (cb.checked) {
				field.dataset.savedValue = field.value;
				field.name = ''; // exclude from POST
			} else {
				field.name = 'sscp[card_icon_color]';
				if (field.dataset.savedValue) {
					field.value = field.dataset.savedValue;
				}
			}
		}

		cb.addEventListener('change', sync);
		sync();
	}

	/* ------------------------------------------------------------------
	   Reset to defaults
	   ------------------------------------------------------------------ */

	function initResetButton() {
		var btn = document.getElementById('sscp-reset-btn');
		if (!btn) return;

		btn.addEventListener('click', function () {
			if (!window.confirm(sscpAdmin.confirmReset)) return;

			// POST empty sscp array to trigger sanitize with all unchecked values,
			// which will fall back to defaults in PHP.
			var form = document.getElementById('sscp-settings-form');
			if (!form) return;

			// Temporarily disable all inputs so only the action + nonce post
			var inputs = form.querySelectorAll('input, textarea, select');
			inputs.forEach(function (el) {
				if (el.name !== 'action' && el.name !== 'sscp_nonce' && el.name !== 'sscp_current_tab') {
					el.disabled = true;
				}
			});

			// Add a reset flag
			var resetFlag = document.createElement('input');
			resetFlag.type  = 'hidden';
			resetFlag.name  = 'sscp_reset';
			resetFlag.value = '1';
			form.appendChild(resetFlag);

			form.submit();
		});
	}

	/* ------------------------------------------------------------------
	   Export settings as JSON download
	   ------------------------------------------------------------------ */

	function initExportButton() {
		var btn      = document.getElementById('sscp-export-btn');
		var textarea = document.getElementById('sscp-export-data');
		if (!btn || !textarea) return;

		btn.addEventListener('click', function () {
			var json = textarea.value;
			var blob = new Blob([json], { type: 'application/json' });
			var url  = URL.createObjectURL(blob);
			var a    = document.createElement('a');
			a.href     = url;
			a.download = 'sscp-settings-' + new Date().toISOString().slice(0, 10) + '.json';
			document.body.appendChild(a);
			a.click();
			document.body.removeChild(a);
			URL.revokeObjectURL(url);
		});
	}

	/* ------------------------------------------------------------------
	   Import settings from JSON file
	   ------------------------------------------------------------------ */

	function initImportButton() {
		var fileInput   = document.getElementById('sscp-import-file');
		var dataField   = document.getElementById('sscp-import-data');
		var fileNameEl  = document.getElementById('sscp-import-filename');
		var messageEl   = document.getElementById('sscp-import-message');
		if (!fileInput) return;

		fileInput.addEventListener('change', function () {
			var file = fileInput.files[0];
			if (!file) return;

			fileNameEl.textContent = file.name;

			var reader = new FileReader();
			reader.onload = function (evt) {
				try {
					var parsed = JSON.parse(evt.target.result);

					// Basic sanity check
					if (typeof parsed !== 'object' || Array.isArray(parsed) || !parsed.platforms) {
						throw new Error('invalid');
					}

					dataField.value   = evt.target.result;
					dataField.name    = 'sscp_import_json';
					messageEl.style.color = '#1a7431';
					messageEl.textContent = '✓ Valid settings file loaded. Click "Save Imported Settings" to apply.';
				} catch (err) {
					dataField.value   = '';
					dataField.name    = '';
					messageEl.style.color = '#cc1818';
					messageEl.textContent = sscpAdmin.importInvalid;
				}
			};
			reader.readAsText(file);
		});
	}

	/* ------------------------------------------------------------------
	   Keep sscp_current_tab in sync with active tab links
	   ------------------------------------------------------------------ */

	function initTabTracking() {
		var tabInput = document.getElementById('sscp-current-tab');
		if (!tabInput) return;

		document.querySelectorAll('.sscp-tab').forEach(function (link) {
			link.addEventListener('click', function () {
				var url    = new URL(link.href);
				var tab    = url.searchParams.get('tab') || 'general';
				tabInput.value = tab;
			});
		});
	}

	/* ------------------------------------------------------------------
	   Bootstrap
	   ------------------------------------------------------------------ */

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
