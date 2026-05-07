(function () {
	'use strict';

	var isOpen                  = false;
	var focusedElementBeforeOpen = null;

	var trigger      = null;
	var overlay      = null;
	var panel        = null;
	var closeBtn     = null;
	var firstFocusable = null;
	var lastFocusable  = null;

	var FOCUSABLE = [
		'a[href]',
		'button:not([disabled])',
		'input:not([disabled])',
		'select:not([disabled])',
		'textarea:not([disabled])',
		'[tabindex]:not([tabindex="-1"])',
	].join(', ');

	function init() {
		trigger  = document.getElementById('wltsscp-trigger');
		overlay  = document.getElementById('wltsscp-overlay');
		panel    = document.getElementById('wltsscp-panel');
		closeBtn = document.getElementById('wltsscp-close');

		if (!trigger || !panel) return; // plugin disabled or elements missing
		if (trigger.classList.contains('wltsscp-trigger--bottom-left')) {
			document.body.classList.add('wltsscp-pos-left');
		} else {
			document.body.classList.add('wltsscp-pos-right');
		}
		if (panel.dataset.mobileFullwidth === '1') {
			panel.classList.add('wltsscp-mobile-fullwidth');
		}

		cacheFocusableElements();
		bindEvents();
	}

	function cacheFocusableElements() {
		if (!panel) return;
		var nodes = panel.querySelectorAll(FOCUSABLE);
		firstFocusable = nodes.length ? nodes[0] : null;
		lastFocusable  = nodes.length ? nodes[nodes.length - 1] : null;
	}

	function bindEvents() {
		trigger.addEventListener('click', openPanel);

		if (closeBtn) {
			closeBtn.addEventListener('click', closePanel);
		}

		if (overlay) {
			overlay.addEventListener('click', closePanel);
		}

		document.addEventListener('keydown', handleKeydown);
	}

	function openPanel() {
		if (isOpen) return;

		focusedElementBeforeOpen = document.activeElement;
		isOpen = true;
		trigger.setAttribute('aria-expanded', 'true');
		if (overlay) {
			overlay.classList.add('wltsscp-visible');
			overlay.setAttribute('aria-hidden', 'false');
		}
		panel.classList.add('wltsscp-open');
		panel.setAttribute('aria-hidden', 'false');
		document.body.classList.add('wltsscp-body-lock');
		cacheFocusableElements();
		if (firstFocusable) {
			setTimeout(function () {
				firstFocusable.focus();
			}, 50);
		} else if (closeBtn) {
			setTimeout(function () {
				closeBtn.focus();
			}, 50);
		}
	}

	function closePanel() {
		if (!isOpen) return;

		isOpen = false;
		trigger.setAttribute('aria-expanded', 'false');
		if (overlay) {
			overlay.classList.remove('wltsscp-visible');
			overlay.setAttribute('aria-hidden', 'true');
		}
		panel.classList.remove('wltsscp-open');
		panel.setAttribute('aria-hidden', 'true');
		document.body.classList.remove('wltsscp-body-lock');
		if (focusedElementBeforeOpen && typeof focusedElementBeforeOpen.focus === 'function') {
			focusedElementBeforeOpen.focus();
		}
	}

	function handleKeydown(e) {
		if (!isOpen) return;

		switch (e.key) {
			case 'Escape':
				e.preventDefault();
				closePanel();
				break;

			case 'Tab':
				trapFocus(e);
				break;
		}
	}

	function trapFocus(e) {
		if (!firstFocusable || !lastFocusable) return;

		if (e.shiftKey) {
			if (document.activeElement === firstFocusable) {
				e.preventDefault();
				lastFocusable.focus();
			}
		} else {
			if (document.activeElement === lastFocusable) {
				e.preventDefault();
				firstFocusable.focus();
			}
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

})();
