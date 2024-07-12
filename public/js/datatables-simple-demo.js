window.addEventListener('DOMContentLoaded', event => {
    // Simple-DataTables
    // https://github.com/fiduswriter/Simple-DataTables/wiki

    const datatablesSimple = document.getElementById('datatablesSimple');
    if (datatablesSimple) {
        new simpleDatatables.DataTable(datatablesSimple);
    }

    const infoLogsTable = document.getElementById('infoLogsTable');
    if (infoLogsTable) {
        new simpleDatatables.DataTable(infoLogsTable);
    }

    const warningLogsTable = document.getElementById('warningLogsTable');
    if (warningLogsTable) {
        new simpleDatatables.DataTable(warningLogsTable);
    }

    const dangerLogsTable = document.getElementById('dangerLogsTable');
    if (dangerLogsTable) {
        new simpleDatatables.DataTable(dangerLogsTable);
    }
});
