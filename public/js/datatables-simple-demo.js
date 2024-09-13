window.addEventListener('DOMContentLoaded', event => {
    // Simple-DataTables
    // https://github.com/fiduswriter/Simple-DataTables/wiki

    const datatablesSimple = document.getElementById('datatablesSimple');
    if (datatablesSimple) {
        new simpleDatatables.DataTable(datatablesSimple);
    }

    const infoLogsTable = document.getElementById('infoLogsTable');
    if (infoLogsTable) {
        new simpleDatatables.DataTable(infoLogsTable, {
            columns: [
                { select: 2, sort: 'desc' } // Assuming 'Date' is in the first column (index 0)
            ]
        });
    }

    const warningLogsTable = document.getElementById('warningLogsTable');
    if (warningLogsTable) {
        new simpleDatatables.DataTable(warningLogsTable, {
            columns: [
                { select: 2, sort: 'desc' } // Adjust the index if 'Date' is in a different column
            ]
        });
    }

    const dangerLogsTable = document.getElementById('dangerLogsTable');
    if (dangerLogsTable) {
        new simpleDatatables.DataTable(dangerLogsTable, {
            columns: [
                { select: 2, sort: 'desc' } // Modify accordingly
            ]
        });
    }

    const adminFieldsTable = document.getElementById('adminFieldsTable');
    if (adminFieldsTable) {
        new simpleDatatables.DataTable(adminFieldsTable, {
            paging: false
        });
    }

    const warehouseOverviewTable = document.getElementById('warehouseOverviewTable');
    if (warehouseOverviewTable) {
        new simpleDatatables.DataTable(warehouseOverviewTable, {
            perPage: 2,              // Set the number of records per page to 25.
            perPageSelect: false,     // Disable the per-page select dropdown.
            sortable: false
        });
    }
});
