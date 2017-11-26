var drawImportedTable = function (receivedData) {
    var dataDisplay = $("#table-row");
    dataDisplay.css("display", "block")
    $("#dataDisplay").DataTable({
        data: receivedData,
        columns: [
            { data: 'date' },
            { data: 'liters' },
            { data: 'speedometer' },
            { data: 'country' },
            { data : 'adBlueSum' },
            { data : 'driverId' }
        ]
    });
};

$("#fileForm").submit(function (event) {
    $.ajax({
        processData: false,
        contentType: false,
        data: new FormData($("#fileForm")[0]),
        type: $(this).attr('method'),
        url: $(this).attr('action'),
        success: function (response) {
            var objResponse = JSON.parse(response);
            window.drawImportedTable(objResponse);
        }
    });
    return false;
});

var handleLastUsedFilesClicks = function (dataTable) {
    $("#filesDisplay").on('click', 'tbody td', function () {
        var currentRowData = dataTable.row(this).data();
        console.log("currentRowData", currentRowData);
    });
}

var drawLastFilesTable = function (receivedData) {
    var lastFilesRow = $("#last_files_row");
    lastFilesRow.css("display", "block");
    var filesTable = $("#filesDisplay").DataTable({
        data: receivedData,
        columns: [
            { data: 'data' },
            { data: 'fileName' },
        ]
    });
    window.handleLastUsedFilesClicks(filesTable);
};

var getLastUploadedFilesData = function () {
    $.ajax({
        type: 'POST',
        url: 'last_uploaded_files.php',
        success: function (e) {
            window.drawLastFilesTable(JSON.parse(e));
        }
    });
};

getLastUploadedFilesData();
