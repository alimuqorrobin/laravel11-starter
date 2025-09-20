const DOMUtil = (() => {
    const getElement = (selector) => {
        if (!selector) return null;
        if (selector.startsWith("#")) {
            return document.getElementById(selector.slice(1));
        } else {
            return document.getElementsByName(selector);
        }
    };

    const setSelect2Value = (el, value, multiple = false) => {
        if (!el) return;
        const $el = $(el);
        if (!$el.hasClass('select2-hidden-accessible')) return;

        // Jika remote AJAX, kita perlu membuat data dummy untuk ditampilkan
        if ($el.data('select2') && $el.data('select2').options.options.ajax) {
            if (!value) {
                $el.val(null).trigger('change');
                return;
            }

            const values = multiple ? value : [value];

            // Cek option yang sudah ada, jika belum ada buat dummy
            const currentOptions = $el.find('option').map((i, opt) => opt.value).get();
            values.forEach(v => {
                if (!currentOptions.includes(v.id ? v.id.toString() : v.toString())) {
                    const newOption = new Option(v.text || v.id || v, v.id || v, true, true);
                    $el.append(newOption).trigger('change');
                }
            });
            $el.trigger({
                type: 'select2:select',
                params: { data: values }
            });
        } else {
            // non-ajax
            $el.val(value).trigger('change');
        }
    };

    return {
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
                Array.from(el).forEach(i => setSelect2Value(i, value));
            } else {
                setSelect2Value(el, value);
            }
        },

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

        initSelect2: (selector, options = {}) => {
            const el = $(selector);
            if (!el.length) return;

            let config = {
                width: '100%',
                dropdownParent: el.closest('.modal, .dropdown, .tab-pane').length ?
                                el.closest('.modal, .dropdown, .tab-pane') : $(document.body),
                ...options
            };

            if (options.ajax && options.url) {
                config.ajax = {
                    url: options.url,
                    type: options.type || 'POST',
                    dataType: 'json',
                    delay: options.delay || 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            ...options.data
                        };
                    },
                    processResults: function (data) {
                        return { results: data }; // harus {id, text} array
                    },
                    cache: true
                };
            }

            el.select2(config);
        },

        // --- FUNGSI BARU UNTUK AJAX REMOTE SELECT2 EDIT ---
        setAjaxSelect2Value: (selector, data) => {
            const el = getElement(selector);
            setSelect2Value(el, data, Array.isArray(data));
        }
    };
})();


/* ===============================
   DOMUtil Usage Cheat Sheet
   =============================== */

// --- Ambil / set value ---
/*
let name = DOMUtil.getValue('#nameInput');       // ambil by id
let tags = DOMUtil.getValue('tags');             // ambil by name

DOMUtil.setValue('#nameInput', 'Ali');          // set single
DOMUtil.setValue({                               // set multiple
    '#nameInput': 'Ali',
    'tags': 'javascript',
    '#email': 'ali@example.com'
});

// --- Set value untuk Select2 remote AJAX (edit data) ---
DOMUtil.setAjaxSelect2Value('#category', { id: 5, text: 'Elektronik' });   // single
DOMUtil.setAjaxSelect2Value('#tags', [                                       // multiple
    { id: 1, text: 'JavaScript' },
    { id: 3, text: 'PHP' },
    { id: 5, text: 'Laravel' }
]);

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

// --- Contoh lengkap: edit form dengan AJAX remote Select2 ---
$.get('/api/order/123', function(order) {
    DOMUtil.setValue('#nameInput', order.name);                   // single input
    DOMUtil.setAjaxSelect2Value('#category', order.category);     // single Select2 AJAX
    DOMUtil.setAjaxSelect2Value('#tags', order.tags);             // multiple Select2 AJAX
});
*/