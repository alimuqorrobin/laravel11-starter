<div class="row mb-3">
    <div class="col-xxl">
        <button type="button" class="btn btn-primary waves-effect waves-light"><span
                class="icon-base ri ri-sticky-note-add-fill icon-20px me-4"></span>Tambah</button>
    </div>
</div>
<div class="row mb-12 gy-12">
    <!-- Basic Layout -->
    <div class="col-xxl">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Roles User</h5>
                <small class="text-body-secondary float-end">Default label</small>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <a href="{{ route('roles.export.csv') }}" class="btn btn-success btn-sm"><span class="icon-base ri ri-file-3-fill icon-20px me-4"></span>Export CSV</a>
                    <a href="{{ route('roles.export.excel') }}" class="btn btn-primary btn-sm"><span class="icon-base ri ri-file-excel-2-fill icon-20px me-4"></span>Export Excel</a>
                </div>
                {!! $datatable !!}
            </div>
        </div>
    </div>
</div>
