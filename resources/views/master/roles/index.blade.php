<div class="row mb-3">
    <div class="col-xxl">
        <button onclick="Roles.addData()" type="button" class="btn btn-primary waves-effect waves-light"><span
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
                    <select class="form-select w-25" data-filter="status">
                        <option value="">-- Semua Status --</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
                {!! \App\Helpers\DataTableHelper::render($datatableConfig) !!}
            </div>
        </div>
    </div>
</div>