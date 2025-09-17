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
        ];

        $configJson = json_encode($config);

        ob_start(); ?>
        <div class="table-responsive">
            <input type="text" class="form-control mb-2 search-box" placeholder="Cari...">
            <table class="table table-bordered table-striped align-middle" id="<?= $id ?>">
                <thead class="table-dark">
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

                async function loadData(page = 1, search = "", order = config.defaultOrder) {
                    const response = await fetch(config.route, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
                        },
                        body: JSON.stringify({
                            page,
                            search,
                            order
                        })
                    });

                    const result = await response.json();
                    const tbody = document.getElementById(tableId).querySelector("tbody");
                    tbody.innerHTML = "";

                    if (result.data.length) {
                        result.data.forEach(row => {
                            let tr = "<tr>";
                            config.columns.forEach(c => {
                                // cek apakah ada custom kolom
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
                }

                // init load
                loadData();

                // search box
                document.querySelector(".search-box").addEventListener("keyup", e => {
                    loadData(1, e.target.value);
                });

            })();
        </script>
<?php
        return ob_get_clean();
    }
}
