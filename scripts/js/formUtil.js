/**
 * @module NAFIDAS/formUtil
 */
define(function() {
	"use strict";

	/**
	 * A Module providing helper methods to work with numbers.
	 * @exports NAFIDAS/formUtil
	 */
	return {

		/**
		 * Clear all form filed values, e.g. set them to empty.
		 * fom.reset() sets the form back not to empty, but to the state before resetting,
		 * e.g. if form field values were not empty but had default values (set by PHP),
		 * these values are restored
		 * @param {HTMLFormElement} frm form element to clear fields
		 * @param {Array} [exceptions] ids of fields to skip
		 */
		clearAll: function(frm, exceptions) {
			var arr = [], el, j, i = 0, len = frm.length,
				arrSkip = exceptions || false;

			for (i; i < len; i++) {
				arr.push(frm[i]);
			}

			if (arrSkip) {
				arr = arr.filter(function(fld) {
					var notMatched = true, z = 0, lenZ = arrSkip.length;

					for (z; z < lenZ; z++) {
						if (fld.id === arrSkip[z]) {
							notMatched = false;
							break;
						}
					}
					return notMatched;
				});
			}

			len = arr.length;
			for (i = 0; i < len; i++) {
				el = arr[i];
				switch (el.nodeName.toLowerCase()) {
					case 'input':
						switch (el.getAttribute('type').toLowerCase()) {
							case 'text':
								el.value = '';
								break;
							case 'password':
								el.value = '';
								break;
							case 'checkbox':
								el.value = '';
								el.checked = false;
								break;
						}
						break;
					case 'textarea':
						el.value = '';
						break;
					case 'select':
						j = el.options.length - 1;
						for (j; j > -1; j--) {
							el.options[j].selected = false;
						}
						el.selectedIndex = -1;
						break;
				}
			}
		},

		/**
		 * Set a form element to selected.
		 * @param {HTMLElement} node form field
		 * @param {string} [val] value
		 */
		setSelected: function(node, val) 	{
			var j, i = 0, len = node.length;

			val = (val === 0 ? '0' : val);
			if (!node.nodeName) {  // e.g. radio group
				for (i = 0; i < len; i++) {
					if (node[i].value == val) {
						this.setSelected(node[i]);
						break;
					}
				}
			}
			else {
				switch (node.nodeName.toUpperCase()) {
					case 'INPUT':
						switch (node.getAttribute('type').toLowerCase()) {
							case 'checkbox':
								node.checked = true;
								break;
							case 'radio':
								node.checked = true;
								break;
						}
						break;
					case 'SELECT':
						j = node.options.length - 1;
						for (j; j > -1; j--) {
							if (node.options[j].value == val) {
								node.options[j].selected = true;
							}
						}
						break;
				}
			}
		},

		/**
		 * Set form elements to read only.
		 * The HTML attribute readonly is only valid on textarea, input type=text and type password.
		 * Make this work also for select and radio by removing focus.
		 * Note: readonly elements are posted whereas disabled are not.
		 * @param {HTMLElement} el form field
		 * @param {Boolean} readOnly
		 */
		setReadOnly: function(el, readOnly) {
			var j;

			if (!el) {
				return;
			}

			/**
			 * Sets the elements style property.
			 * @param {HTMLElement} el element
			 * @param {Boolean} readOnly
			 */
			function setStyle(el, readOnly) {
				if (readOnly) {
					el.style.backgroundColor = '#eeeeee';
					el.style.color = '#666666';
				}
				else {
					el.style.backgroundColor = 'inherit';
					el.style.color = 'inherit';
				}
			}

			switch (el.nodeName.toLowerCase()) {
				case 'input':
					switch (el.getAttribute('type').toLowerCase()) {
						case 'checkbox':
							el.onfocus = function() {
								this.blur();
							};
							break;
						case 'radio':
							if (readOnly) {
								el.onfocus = function() {
									this.blur();
								};
							}
							else {
								el.onfocus = null;
							}
							break;
						default:
							el.readOnly = !!readOnly;
					}
					break;
				case 'select':
					if (readOnly) {
						el.onfocus = function() {
							this.blur();
						};
					}
					else {
						el.onfocus = null;
					}
					j = el.options.length - 1;
					for (j; j > -1; j--) {
						el.options[j].style.backgroundColor = 'white';	// this is not set back correctly in SetStyle?
					}
					break;
				default:
					el.readOnly = !!readOnly;
			}

			setStyle(el, readOnly);
		},

		/**
		 * Set all form elements to readonly.
		 * @param {HTMLFormElement} frm
		 * @param {Boolean} [readOnly]
		 */
		setReadOnlyAll: function(frm, readOnly) {
			var i, flds = frm.elements, len = flds.length;

			for (i = 0; i < len; i++) {
				if (flds[i].nodeName.toLowerCase() !== 'fieldset') {
					readOnly = readOnly !== undefined ? readOnly : true;
					this.setReadOnly(flds[i], readOnly);
				}
			}
		}
	};
});
