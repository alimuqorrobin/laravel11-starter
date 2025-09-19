window.Laravel = {
    routes: {
        baseUrlPage: route('roles.index'),
        addData: route('roles.add'),
        saveData: route('roles.save')
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
            let params = {}
            params.roles = $("#txt-roles-name").val()
            params.keterangan = $("#txt-keterangan").val()
            const url_save = Laravel.routes.saveData
            const postData = AjaxProcess.ajaxRequest(url_save,params,'POST',function(response){
                if(response.is_valid){
                    MessageDialog.showSuccess('Data Succesfully Saved');
                }else{
                    MessageDialog.showError(response.message);
                }
            });
        });
    }
}

$(function () {
    Roles.initFormValidation()
})
