<?php

namespace App\Helpers;

class DataTableHelper
{
    public static function render(array $config = [])
    {
        $tableId     = $config['id'] ?? ('datatable_' . uniqid());
        $routeFetch  = $config['routeFetch'] ?? '';
        $routeExport = $config['routeExport'] ?? '';
        $columns     = $config['columns'] ?? [];
        $defaultOrder = $config['defaultOrder'] ?? ['col' => 'id', 'dir' => 'desc'];
        $perPage     = $config['perPage'] ?? 10;

        // generate header
        $thead = '';
        foreach ($columns as $col) {
            $thead .= "<th data-field='{$col['field']}'>{$col['label']} <span class='sort-icon'></span></th>";
        }

        $columnsJs = json_encode(array_map(fn($c) => $c['field'], $columns));
        $defaultCol = $defaultOrder['col'];
        $defaultDir = $defaultOrder['dir'];

        // --- HTML ---
        $html = <<<HTML
        <div class="datatable-wrapper mb-3">
            <div class="d-flex justify-content-between mb-2">
                <input type="text" id="search_{$tableId}" class="form-control w-25" placeholder="Search...">
                <div>
                    <a href="{$routeExport}?type=excel" class="btn btn-success btn-sm me-1">Export Excel</a>
                    <a href="{$routeExport}?type=csv" class="btn btn-primary btn-sm">Export CSV</a>
                </div>
            </div>

            <table id="{$tableId}" class="table table-bordered table-striped">
                <thead><tr>{$thead}</tr></thead>
                <tbody></tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center mt-2">
                <select id="perPage_{$tableId}" class="form-select w-auto">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <nav><ul class="pagination" id="pagination_{$tableId}"></ul></nav>
            </div>
        </div>
HTML;

        // --- JavaScript ---
        $html .= <<<'JS'
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tableId = "__TABLEID__";
            const routeFetch = "__ROUTEFETCH__";
            const columns = __COLUMNS__;
            let currentPage = 1;
            let perPage = __PERPAGE__;
            let search = "";
            let orderBy = [{ col: "__DEFAULTCOL__", dir: "__DEFAULTDIR__" }];

            async function loadData() {
                // ambil custom filter
                let filters = {};
                document.querySelectorAll("[data-filter]").forEach(el => {
                    filters[el.dataset.filter] = el.value;
                });

                const response = await fetch(routeFetch, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
                    },
                    body: JSON.stringify({
                        page: currentPage,
                        perPage: perPage,
                        search: search,
                        orderBy: orderBy,
                        filters: filters
                    })
                });
                const result = await response.json();

                // render body
                const tbody = document.querySelector(`#${tableId} tbody`);
                tbody.innerHTML = "";
                result.data.forEach(row => {
                    let tr = "<tr>";
                    columns.forEach(field => {
                        tr += `<td>${row[field] !== undefined && row[field] !== null ? row[field] : ""}</td>`;
                    });
                    tr += "</tr>";
                    tbody.innerHTML += tr;
                });

                // render pagination
                const pagination = document.querySelector("#pagination_" + tableId);
                pagination.innerHTML = "";
                for (let i = 1; i <= result.lastPage; i++) {
                    pagination.innerHTML += `<li class="page-item ${i==result.currentPage?"active":""}">
                        <a href="#" class="page-link" data-page="${i}">${i}</a></li>`;
                }
                pagination.querySelectorAll("a").forEach(a => {
                    a.addEventListener("click", e => {
                        e.preventDefault();
                        currentPage = e.target.dataset.page;
                        loadData();
                    });
                });

                // update sort icons
                document.querySelectorAll(`#${tableId} thead th .sort-icon`).forEach(icon => {
                    icon.innerHTML = "";
                });
                orderBy.forEach(o => {
                    const activeTh = document.querySelector(`#${tableId} thead th[data-field="${o.col}"] .sort-icon`);
                    if (activeTh) {
                        activeTh.innerHTML += (o.dir === "asc" ? " ▲" : " ▼");
                    }
                });
            }

            // search
            document.querySelector("#search_" + tableId).addEventListener("keyup", e => {
                search = e.target.value;
                currentPage = 1;
                loadData();
            });

            // perPage
            document.querySelector("#perPage_" + tableId).addEventListener("change", e => {
                perPage = e.target.value;
                currentPage = 1;
                loadData();
            });

            // sorting handler
            document.querySelectorAll(`#${tableId} thead th`).forEach(th => {
                th.addEventListener("click", (e) => {
                    const field = th.dataset.field;
                    if (!field) return;

                    if (e.shiftKey) {
                        // multi-order
                        let existing = orderBy.find(o => o.col === field);
                        if (existing) {
                            existing.dir = existing.dir === "asc" ? "desc" : "asc";
                        } else {
                            orderBy.push({ col: field, dir: "asc" });
                        }
                    } else {
                        // single-order
                        let existing = orderBy.find(o => o.col === field);
                        if (existing) {
                            existing.dir = existing.dir === "asc" ? "desc" : "asc";
                            orderBy = [existing];
                        } else {
                            orderBy = [{ col: field, dir: "asc" }];
                        }
                    }
                    loadData();
                });
            });

            // trigger load pertama
            loadData();
        });
        </script>
JS;

        // replace placeholder
        $html = str_replace(
            ["__TABLEID__", "__ROUTEFETCH__", "__COLUMNS__", "__PERPAGE__", "__DEFAULTCOL__", "__DEFAULTDIR__"],
            [$tableId, $routeFetch, $columnsJs, $perPage, $defaultCol, $defaultDir],
            $html
        );

        return $html;
    }
}
