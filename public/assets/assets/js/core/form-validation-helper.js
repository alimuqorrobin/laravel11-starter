// 🔧 Ambil teks label berdasarkan name/id
function getLabelText(fieldName) {
    const input = document.querySelector(`[name="${fieldName}"]`);
    if (!input) return 'Field';

    const id = input.getAttribute('id');
    let label = null;

    if (id) {
        // Cari label dengan for
        label = document.querySelector(`label[for="${id}"]`);
    }

    // Kalau belum ketemu, coba cari di parent row / form-group
    if (!label) {
        const row = input.closest('.row, .form-group');
        if (row) {
            label = row.querySelector('label');
        }
    }

    return label ? label.textContent.trim() : 'Field';
}

/**
 * RuleBuilder: helper untuk bikin rules validasi
 * Semua pesan otomatis ambil teks label field
 */
const RuleBuilder = {
    required: (field, msg) => ({
        notEmpty: { message: msg || `${getLabelText(field)} wajib diisi` }
    }),
    minLength: (field, min, msg) => ({
        stringLength: { min, message: msg || `${getLabelText(field)} minimal ${min} karakter` }
    }),
    maxLength: (field, max, msg) => ({
        stringLength: { max, message: msg || `${getLabelText(field)} maksimal ${max} karakter` }
    }),
    rangeLength: (field, min, max, msg) => ({
        stringLength: { min, max, message: msg || `${getLabelText(field)} panjang ${min}-${max} karakter` }
    }),
    email: (field, msg) => ({
        emailAddress: { message: msg || `${getLabelText(field)} tidak valid` }
    }),
    numeric: (field, msg) => ({
        regexp: { regexp: /^[0-9]+$/, message: msg || `${getLabelText(field)} hanya boleh angka` }
    }),
    alphabet: (field, msg) => ({
        regexp: { regexp: /^[A-Za-z\s]+$/, message: msg || `${getLabelText(field)} hanya boleh huruf` }
    }),
    phoneID: (field, msg) => ({
        regexp: { regexp: /^08[0-9]{8,11}$/, message: msg || `${getLabelText(field)} tidak valid` }
    }),
    strongPassword: (field, msg) => ({
        regexp: {
            regexp: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/,
            message: msg || `${getLabelText(field)} harus ada huruf besar, kecil, angka, dan simbol`
        }
    }),
    matchField: (field, compareField, msg) => ({
        identical: {
            compare: () => document.querySelector(`[name="${compareField}"]`).value,
            message: msg || `${getLabelText(field)} harus sama dengan ${getLabelText(compareField)}`
        }
    }),
    regex: (field, pattern, msg) => ({
        regexp: { regexp: pattern, message: msg || `${getLabelText(field)} format tidak valid` }
    }),
    nik: (field, msg) => ({
        regexp: { regexp: /^[0-9]{16}$/, message: msg || `${getLabelText(field)} harus 16 digit angka` }
    }),
    npwp: (field, msg) => ({
        regexp: { regexp: /^[0-9]{15}$|^\d{2}\.\d{3}\.\d{3}\.\d{1}-\d{3}\.\d{3}$/, message: msg || `${getLabelText(field)} tidak valid` }
    }),
};

/**
 * setupValidation: inisialisasi form validation
 */
function setupValidation(formId, rules, onSuccess, rowSelector = '.mb-3') {
    const form = document.getElementById(formId);
    if (!form) return null;

    const fv = FormValidation.formValidation(form, {
        fields: rules,
        plugins: {
            trigger: new FormValidation.plugins.Trigger(),
            bootstrap5: new FormValidation.plugins.Bootstrap5({
                rowSelector: rowSelector,
                eleInvalidClass: '',
                eleValidClass: ''
            }),
            submitButton: new FormValidation.plugins.SubmitButton(),
        }
    })
    .on('core.form.invalid', () => {
        const firstError = form.querySelector('.is-invalid');
        if (firstError) firstError.focus();
    })
    .on('core.form.valid', () => {
        if (typeof onSuccess === 'function') {
            onSuccess(form);
        }
    });

    return fv;
}
/*
<form id="formCustom">
    <div class="form-group mb-2">
        <label>Username</label>
        <input type="text" name="username" class="form-control"/>
    </div>
    <div class="form-group mb-2">
        <label>Password</label>
        <input type="password" name="password" class="form-control"/>
    </div>
    <button class="btn btn-success" type="submit">Login</button>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    setupValidation(
        'formCustom',
        {
            username: { validators: { ...RuleBuilder.required() } },
            password: { validators: { ...RuleBuilder.required(), ...RuleBuilder.minLength(6) } },
        },
        function(form) {
            alert("Login valid ✅");
        },
        '.form-group' // ✅ pakai rowSelector custom
    );
});
</script>
*/