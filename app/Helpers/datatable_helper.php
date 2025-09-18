<?php

namespace App\Helpers;

class DataTableHelper
{
    public static function render(array $config)
    {
        $id          = $config['id'] ?? 'datatable_' . uniqid();
        $routeFetch  = $config['routeFetch'] ?? '';
        $routeExport = $config['routeExport'] ?? '';
        $columns     = $config['columns'] ?? [];
        $defaultOrder= $config['defaultOrder'] ?? [];
        $perPage     = $config['perPage'] ?? 10;

        $columnsJson = json_encode($columns);
        $orderJson   = json_encode($defaultOrder);

        return <<<HTML
            <div class="d-flex justify-content-between mb-2">
                <div>
                    <button class="btn btn-success btn-sm" onclick="exportData('excel', '{$id}')"><span class="icon-base ri ri-file-excel-2-fill icon-20px me-4"></span>Export Excel</button>
                    <button class="btn btn-primary btn-sm" onclick="exportData('csv', '{$id}')"><span class="icon-base ri ri-file-3-fill icon-20px me-4"></span>Export CSV</button>
                </div>
                <div class="d-flex gap-2">
                    <select id="{$id}_perPage" class="form-select form-select-sm" style="width:auto;">
                        <option value="10"  ".($perPage==10?'selected':'').">10</option>
                        <option value="25"  ".($perPage==25?'selected':'').">25</option>
                        <option value="50"  ".($perPage==50?'selected':'').">50</option>
                        <option value="100" ".($perPage==100?'selected':'').">100</option>
                    </select>
                    <input type="text" id="{$id}_search" class="form-control form-control-sm" placeholder="Search...">
                </div>
            </div>
            <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped" id="{$id}">
                <thead>
                    <tr>
            HTML
            . implode("", array_map(fn($col) => "<th data-col='{$col['field']}' class='sortable'>{$col['title']} <span class='sort-icon'></span></th>", $columns)) .
            <<<HTML
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            </div>
            <br>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center" id="{$id}_pagination"></ul>
            </nav>
            <div class="mt-2 text-muted small" id="{$id}_info"></div>
            <script>
            window.{$id}_config = {
                id: "{$id}",
                routeExport: "{$routeExport}",
            };
            document.addEventListener("DOMContentLoaded", function() {
                initDataTable({
                    id: "{$id}",
                    routeFetch: "{$routeFetch}",
                    routeExport: "{$routeExport}",
                    columns: {$columnsJson},
                    defaultOrder: {$orderJson},
                    perPage: {$perPage}
                });
            });
            </script>
        HTML;
    }
}
