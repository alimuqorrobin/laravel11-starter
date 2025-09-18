let MessageDialog = {
    // Success alert
    /*
    showSuccess("Data berhasil disimpan!", () => {
        console.log("Lanjut ke halaman lain...");
        window.location.href = "/dashboard";
    });
    */
    showSuccess (message, callback = null) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: message,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        }).then(() => {
            if (typeof callback === 'function') {
                callback()
            }
        })
    },

    // Error alert
    /*
    showError("Gagal menyimpan data!", () => {
        console.log("User sudah membaca pesan error.");
    });
    */
    showError (message, callback = null) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: message,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Tutup'
        }).then(() => {
            if (typeof callback === 'function') {
                callback()
            }
        })
    },

    // Confirmation alert
    /*
    showConfirm("Apakah Anda yakin ingin menghapus data ini?", function() {
        console.log("Data dihapus!");
        // jalankan ajax / fetch delete di sini
    });
    */
    showConfirm: (text, callback) => {
        Swal.fire({
            title: 'Konfirmasi',
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        }).then(result => {
            if (result.isConfirmed && typeof callback === 'function') {
                callback()
            }
        })
    }
}
