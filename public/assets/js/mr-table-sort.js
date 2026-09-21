/**
 * BLUE ZONE™ MR CRM - Interactive DataTable Column Sorting & Instant Filter
 * Lightweight, zero-dependency client-side sorting engine for data tables.
 */
document.addEventListener('DOMContentLoaded', function () {
    initBzTableSorting();
});

function initBzTableSorting() {
    const tables = document.querySelectorAll('table.bz-sortable-table, table[data-sortable="true"]');
    
    tables.forEach((table) => {
        const thead = table.querySelector('thead');
        const tbody = table.querySelector('tbody');
        if (!thead || !tbody) return;

        const headers = thead.querySelectorAll('th');
        headers.forEach((th, colIndex) => {
            // Check if column is excluded from sorting
            if (th.getAttribute('data-no-sort') !== null || th.classList.contains('no-sort')) {
                return;
            }

            // Make header clickable and style as sortable
            th.style.cursor = 'pointer';
            th.classList.add('select-none', 'group');
            th.setAttribute('title', 'Click to sort');

            // Inject sort icon container if not already present
            let iconWrapper = th.querySelector('.bz-sort-icon');
            if (!iconWrapper) {
                iconWrapper = document.createElement('span');
                iconWrapper.className = 'bz-sort-icon inline-flex items-center ml-1.5 mr-1.5 text-gray-400 group-hover:text-sky-500 transition-colors text-[11px]';
                iconWrapper.innerHTML = '<i class="fa-solid fa-sort opacity-40 group-hover:opacity-100"></i>';
                th.appendChild(iconWrapper);
            }

            // Click event listener
            th.addEventListener('click', function () {
                const currentOrder = th.getAttribute('data-order') || 'none';
                const newOrder = currentOrder === 'asc' ? 'desc' : 'asc';

                // Reset all other headers in this table
                headers.forEach((otherTh) => {
                    if (otherTh !== th) {
                        otherTh.removeAttribute('data-order');
                        const otherIcon = otherTh.querySelector('.bz-sort-icon');
                        if (otherIcon) {
                            otherIcon.innerHTML = '<i class="fa-solid fa-sort opacity-40 group-hover:opacity-100"></i>';
                        }
                    }
                });

                // Update current header state
                th.setAttribute('data-order', newOrder);
                if (newOrder === 'asc') {
                    iconWrapper.innerHTML = '<i class="fa-solid fa-arrow-up-short-wide text-sky-500 font-bold"></i>';
                } else {
                    iconWrapper.innerHTML = '<i class="fa-solid fa-arrow-down-wide-short text-sky-500 font-bold"></i>';
                }

                // Determine sorting type
                const sortType = th.getAttribute('data-sort-type') || detectColumnType(tbody, colIndex);

                // Sort rows
                sortTbodyRows(tbody, colIndex, newOrder, sortType);
            });
        });
    });

    // Support instant in-table search if input exists
    document.querySelectorAll('input[data-table-filter]').forEach((input) => {
        const targetSelector = input.getAttribute('data-table-filter');
        const targetTable = document.querySelector(targetSelector);
        if (!targetTable) return;

        const tbody = targetTable.querySelector('tbody');
        if (!tbody) return;

        input.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const rows = tbody.querySelectorAll('tr');
            rows.forEach((row) => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    });
}

function detectColumnType(tbody, colIndex) {
    const rows = Array.from(tbody.querySelectorAll('tr'));
    for (let r of rows) {
        const cell = r.children[colIndex];
        if (!cell) continue;
        const text = cell.textContent.trim();
        if (!text || text === '—' || text === 'N/A') continue;

        // Check percent
        if (/^-?\d+(\.\d+)?%$/.test(text)) return 'number';
        // Check number / distance / points
        if (/^-?[\d,]+(\.\d+)?(\s*(m|km|min|pts|pt))?$/i.test(text)) return 'number';
        // Check date
        if (!isNaN(Date.parse(text)) && (text.includes('-') || text.includes('/') || text.includes(','))) return 'date';
    }
    return 'text';
}

function sortTbodyRows(tbody, colIndex, order, sortType) {
    const rows = Array.from(tbody.querySelectorAll('tr'));
    // Filter out empty state rows
    const dataRows = rows.filter(r => !r.classList.contains('empty-state-row') && r.children.length > 1);
    if (dataRows.length <= 1) return;

    dataRows.sort((a, b) => {
        const cellA = a.children[colIndex];
        const cellB = b.children[colIndex];
        let valA = cellA ? (cellA.getAttribute('data-value') || cellA.textContent).trim() : '';
        let valB = cellB ? (cellB.getAttribute('data-value') || cellB.textContent).trim() : '';

        if (sortType === 'number') {
            // Clean non-numeric characters except minus and dot
            const numA = parseFloat(valA.replace(/[^0-9.-]/g, '')) || 0;
            const numB = parseFloat(valB.replace(/[^0-9.-]/g, '')) || 0;
            return order === 'asc' ? numA - numB : numB - numA;
        }

        if (sortType === 'date') {
            const dateA = Date.parse(valA) || 0;
            const dateB = Date.parse(valB) || 0;
            return order === 'asc' ? dateA - dateB : dateB - dateA;
        }

        // Default text sorting
        const cmp = valA.localeCompare(valB, undefined, { numeric: true, sensitivity: 'base' });
        return order === 'asc' ? cmp : -cmp;
    });

    // Re-append sorted rows to tbody
    dataRows.forEach(row => tbody.appendChild(row));
}

// Re-run sorting setup if dynamically loaded content appears
window.initBzTableSorting = initBzTableSorting;
