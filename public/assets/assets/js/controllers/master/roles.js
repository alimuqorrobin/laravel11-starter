window.Laravel = {
    routes: {
        baseUrlPage: route('roles.index'),
        addData: route('roles.add')
    }
}
let Roles = {
    addData: () => {
        window.location.href = Laravel.routes.addData
    },
    homePage: () => {
        window.location.href = Laravel.routes.baseUrlPage
    },
    initFormValidation: () => {
        initFormValidationGlobal([
            {
                key: 'roles-submit',
                formId: 'frm-rules',
                rowSelector: '.mb-4',
                rules: {
                    'txt-roles-name': {
                        validators: { ...RuleBuilder.required('txt-roles-name') }
                    },
                    'txt-keterangan': {
                        validators: { ...RuleBuilder.required('txt-keterangan') }
                    }
                },
                onSuccess: form => {}
            }
        ])
    },
    submitData: () => {
        submitFormValidation('roles-submit', () => {
            alert('lolos')
        })
    }
}

$(function () {
    Roles.initFormValidation()
})
