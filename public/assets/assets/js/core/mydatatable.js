let orderState = {};

function initDataTable(config) {
    const tbody = document.querySelector(`#${config.id} tbody`);
    const searchBox = document.querySelector(`#${config.id}_search`);
    const perPageSelect = document.querySelector(`#${config.id}_perPage`);

    // inisialisasi order default
    orderState[config.id] = config.defaultOrder;

    // load pertama
    loadData(config, 1, searchBox.value, {}, parseInt(perPageSelect.value));

    // event search
    searchBox.addEventListener("keyup", function() {
        loadData(config, 1, this.value, {}, parseInt(perPageSelect.value));
    });

    // event perPage
    perPageSelect.addEventListener("change", function() {
        loadData(config, 1, searchBox.value, {}, parseInt(this.value));
    });

    // event sorting
    document.querySelectorAll(`#${config.id} thead th.sortable`).forEach(th => {
        th.addEventListener("click", function() {
            const col = this.getAttribute("data-col");
            let currentOrders = orderState[config.id] || [];
            let existing = currentOrders.find(o => o.col === col);

            if (!existing) {
                currentOrders.push({col: col, dir: "asc"});
            } else if (existing.dir === "asc") {
                existing.dir = "desc";
            } else {
                currentOrders = currentOrders.filter(o => o.col !== col);
            }
            orderState[config.id] = currentOrders;

            updateSortIcons(config.id, currentOrders);
            loadData(config, 1, searchBox.value, {}, parseInt(perPageSelect.value));
        });
    });
}

function updateSortIcons(tableId, orders) {
    document.querySelectorAll(`#${tableId} thead th.sortable`).forEach(th => {
        th.querySelector(".sort-icon").innerHTML = "";
        const col = th.getAttribute("data-col");
        const order = orders.find(o => o.col === col);
        if (order) {
            th.querySelector(".sort-icon").innerHTML = order.dir === "asc" ? "▲" : "▼";
        }
    });
}

async function loadData(config, page = 1, search = "", extraFilters = {}, perPage = 10) {
    const tbody = document.querySelector(`#${config.id} tbody`);
    tbody.innerHTML = `
        <tr>
            <td colspan="${config.columns.length}" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </td>
        </tr>
    `;
    const response = await fetch(config.routeFetch, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name=csrf-token]').content
        },
        body: JSON.stringify({
            page,
            perPage,
            search,
            order: orderState[config.id],
            filters: extraFilters
        })
    });
    const result = await response.json();

    tbody.innerHTML = "";
    if (result.data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${config.columns.length}" class="text-center text-muted">No data found</td></tr>`;
    } else {
        result.data.forEach(row => {
            let tr = "<tr>";
            config.columns.forEach(c => {
                tr += `<td>${row[c.field] ?? ""}</td>`;
            });
            tr += "</tr>";
            tbody.innerHTML += tr;
        });
    }

    renderPagination(config, result.current_page, result.total, perPage, search, extraFilters);
    updateInfo(config, result.current_page, result.total, perPage); // ✅ update info
}

function renderPagination(config, currentPage, total, perPage, search, extraFilters) {
    const pagination = document.querySelector(`#${config.id}_pagination`);
    pagination.innerHTML = "";
    const totalPages = Math.ceil(total / perPage);

    if (totalPages <= 1) return;

    for (let i = 1; i <= totalPages; i++) {
        const li = document.createElement("li");
        li.className = "page-item " + (i === currentPage ? "active" : "");
        li.innerHTML = `<a class="page-link" href="#">${i}</a>`;
        li.addEventListener("click", e => {
            e.preventDefault();
            loadData(config, i, search, extraFilters, perPage);
        });
        pagination.appendChild(li);
    }
}

function exportData(type, tableId) {
    const config = window[`${tableId}_config`]; // ambil config global
    if (!config || !config.routeExport) {
        alert("Export route not defined!");
        return;
    }

    const search = document.querySelector(`#${tableId}_search`)?.value || "";
    const perPage = document.querySelector(`#${tableId}_perPage`)?.value || 10;

    // gabungkan order & filter
    const params = new URLSearchParams({
        type,
        search,
        perPage,
        order: JSON.stringify(orderState[config.id] || []),
    });

    // buka di tab baru
    window.open(`${config.routeExport}?${params.toString()}`, "_blank");
}

function updateInfo(config, currentPage, total, perPage) {
    const infoEl = document.querySelector(`#${config.id}_info`);
    if (!infoEl) return;

    if (total === 0) {
        infoEl.innerText = "Showing 0 to 0 of 0 entries";
        return;
    }

    let start = (currentPage - 1) * perPage + 1;
    let end = Math.min(start + perPage - 1, total);
    infoEl.innerText = `Showing ${start} to ${end} of ${total} entries`;
}
