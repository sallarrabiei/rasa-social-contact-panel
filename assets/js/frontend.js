/**
 * Smart Social Contact Panel — Frontend Script
 *
 * Vanilla JS, IIFE-wrapped. No jQuery, no globals, no external dependencies.
 * Only manipulates CSS classes and ARIA attributes — never writes user-controlled
 * data into the DOM via innerHTML.
 */
(function () {
	'use strict';

	// -------------------------------------------------------------------------
	// State
	// -------------------------------------------------------------------------

	var isOpen                  = false;
	var focusedElementBeforeOpen = null;

	// -------------------------------------------------------------------------
	// DOM references (resolved once in init)
	// -------------------------------------------------------------------------

	var trigger      = null;
	var overlay      = null;
	var panel        = null;
	var closeBtn     = null;
	var firstFocusable = null;
	var lastFocusable  = null;

	// -------------------------------------------------------------------------
	// Selectors for focusable elements inside the panel
	// -------------------------------------------------------------------------

	var FOCUSABLE = [
		'a[href]',
		'button:not([disabled])',
		'input:not([disabled])',
		'select:not([disabled])',
		'textarea:not([disabled])',
		'[tabindex]:not([tabindex="-1"])',
	].join(', ');

	// -------------------------------------------------------------------------
	// Init
	// -------------------------------------------------------------------------

	function init() {
		trigger  = document.getElementById('sscp-trigger');
		overlay  = document.getElementById('sscp-overlay');
		panel    = document.getElementById('sscp-panel');
		closeBtn = document.getElementById('sscp-close');

		if (!trigger || !panel) return; // plugin disabled or elements missing

		// Apply position class to body so CSS can match panel position
		if (trigger.classList.contains('sscp-trigger--bottom-left')) {
			document.body.classList.add('sscp-pos-left');
		} else {
			document.body.classList.add('sscp-pos-right');
		}

		// Apply mobile full-width if data attribute set
		if (panel.dataset.mobileFullwidth === '1') {
			panel.classList.add('sscp-mobile-fullwidth');
		}

		cacheFocusableElements();
		bindEvents();
	}

	// -------------------------------------------------------------------------
	// Cache focusable elements (called once on init; re-cached on open)
	// -------------------------------------------------------------------------

	function cacheFocusableElements() {
		if (!panel) return;
		var nodes = panel.querySelectorAll(FOCUSABLE);
		firstFocusable = nodes.length ? nodes[0] : null;
		lastFocusable  = nodes.length ? nodes[nodes.length - 1] : null;
	}

	// -------------------------------------------------------------------------
	// Event binding
	// -------------------------------------------------------------------------

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

	// -------------------------------------------------------------------------
	// Open
	// -------------------------------------------------------------------------

	function openPanel() {
		if (isOpen) return;

		focusedElementBeforeOpen = document.activeElement;
		isOpen = true;

		// Update ARIA on trigger
		trigger.setAttribute('aria-expanded', 'true');

		// Show backdrop
		if (overlay) {
			overlay.classList.add('sscp-visible');
			overlay.setAttribute('aria-hidden', 'false');
		}

		// Show panel
		panel.classList.add('sscp-open');
		panel.setAttribute('aria-hidden', 'false');

		// Prevent body scroll
		document.body.classList.add('sscp-body-lock');

		// Re-cache focusable elements (cards may vary)
		cacheFocusableElements();

		// Move focus into panel
		if (firstFocusable) {
			// Small delay lets animation start first so focus ring is visible
			setTimeout(function () {
				firstFocusable.focus();
			}, 50);
		} else if (closeBtn) {
			setTimeout(function () {
				closeBtn.focus();
			}, 50);
		}
	}

	// -------------------------------------------------------------------------
	// Close
	// -------------------------------------------------------------------------

	function closePanel() {
		if (!isOpen) return;

		isOpen = false;

		// Update ARIA on trigger
		trigger.setAttribute('aria-expanded', 'false');

		// Hide backdrop
		if (overlay) {
			overlay.classList.remove('sscp-visible');
			overlay.setAttribute('aria-hidden', 'true');
		}

		// Hide panel
		panel.classList.remove('sscp-open');
		panel.setAttribute('aria-hidden', 'true');

		// Restore body scroll
		document.body.classList.remove('sscp-body-lock');

		// Return focus to the element that triggered the open
		if (focusedElementBeforeOpen && typeof focusedElementBeforeOpen.focus === 'function') {
			focusedElementBeforeOpen.focus();
		}
	}

	// -------------------------------------------------------------------------
	// Keyboard handler
	// -------------------------------------------------------------------------

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

	// -------------------------------------------------------------------------
	// Focus trap — keeps Tab/Shift+Tab cycling within the panel
	// -------------------------------------------------------------------------

	function trapFocus(e) {
		if (!firstFocusable || !lastFocusable) return;

		if (e.shiftKey) {
			// Shift+Tab: if on first element, wrap to last
			if (document.activeElement === firstFocusable) {
				e.preventDefault();
				lastFocusable.focus();
			}
		} else {
			// Tab: if on last element, wrap to first
			if (document.activeElement === lastFocusable) {
				e.preventDefault();
				firstFocusable.focus();
			}
		}
	}

	// -------------------------------------------------------------------------
	// Bootstrap — run after DOM is ready
	// -------------------------------------------------------------------------

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}

})();
