<?php

namespace App\Helpers;

class DataTableHelper
{
    public static function render($options = [])
    {
        $id = "datatable_" . uniqid();

        $config = [
            'route'         => $options['route'] ?? '',
            'columns'       => $options['columns'] ?? [],
            'searchable'    => $options['searchable'] ?? [],
            'defaultOrder'  => $options['defaultOrder'] ?? [],
            'customColumns' => $options['customColumns'] ?? [],
            'perPage'       => $options['perPage'] ?? 10,
        ];

        $configJson = json_encode($config);

        ob_start(); ?>
        
        <div class="table-responsive text-nowrap">
            <div class="d-flex justify-content-between mb-2">
                <input type="text" class="form-control w-25 search-box" placeholder="Cari...">
                <div>
                    <label class="me-2">Rows per page:</label>
                    <select class="form-select d-inline-block w-auto rows-per-page">
                        <option value="5" <?= $config['perPage'] == 5 ? 'selected' : '' ?>>5</option>
                        <option value="10" <?= $config['perPage'] == 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= $config['perPage'] == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $config['perPage'] == 50 ? 'selected' : '' ?>>50</option>
                    </select>
                </div>
            </div>

            <table class="table table-bordered table-striped align-middle" id="<?= $id ?>">
                <thead>
                    <tr>
                        <?php foreach ($config['columns'] as $col): ?>
                            <th data-field="<?= $col['field'] ?>" class="sortable">
                                <?= $col['label'] ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <nav>
            <ul class="pagination"></ul>
        </nav>

        <script>
            (function() {
                const config = <?= $configJson ?>;
                const tableId = "<?= $id ?>";

                let currentPage = 1;
                let perPage = config.perPage;

                async function loadData(page = 1, search = "") {
                    currentPage = page;

                    // tampilkan loading spinner
                    const tbody = document.getElementById(tableId).querySelector("tbody");
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="${config.columns.length}" class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </td>
                        </tr>
                    `;

                    try {
                        const response = await fetch(config.route, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
                            },
                            body: JSON.stringify({
                                ...config,
                                page,
                                perPage,
                                search
                            })
                        });

                        const result = await response.json();
                        tbody.innerHTML = "";

                        if (result.data.length) {
                            result.data.forEach(row => {
                                let tr = "<tr>";
                                config.columns.forEach(c => {
                                    if (row["__custom_" + c.field] !== undefined) {
                                        tr += `<td>${row["__custom_"+c.field]}</td>`;
                                    } else {
                                        tr += `<td>${row[c.field] ?? '-'}</td>`;
                                    }
                                });
                                tr += "</tr>";
                                tbody.innerHTML += tr;
                            });
                        } else {
                            tbody.innerHTML = `<tr><td colspan="${config.columns.length}" class="text-center">Tidak ada data</td></tr>`;
                        }

                        // render pagination
                        renderPagination(result.total, result.per_page, result.current_page);

                    } catch (error) {
                        console.error("Error loadData:", error);
                        tbody.innerHTML = `<tr><td colspan="${config.columns.length}" class="text-center text-danger">Gagal memuat data</td></tr>`;
                    }
                }

                function renderPagination(total, perPage, currentPage) {
                    const totalPages = Math.ceil(total / perPage);
                    const pagination = document.querySelector(`#${tableId}`).closest(".table-responsive").nextElementSibling.querySelector(".pagination");
                    pagination.innerHTML = "";

                    if (totalPages <= 1) return;

                    for (let i = 1; i <= totalPages; i++) {
                        pagination.innerHTML += `
                            <li class="page-item ${i === currentPage ? 'active' : ''}">
                                <a href="#" class="page-link" data-page="${i}">${i}</a>
                            </li>`;
                    }

                    pagination.querySelectorAll("a").forEach(a => {
                        a.addEventListener("click", function(e) {
                            e.preventDefault();
                            loadData(parseInt(this.dataset.page));
                        });
                    });
                }

                document.querySelector(".rows-per-page").addEventListener("change", function() {
                    perPage = parseInt(this.value);
                    loadData(1);
                });

                document.querySelector(".search-box").addEventListener("keyup", function() {
                    loadData(1, this.value);
                });

                loadData();
            })();
        </script>
        <?php
        return ob_get_clean();
    }
}
