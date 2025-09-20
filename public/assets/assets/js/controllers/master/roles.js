window.Laravel = {
    routes: {
        baseUrlPage: route('roles.index'),
        addData: route('roles.add'),
        saveData: route('roles.save'),
        editPage: route('roles.edit')
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
            },
            {
                key: 'roles-update-data',
                formId: 'frm-rules-edit',
                rowSelector: '.mb-4',
                rules: {
                    'txt-roles-name-edit': {
                        validators: { ...RuleBuilder.required('txt-roles-name-edit') }
                    },
                    'txt-keterangan-edit': {
                        validators: { ...RuleBuilder.required('txt-keterangan-edit') }
                    }
                },
                onSuccess: form => {}
            },
        ])
    },
    submitData: () => {
        submitFormValidation('roles-submit', () => {
            let params              = {}
            params.roles            = DOMUtil.getValue("#txt-roles-name");
            params.keterangan       = DOMUtil.getValue("#txt-keterangan"); 
            const url_save          = Laravel.routes.saveData
            const postData          = AjaxProcess.ajaxRequest(url_save,params,'POST',function(response){
                if(response.is_valid){
                    MessageDialog.showSuccess('Data Succesfully Saved');
                    DOMUtil.clearForm('#frm-rules');
                }else{
                    MessageDialog.showError(response.message);
                }
            });
        });
    },
    editPage:(id)=>{
        const urlPageEdit = Laravel.routes.editPage+`?id=${id}`
        window.location.href = urlPageEdit
    },

    updateData:()=>{
        submitFormValidation('roles-update-data', () => {
            let params              = {}
            params.roles            = DOMUtil.getValue("#txt-roles-name-edit");
            params.keterangan       = DOMUtil.getValue("#txt-keterangan-edit"); 
            const url_save          = Laravel.routes.saveData
            const postData          = AjaxProcess.ajaxRequest(url_save,params,'POST',function(response){
                if(response.is_valid){
                    MessageDialog.showSuccess('Data Succesfully Saved');
                    DOMUtil.clearForm('#frm-rules-edit');
                }else{
                    MessageDialog.showError(response.message);
                }
            });
        });
    },
}

$(function () {
    Roles.initFormValidation();
})
