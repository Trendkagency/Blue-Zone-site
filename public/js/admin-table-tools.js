/**
 * Blue Zone Admin Table Tools Engine
 * Enterprise Multi-Column Sorting, Styled Excel/CSV Exporter & Branded Print Engine
 */

(function () {
    'use strict';

    window.BlueZoneTableEngine = {
        /**
         * Initialize all sortable tables and export buttons on the page
         */
        init: function () {
            this.initSorting();
            this.initSearchFilters();
            this.initAutoToolbars();
        },

        /**
         * Initialize interactive column sorting for tables
         */
        initSorting: function () {
            const tables = document.querySelectorAll('table.table, table[data-table-sortable="true"]');
            tables.forEach((table) => {
                if (table.dataset.sortInitialized === 'true') return;
                table.dataset.sortInitialized = 'true';

                const headers = table.querySelectorAll('thead th');
                headers.forEach((th, colIndex) => {
                    // Skip action columns, checkbox columns, or columns with data-no-sort
                    if (
                        th.hasAttribute('data-no-sort') ||
                        th.classList.contains('no-sort') ||
                        th.querySelector('input[type="checkbox"]') ||
                        th.textContent.trim() === '' ||
                        th.innerText.includes('الإجراءات') ||
                        th.innerText.includes('Actions')
                    ) {
                        return;
                    }

                    // Style the header for sorting
                    th.style.cursor = 'pointer';
                    th.style.userSelect = 'none';
                    th.setAttribute('title', (document.documentElement.lang === 'ar' || document.dir === 'rtl') ? 'اضغط للترتيب' : 'Click to sort');
                    
                    // Add sort indicator container if not present
                    if (!th.querySelector('.sort-indicator')) {
                        const iconSpan = document.createElement('span');
                        iconSpan.className = 'sort-indicator';
                        iconSpan.style.marginInlineStart = '0.45rem';
                        iconSpan.style.opacity = '0.4';
                        iconSpan.style.fontSize = '0.75rem';
                        iconSpan.innerHTML = '<i class="fa-solid fa-sort"></i>';
                        th.appendChild(iconSpan);
                    }

                    // Click handler
                    th.addEventListener('click', (e) => {
                        // Don't sort if clicking an input, checkbox, or button inside the header
                        if (['INPUT', 'BUTTON', 'A', 'SELECT'].includes(e.target.tagName)) return;

                        const currentDir = th.dataset.sortDir || 'none';
                        const newDir = currentDir === 'asc' ? 'desc' : 'asc';

                        // Reset all sibling headers in this thead
                        headers.forEach((h) => {
                            if (h !== th) {
                                h.dataset.sortDir = 'none';
                                const indicator = h.querySelector('.sort-indicator');
                                if (indicator) {
                                    indicator.innerHTML = '<i class="fa-solid fa-sort"></i>';
                                    indicator.style.opacity = '0.4';
                                    indicator.style.color = '';
                                }
                                h.classList.remove('sorted-asc', 'sorted-desc');
                            }
                        });

                        // Set new direction
                        th.dataset.sortDir = newDir;
                        th.classList.remove('sorted-asc', 'sorted-desc');
                        th.classList.add('sorted-' + newDir);

                        const indicator = th.querySelector('.sort-indicator');
                        if (indicator) {
                            indicator.innerHTML = newDir === 'asc' 
                                ? '<i class="fa-solid fa-sort-up"></i>' 
                                : '<i class="fa-solid fa-sort-down"></i>';
                            indicator.style.opacity = '1';
                            indicator.style.color = '#0284C7';
                        }

                        // Execute sorting
                        this.sortTable(table, colIndex, newDir, th.dataset.sortType);
                    });
                });
            });
        },

        /**
         * Sort table rows by column index and direction
         */
        sortTable: function (table, colIndex, direction, explicitType) {
            const tbody = table.querySelector('tbody');
            if (!tbody) return;

            const rows = Array.from(tbody.querySelectorAll('tr'));
            if (rows.length <= 1) return;

            // Check if there is an empty state row
            if (rows.length === 1 && rows[0].querySelector('td[colspan]')) return;

            // Detect column data type
            const sampleCell = rows[0].children[colIndex];
            const sampleText = sampleCell ? sampleCell.innerText.trim() : '';
            const type = explicitType || this.detectDataType(sampleText);

            const isAr = (document.documentElement.lang === 'ar' || document.dir === 'rtl');
            const collator = new Intl.Collator(isAr ? 'ar' : 'en', { numeric: true, sensitivity: 'base' });

            rows.sort((rowA, rowB) => {
                const cellA = rowA.children[colIndex];
                const cellB = rowB.children[colIndex];
                if (!cellA || !cellB) return 0;

                const valA = (cellA.dataset.sortValue !== undefined) ? cellA.dataset.sortValue : cellA.innerText.trim();
                const valB = (cellB.dataset.sortValue !== undefined) ? cellB.dataset.sortValue : cellB.innerText.trim();

                let comparison = 0;

                if (type === 'number') {
                    const numA = this.parseNumeric(valA);
                    const numB = this.parseNumeric(valB);
                    comparison = numA - numB;
                } else if (type === 'date') {
                    const dateA = new Date(valA).getTime() || 0;
                    const dateB = new Date(valB).getTime() || 0;
                    comparison = dateA - dateB;
                } else {
                    comparison = collator.compare(valA, valB);
                }

                return direction === 'asc' ? comparison : -comparison;
            });

            // Re-append sorted rows
            const fragment = document.createDocumentFragment();
            rows.forEach(row => fragment.appendChild(row));
            tbody.appendChild(fragment);
        },

        /**
         * Detect column data type based on sample cell text
         */
        detectDataType: function (text) {
            if (!text) return 'text';
            const clean = text.replace(/[\s,SAR$€£%ر.س\(\)\+]/gi, '');
            if (/^-?\d+(\.\d+)?$/.test(clean)) return 'number';
            if (/^\d{4}[-/.]\d{2}[-/.]\d{2}/.test(text)) return 'date';
            return 'text';
        },

        /**
         * Clean and parse numeric values including currencies and quantities
         */
        parseNumeric: function (val) {
            if (typeof val === 'number') return val;
            if (!val) return 0;
            const cleaned = val.toString().replace(/[\s,SAR$€£%ر.س]/gi, '').trim();
            const parsed = parseFloat(cleaned);
            return isNaN(parsed) ? 0 : parsed;
        },

        /**
         * Initialize in-table live search filters
         */
        initSearchFilters: function () {
            document.querySelectorAll('[data-table-filter]').forEach((input) => {
                const targetSelector = input.getAttribute('data-table-filter');
                const targetTable = document.querySelector(targetSelector);
                if (!targetTable) return;

                input.addEventListener('input', (e) => {
                    const query = e.target.value.toLowerCase().trim();
                    const rows = targetTable.querySelectorAll('tbody tr');

                    rows.forEach((row) => {
                        // Skip empty state row
                        if (row.querySelector('td[colspan]')) return;
                        const rowText = row.innerText.toLowerCase();
                        row.style.display = rowText.includes(query) ? '' : 'none';
                    });
                });
            });
        },

        /**
         * Initialize auto toolbar bindings
         */
        initAutoToolbars: function () {
            // Find all export triggers
            document.querySelectorAll('[data-export-excel]').forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const tableSelector = btn.getAttribute('data-export-excel');
                    const title = btn.getAttribute('data-title') || 'Report';
                    this.exportToExcel(tableSelector, title);
                });
            });

            document.querySelectorAll('[data-export-csv]').forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const tableSelector = btn.getAttribute('data-export-csv');
                    const title = btn.getAttribute('data-title') || 'Report';
                    this.exportToCsv(tableSelector, title);
                });
            });

            document.querySelectorAll('[data-print-table]').forEach((btn) => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const tableSelector = btn.getAttribute('data-print-table');
                    const title = btn.getAttribute('data-title') || 'Blue Zone Report';
                    this.printTable(tableSelector, title);
                });
            });
        },

        /**
         * Export table to Styled Excel Spreadsheet (.xls with HTML/XML structure)
         */
        exportToExcel: function (tableOrSelector, filename = 'BlueZone_Report') {
            const table = typeof tableOrSelector === 'string' ? document.querySelector(tableOrSelector) : tableOrSelector;
            if (!table) {
                console.error('[BlueZone Table Tools]: Target table not found:', tableOrSelector);
                return;
            }

            const isAr = (document.documentElement.lang === 'ar' || document.dir === 'rtl');
            const now = new Date();
            const timestamp = now.toLocaleDateString(isAr ? 'ar-SA' : 'en-US') + ' ' + now.toLocaleTimeString();
            const cleanTitle = (filename || 'BlueZone_Report').replace(/[/\\?%*:|"<>]/g, '_');

            // Extract Headers
            const headers = [];
            table.querySelectorAll('thead th').forEach((th) => {
                if (this.shouldIgnoreCell(th)) return;
                let text = th.innerText.replace(/\s+/g, ' ').replace(/[▲▼↕]/g, '').trim();
                headers.push(text);
            });

            // Extract Rows
            const rowsData = [];
            table.querySelectorAll('tbody tr').forEach((tr) => {
                if (tr.style.display === 'none' || tr.querySelector('td[colspan]')) return;
                const row = [];
                tr.querySelectorAll('td').forEach((td, idx) => {
                    if (this.shouldIgnoreCell(td)) return;
                    let text = td.innerText.replace(/\s+/g, ' ').trim();
                    row.push(text);
                });
                if (row.length > 0) rowsData.push(row);
            });

            // Build Styled Excel XML / HTML content
            let excelContent = `
                <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                    <!--[if gte mso 9]>
                    <xml>
                        <x:ExcelWorkbook>
                            <x:ExcelWorksheets>
                                <x:ExcelWorksheet>
                                    <x:Name>${cleanTitle.substring(0, 31)}</x:Name>
                                    <x:WorksheetOptions>
                                        <x:DisplayGridlines/>
                                        ${isAr ? '<x:DisplayRightToLeft/>' : ''}
                                    </x:WorksheetOptions>
                                </x:ExcelWorksheet>
                            </x:ExcelWorksheets>
                        </x:ExcelWorkbook>
                    </xml>
                    <![endif]-->
                    <style>
                        body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; direction: ${isAr ? 'rtl' : 'ltr'}; }
                        .report-header { font-size: 16pt; font-weight: bold; color: #0A4F78; text-align: center; }
                        .report-sub { font-size: 10pt; color: #64748B; text-align: center; margin-bottom: 12px; }
                        table { border-collapse: collapse; width: 100%; }
                        th { background-color: #0A4F78; color: #FFFFFF; font-weight: bold; border: 1px solid #083D5D; padding: 10px 14px; font-size: 11pt; text-align: ${isAr ? 'right' : 'left'}; }
                        td { border: 1px solid #CBD5E1; padding: 8px 12px; font-size: 10pt; color: #1E293B; text-align: ${isAr ? 'right' : 'left'}; }
                        .alt-row { background-color: #F8FAFC; }
                    </style>
                </head>
                <body>
                    <div class="report-header">Blue Zone Luxury Medical Bioceuticals — ${cleanTitle}</div>
                    <div class="report-sub">${isAr ? 'تم استخراج التقرير بتاريخ' : 'Exported on'}: ${timestamp} | ${isAr ? 'إجمالي السجلات' : 'Total Records'}: ${rowsData.length}</div>
                    <table>
                        <thead>
                            <tr>
                                ${headers.map(h => `<th>${this.escapeXml(h)}</th>`).join('')}
                            </tr>
                        </thead>
                        <tbody>
                            ${rowsData.map((row, rIdx) => `
                                <tr class="${rIdx % 2 === 1 ? 'alt-row' : ''}">
                                    ${row.map(cell => `<td>${this.escapeXml(cell)}</td>`).join('')}
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </body>
                </html>
            `;

            const blob = new Blob(['\uFEFF', excelContent], { type: 'application/vnd.ms-excel;charset=utf-8' });
            this.downloadBlob(blob, `${cleanTitle}_${this.getDateStamp()}.xls`);

            if (typeof window.showAdminToast === 'function') {
                window.showAdminToast(
                    isAr ? 'تصدير إكسل ناجح' : 'Excel Export Complete',
                    isAr ? `تم تصدير ${rowsData.length} سجل بنجاح إلى ملف إكسل.` : `Exported ${rowsData.length} records successfully to Excel.`,
                    'fa-solid fa-file-excel text-emerald-500'
                );
            }
        },

        /**
         * Export table to UTF-8 BOM CSV File
         */
        exportToCsv: function (tableOrSelector, filename = 'BlueZone_Report') {
            const table = typeof tableOrSelector === 'string' ? document.querySelector(tableOrSelector) : tableOrSelector;
            if (!table) return;

            const isAr = (document.documentElement.lang === 'ar' || document.dir === 'rtl');
            const cleanTitle = (filename || 'BlueZone_Report').replace(/[/\\?%*:|"<>]/g, '_');

            const csvRows = [];

            // Headers
            const headers = [];
            table.querySelectorAll('thead th').forEach((th) => {
                if (this.shouldIgnoreCell(th)) return;
                let text = th.innerText.replace(/\s+/g, ' ').replace(/[▲▼↕]/g, '').trim();
                headers.push('"' + text.replace(/"/g, '""') + '"');
            });
            csvRows.push(headers.join(','));

            // Rows
            table.querySelectorAll('tbody tr').forEach((tr) => {
                if (tr.style.display === 'none' || tr.querySelector('td[colspan]')) return;
                const row = [];
                tr.querySelectorAll('td').forEach((td) => {
                    if (this.shouldIgnoreCell(td)) return;
                    let text = td.innerText.replace(/\s+/g, ' ').trim();
                    row.push('"' + text.replace(/"/g, '""') + '"');
                });
                if (row.length > 0) csvRows.push(row.join(','));
            });

            // Prepend UTF-8 BOM (\uFEFF)
            const csvString = '\uFEFF' + csvRows.join('\r\n');
            const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
            this.downloadBlob(blob, `${cleanTitle}_${this.getDateStamp()}.csv`);

            if (typeof window.showAdminToast === 'function') {
                window.showAdminToast(
                    isAr ? 'تصدير CSV ناجح' : 'CSV Export Complete',
                    isAr ? 'تم إنشاء ملف CSV بتنسيق UTF-8 المتوافق مع كافة البرامج.' : 'Created UTF-8 CSV compatible with Excel and data tools.',
                    'fa-solid fa-file-csv text-sky-500'
                );
            }
        },

        /**
         * Generate clean, luxury branded Print Window and trigger print dialog
         */
        printTable: function (tableOrSelector, title = 'Blue Zone Report') {
            const table = typeof tableOrSelector === 'string' ? document.querySelector(tableOrSelector) : tableOrSelector;
            if (!table) return;

            const isAr = (document.documentElement.lang === 'ar' || document.dir === 'rtl');
            const now = new Date();
            const timestamp = now.toLocaleDateString(isAr ? 'ar-SA' : 'en-US') + ' ' + now.toLocaleTimeString();

            // Extract clean headers
            const headers = [];
            table.querySelectorAll('thead th').forEach((th) => {
                if (this.shouldIgnoreCell(th)) return;
                let text = th.innerText.replace(/\s+/g, ' ').replace(/[▲▼↕]/g, '').trim();
                headers.push(text);
            });

            // Extract clean rows
            const rowsData = [];
            table.querySelectorAll('tbody tr').forEach((tr) => {
                if (tr.style.display === 'none' || tr.querySelector('td[colspan]')) return;
                const row = [];
                tr.querySelectorAll('td').forEach((td) => {
                    if (this.shouldIgnoreCell(td)) return;
                    let text = td.innerText.replace(/\s+/g, ' ').trim();
                    row.push(text);
                });
                if (row.length > 0) rowsData.push(row);
            });

            const printWindow = window.open('', '_blank', 'width=1100,height=850');
            if (!printWindow) {
                alert(isAr ? 'يرجى السماح بالنوافذ المنبثقة للطباعة' : 'Please allow popups to open the print dialog.');
                return;
            }

            const html = `
                <!DOCTYPE html>
                <html lang="${isAr ? 'ar' : 'en'}" dir="${isAr ? 'rtl' : 'ltr'}">
                <head>
                    <meta charset="UTF-8">
                    <title>${title} — Blue Zone Bioceuticals</title>
                    <style>
                        @page {
                            size: A4 landscape;
                            margin: 1.2cm 1.5cm;
                        }
                        * {
                            box-sizing: border-box;
                            margin: 0;
                            padding: 0;
                        }
                        body {
                            font-family: 'Segoe UI', Tahoma, -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
                            font-size: 10pt;
                            color: #0F172A;
                            background: #FFFFFF;
                            padding: 20px;
                            direction: ${isAr ? 'rtl' : 'ltr'};
                        }
                        .print-header {
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            border-bottom: 2.5px solid #0A4F78;
                            padding-bottom: 12px;
                            margin-bottom: 18px;
                        }
                        .brand-title {
                            font-size: 18pt;
                            font-weight: 800;
                            color: #0A4F78;
                            letter-spacing: -0.5px;
                        }
                        .brand-subtitle {
                            font-size: 9pt;
                            color: #64748B;
                            font-weight: 600;
                        }
                        .report-meta {
                            text-align: ${isAr ? 'left' : 'right'};
                            font-size: 8.5pt;
                            color: #475569;
                            line-height: 1.4;
                        }
                        .report-title-badge {
                            background: #F0F9FF;
                            border: 1px solid #BAE6FD;
                            color: #0369A1;
                            font-weight: 700;
                            font-size: 12pt;
                            padding: 6px 14px;
                            border-radius: 6px;
                            margin-bottom: 14px;
                            display: inline-block;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 8px;
                        }
                        thead {
                            display: table-header-group;
                        }
                        tr {
                            page-break-inside: avoid;
                        }
                        th {
                            background-color: #0A4F78 !important;
                            color: #FFFFFF !important;
                            -webkit-print-color-adjust: exact;
                            print-color-adjust: exact;
                            font-weight: 700;
                            font-size: 9.5pt;
                            padding: 8px 10px;
                            border: 1px solid #083D5D;
                            text-align: ${isAr ? 'right' : 'left'};
                        }
                        td {
                            border: 1px solid #E2E8F0;
                            padding: 7px 10px;
                            font-size: 9pt;
                            color: #1E293B;
                            text-align: ${isAr ? 'right' : 'left'};
                        }
                        tbody tr:nth-child(even) td {
                            background-color: #F8FAFC !important;
                            -webkit-print-color-adjust: exact;
                            print-color-adjust: exact;
                        }
                        .print-footer {
                            margin-top: 24px;
                            border-top: 1px solid #E2E8F0;
                            padding-top: 10px;
                            display: flex;
                            justify-content: space-between;
                            font-size: 8pt;
                            color: #94A3B8;
                        }
                        .print-controls {
                            margin-bottom: 15px;
                            display: flex;
                            gap: 10px;
                        }
                        @media print {
                            .print-controls { display: none !important; }
                            body { padding: 0; }
                        }
                        .btn-print {
                            background: #0A4F78;
                            color: #fff;
                            border: none;
                            padding: 8px 16px;
                            border-radius: 6px;
                            font-weight: bold;
                            cursor: pointer;
                        }
                    </style>
                </head>
                <body>
                    <div class="print-controls">
                        <button class="btn-print" onclick="window.print()">🖨️ ${isAr ? 'طباعة الآن' : 'Print Now'}</button>
                        <button class="btn-print" style="background: #64748B;" onclick="window.close()">${isAr ? 'إغلاق' : 'Close'}</button>
                    </div>

                    <div class="print-header">
                        <div>
                            <div class="brand-title">BLUE ZONE</div>
                            <div class="brand-subtitle">Cellular Longevity &amp; Precision Bioceuticals OS</div>
                        </div>
                        <div class="report-meta">
                            <div><strong>${isAr ? 'تاريخ التقرير:' : 'Date:'}</strong> ${timestamp}</div>
                            <div><strong>${isAr ? 'المستخدم:' : 'User:'}</strong> Admin Controller</div>
                            <div><strong>${isAr ? 'إجمالي السجلات:' : 'Total Records:'}</strong> ${rowsData.length}</div>
                        </div>
                    </div>

                    <div class="report-title-badge">${title}</div>

                    <table>
                        <thead>
                            <tr>
                                ${headers.map(h => `<th>${this.escapeXml(h)}</th>`).join('')}
                            </tr>
                        </thead>
                        <tbody>
                            ${rowsData.map(row => `
                                <tr>
                                    ${row.map(cell => `<td>${this.escapeXml(cell)}</td>`).join('')}
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>

                    <div class="print-footer">
                        <div>Blue Zone Enterprise Operating System &copy; ${now.getFullYear()} — Confidential Internal Document</div>
                        <div>Page 1 of 1</div>
                    </div>

                    <script>
                        window.onload = function() {
                            setTimeout(function() {
                                window.print();
                            }, 400);
                        };
                    </script>
                </body>
                </html>
            `;

            printWindow.document.open();
            printWindow.document.write(html);
            printWindow.document.close();
        },

        /**
         * Helper to determine if a cell (checkbox/action) should be excluded from export
         */
        shouldIgnoreCell: function (cell) {
            if (!cell) return true;
            if (cell.hasAttribute('data-no-export') || cell.classList.contains('no-export')) return true;
            if (cell.querySelector('input[type="checkbox"]') || cell.querySelector('button') || cell.classList.contains('actions-col')) {
                // Check if it's an action column
                const txt = cell.innerText.trim();
                if (txt === '' || txt === 'الإجراءات' || txt === 'Actions' || txt === 'تعديل' || txt === 'حذف') return true;
            }
            return false;
        },

        /**
         * Helper: Date stamp string YYYY-MM-DD
         */
        getDateStamp: function () {
            const d = new Date();
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        },

        /**
         * Helper: Download Blob
         */
        downloadBlob: function (blob, filename) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            setTimeout(() => URL.revokeObjectURL(url), 1000);
        },

        /**
         * Helper: Escape XML / HTML special characters
         */
        escapeXml: function (unsafe) {
            if (unsafe === null || unsafe === undefined) return '';
            return unsafe.toString()
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&apos;');
        }
    };

    // Auto-initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => window.BlueZoneTableEngine.init());
    } else {
        window.BlueZoneTableEngine.init();
    }
})();
