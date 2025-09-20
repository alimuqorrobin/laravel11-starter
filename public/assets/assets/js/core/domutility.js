const DOMUtil = (() => {
    const getElement = (selector) => {
        if (!selector) return null;
        if (selector.startsWith("#")) {
            return document.getElementById(selector.slice(1));
        } else {
            return document.getElementsByName(selector);
        }
    };

    return {
        // Ambil value (single atau multiple)
        getValue: (selector) => {
            const el = getElement(selector);
            if (!el) return null;

            if (el instanceof HTMLCollection || el instanceof NodeList) {
                return Array.from(el).map(i => {
                    if ($(i).hasClass('select2-hidden-accessible')) {
                        return $(i).val();
                    }
                    return i.value;
                });
            }

            if ($(el).hasClass('select2-hidden-accessible')) {
                return $(el).val();
            }
            return el.value;
        },

        // Set value single atau multiple (obj: {selector: value})
        setValue: (selectorOrObj, value) => {
            if (typeof selectorOrObj === 'object') {
                for (const key in selectorOrObj) {
                    DOMUtil.setValue(key, selectorOrObj[key]);
                }
                return;
            }

            const el = getElement(selectorOrObj);
            if (!el) return;

            if (el instanceof HTMLCollection || el instanceof NodeList) {
                Array.from(el).forEach(i => {
                    if ($(i).hasClass('select2-hidden-accessible')) {
                        $(i).val(value).trigger('change');
                    } else {
                        i.value = value;
                    }
                });
            } else {
                if ($(el).hasClass('select2-hidden-accessible')) {
                    $(el).val(value).trigger('change');
                } else {
                    el.value = value;
                }
            }
        },

        // Disable / enable single atau multiple input
        setDisabled: (selectorOrArray, disabled = true) => {
            if (Array.isArray(selectorOrArray)) {
                selectorOrArray.forEach(s => DOMUtil.setDisabled(s, disabled));
                return;
            }
            const el = getElement(selectorOrArray);
            if (!el) return;

            if (el instanceof HTMLCollection || el instanceof NodeList) {
                Array.from(el).forEach(i => {
                    if ($(i).hasClass('select2-hidden-accessible')) {
                        $(i).prop('disabled', disabled).trigger('change');
                    } else {
                        i.disabled = disabled;
                    }
                });
            } else {
                if ($(el).hasClass('select2-hidden-accessible')) {
                    $(el).prop('disabled', disabled).trigger('change');
                } else {
                    el.disabled = disabled;
                }
            }
        },

        // Clear semua input di form
        clearForm: (formSelector) => {
            const form = document.querySelector(formSelector);
            if (!form) return;

            Array.from(form.elements).forEach(el => {
                if ($(el).hasClass('select2-hidden-accessible')) {
                    $(el).val(null).trigger('change');
                } else if (el.type === 'checkbox' || el.type === 'radio') {
                    el.checked = false;
                } else {
                    el.value = '';
                }
            });
        },

        // Inisialisasi Select2 (normal / remote AJAX) + dropdownParent otomatis
        initSelect2: (selector, options = {}) => {
            const el = $(selector);
            if (!el.length) return;

            let config = {
                width: '100%',
                // cari parent modal / dropdown / tab terdekat
                dropdownParent: el.closest('.modal, .dropdown, .tab-pane').length ?
                                el.closest('.modal, .dropdown, .tab-pane') : $(document.body),
                ...options
            };

            // jika remote AJAX
            if (options.ajax && options.url) {
                config.ajax = {
                    url: options.url,
                    type: options.type || 'POST',
                    dataType: 'json',
                    delay: options.delay || 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            ...options.data // custom additional params
                        };
                    },
                    processResults: function (data) {
                        return { results: data }; // harus {id, text} array
                    },
                    cache: true
                };
            }

            el.select2(config);
        }
    };
})();

/* cara penggunaan 
// --- Ambil / set value ---
let name = DOMUtil.getValue('#nameInput');       // ambil by id
let tags = DOMUtil.getValue('tags');             // ambil by name

DOMUtil.setValue('#nameInput', 'Ali');          // set single
DOMUtil.setValue({                               // set multiple
    '#nameInput': 'Ali',
    'tags': 'javascript',
    '#email': 'ali@example.com'
});

// --- Disable / enable ---
DOMUtil.setDisabled('#nameInput');             // disable
DOMUtil.setDisabled(['#nameInput','tags']);    // disable banyak
DOMUtil.setDisabled('#nameInput', false);      // enable

// --- Clear form ---
DOMUtil.clearForm('#myForm');

// --- Inisialisasi Select2 ---
DOMUtil.initSelect2('#category');              // normal select2

// --- Select2 remote AJAX ---
DOMUtil.initSelect2('#category', {
    url: '/api/get-categories',
    type: 'POST',
    data: { extraParam: 123 },
    delay: 300
});
*/