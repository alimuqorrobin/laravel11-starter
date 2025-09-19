let AjaxProcess = {
    // Helper: cek apakah string valid JSON
    isJsonString: str => {
        try {
            let obj = JSON.parse(str)
            return typeof obj === 'object'
        } catch (e) {
            return false
        }
    },

    /*
    ajaxRequest('/api/get-data', { id: 123 }, 'POST', function(res) {
        console.log("Response:", res);
    });
    */
    ajaxRequest: (url, data = {}, method = 'POST', callback = null) => {
        Swal.fire({
            title: 'Loading...',
            text: 'Mohon tunggu',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading()
            }
        })

        $.ajax({
            url: url,
            type: typeof method === 'string' ? method.toUpperCase() : 'POST',
            data: JSON.stringify(data), // stringify data
            contentType: 'application/json', // biarkan server tahu ini JSON
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')
                    .content
            },
            success: function (response) {
                Swal.close()
                if (typeof response === 'object') {
                    callback?.(response)
                } else if (AjaxProcess.isJsonString(response)) {
                    callback?.(JSON.parse(response))
                } else {
                    Swal.fire({
                        icon: 'error',
                        html: `<div style="max-height:400px; overflow:auto; text-align:left;">${response}</div>`,
                        width: '80%'
                    })
                }
            },
            error: function (xhr, status, error) {
                Swal.close()
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: `<div style="max-height:400px; overflow:auto; text-align:left;">${
                        xhr.responseText || error
                    }</div>`,
                    width: '80%'
                })
            }
        })
    },

    /*
    let formData = new FormData();
    formData.append("file", $("#fileInput")[0].files[0]);
    formData.append("user_id", 123);

    ajaxUpload('/api/upload', formData, function(res) {
        console.log("Upload sukses:", res);
    });
    */
    ajaxUpload: (url, formData, callback = null) => {
        Swal.fire({
            title: 'Uploading...',
            text: 'Mohon tunggu',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading()
            }
        })

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')
                    .content
            },
            success: function (response, status, xhr) {
                Swal.close()

                // cek apakah response JSON atau bukan
                if (typeof response === 'object') {
                    if (callback && typeof callback === 'function') {
                        callback(response)
                    }
                } else if (AjaxProcess.isJsonString(response)) {
                    let res = JSON.parse(response)
                    if (callback && typeof callback === 'function') {
                        callback(res)
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        html: `<div style="max-height:400px; overflow:auto; text-align:left;">${response}</div>`,
                        width: '80%'
                    })
                }
            },
            error: function (xhr, status, error) {
                Swal.close()
                Swal.fire({
                    icon: 'error',
                    title: 'Upload gagal',
                    html: `<div style="max-height:400px; overflow:auto; text-align:left;">${
                        xhr.responseText || error
                    }</div>`,
                    width: '80%'
                })
            }
        })
    }
}
