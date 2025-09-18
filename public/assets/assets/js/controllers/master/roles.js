window.Laravel = {
    routes: {
        baseUrlPage : route('roles.index'),
        addData:route('roles.add')
    }
};
let Roles = {
    addData: () => {
        window.location.href = Laravel.routes.addData
    },
    homePage:()=>{
        window.location.href = Laravel.routes.baseUrlPage
    }
}

$(function () {

})
