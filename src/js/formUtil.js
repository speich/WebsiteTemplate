/**
 * A Module providing helper methods to work with forms.
 * @module formUtil
 */
export const formUtil = {

    /**
     * Clear all form field values.
     * @param {HTMLFormElement} frm form element to clear fields
     * @param {Array} [exceptions=[]] ids of fields to skip
     */
    clearAll(frm, exceptions = []) {
        // 1. Array.from() handles the collection.
        // 2. Array.includes() replaces the entire _filterReferenced helper.
        const fields = Array.from(frm.elements).filter(
            fld => !exceptions.includes(fld.id)
        );

        for (const el of fields) {
            const nodeName = el.nodeName.toLowerCase();
            const type = el.type ? el.type.toLowerCase() : '';

            if (nodeName === 'input') {
                if (['text', 'password'].includes(type)) {
                    el.value = '';
                } else if (['checkbox', 'radio'].includes(type)) {
                    el.value = '';
                    el.checked = false;
                }
            } else if (nodeName === 'textarea') {
                el.value = '';
            } else if (nodeName === 'select') {
                el.selectedIndex = -1;
                // If it's a multi-select, safely uncheck all options
                if (el.multiple) {
                    Array.from(el.options).forEach(opt => opt.selected = false);
                }
            }
        }
    },

    /**
     * Set a form element to selected.
     * @param {HTMLInputElement|HTMLSelectElement|RadioNodeList} node form field
     * @param {String|Number|Array} [val] value of an option element to select
     */
    setSelected(node, val) {
        if (val == null) return; // Catches both null and undefined

        if (node instanceof RadioNodeList) {
            node.value = String(val);
        } else if (node.matches('input[type="checkbox"]')) {
            node.checked = true;
        } else if (node.matches('input, textarea')) {
            node.value = String(val);
        } else if (node.matches('select')) {
            this._setSelectedSelect(node, val);
        }
    },

    /**
     * @param {HTMLSelectElement} node
     * @param {string|number|array} val
     * @private
     */
    _setSelectedSelect(node, val) {
        const targetValues = new Set([val].flat().map(String));

        for (const option of Array.from(node.options)) {
            option.selected = targetValues.has(option.value);
        }
    },

    /**
     * Create an HTML select element.
     * @param {Object[]} data
     * @param {String} nameVal
     * @param {String} nameTxt
     * @param {String} [nameSelected]
     * @param {HTMLSelectElement} [el]
     * @return {HTMLSelectElement}
     */
    createSelect(data, nameVal, nameTxt, nameSelected, el) {
        const sel = el || document.createElement('select');

        data.forEach((item) => {
            const opt = document.createElement('option');
            opt.value = item[nameVal];
            opt.text = item[nameTxt];

            if (item[nameSelected] != null) {
                opt.selected = true;
            }
            sel.add(opt);
        });

        return sel;
    },

    /**
     * Create a radio element.
     * @param {String} id
     * @param {String} name
     * @param {String} value
     * @param {Boolean} [checked=false]
     * @returns {HTMLInputElement}
     */
    createInputRadio(id, name, value, checked = false) {
        const el = document.createElement('input');

        Object.assign(el, { id, type: 'radio', name, value, checked });

        return el;
    },

    /**
     * Set form elements to read-only.
     * @param {HTMLElement} el form field
     * @param {Boolean} readOnly
     */
    setReadOnly(el, readOnly) {
        if (!el) return;

        const isReadOnly = !!readOnly;
        const nodeName = el.nodeName.toUpperCase();
        const type = el.type ? el.type.toLowerCase() : '';

        // Selects, checkboxes, and radios do not support the readonly attribute natively.
        if (nodeName === 'SELECT' || ['checkbox', 'radio'].includes(type)) {
            if (isReadOnly) {
                // Arrow functions keep this clean
                el.onfocus = () => el.blur();
            } else {
                el.onfocus = null;
            }
        } else {
            el.readOnly = isReadOnly;
        }

        if (el.required && isReadOnly) {
            el.required = false;
        }

        el.classList.toggle('readOnly', isReadOnly);
    },

    /**
     * Set all form elements to readonly.
     * @param {HTMLFormElement} frm
     * @param {Boolean} [readOnly=true]
     */
    setReadOnlyAll(frm, readOnly = true) {
        // Array.from() allows us to use .forEach() directly on the form elements
        Array.from(frm.elements).forEach((el) => {
            if (el.nodeName.toUpperCase() !== 'FIELDSET') {
                this.setReadOnly(el, readOnly);
            }
        });
    },
};