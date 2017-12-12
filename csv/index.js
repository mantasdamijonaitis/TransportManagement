var dialog = $( "#dialog" ).dialog({
    autoOpen: false,
    closeOnEscape: false,
    open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); }
});

var confirmDialog = $("#confirm-dialog").dialog({
    autoOpen: false,
    closeOnEscape: false,
    open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); }
});

var entryId = "";
var loadId = "";
var dateInput = $("#date");
var timeInput = $("#time");
var litersInput = $("#remainingLiters");
var licensePlatesInput = $("#licensePlates");
var placeInput = $("#place");

var drawImportedTable = function (receivedData) {
    console.log("receivedData", receivedData);
    var dataDisplay = $("#table-row");
    dataDisplay.css("display", "block")
    console.log(window.importsTable);
    if (window.importsTable == null) {
        window.importsTable =
            $("#dataDisplay").DataTable({
                data: receivedData,
                columns: [
                    {data: 'date'},
                    {data: 'time'},
                    {data: 'remainingLiters'},
                    {data: 'licensePlates'},
                    {data: 'place'},
                ]
            });
    } else {
        window.importsTable.clear();
        window.importsTable.rows.add(receivedData);
        window.importsTable.draw();
    }

    $("#dataDisplay").on('click', 'tbody td', function () {
        var currentRowData = window.importsTable.row(this).data();
        dateInput.val(currentRowData.date);
        timeInput.val(currentRowData.time);
        litersInput.val(currentRowData.remainingLiters);
        licensePlatesInput.val(currentRowData.licensePlates);
        placeInput.val(currentRowData.place);
        entryId = currentRowData.id;
        loadId = currentRowData.loadId;
        dialog.dialog('open');
    });

};

$("#updateForm").on("submit", function(event) {
    event.preventDefault();
    var formData = $('#updateForm').serializeArray().reduce(function(obj, item) {
        obj[item.name] = item.value;
        return obj;
    }, {});
    formData.id = entryId;
    formData.loadId = loadId;
    $.ajax({
        url: 'csv_update.php',
        method: 'POST',
        contentType: "application/json",
        dataType: "json",
        data: JSON.stringify(formData),
        success: function (e) {
            dialog.dialog("close");
            drawImportedTable(e);
        }
    });
    return false;
});

$("#dialogDelete").on("click", function (e) {
    e.preventDefault();
    $.ajax({
        url: 'csv_delete.php',
        method: 'POST',
        data: {
            id: entryId,
            loadId: loadId
        },
        success: function (e) {
            drawImportedTable(JSON.parse(e));
            dialog.dialog("close");
        }
    })
});

$("#dialogCancel").on("click", function(e){
    e.preventDefault();
    dialog.dialog("close");
});

$("#accordion").accordion({
   collapsible: true,
    heightStyle: "content"
});

var wipeFileNamesAndCloseTheDialog = function() {
    var fileNameRows = $(".fileNameRow");
    for (var i = 1; i < fileNameRows.length; i++) {
        fileNameRows.eq(i).remove();
    }
    var fileNames = $(".fileName");
    for (var i = 1; i < fileNames.length; i++) {
        fileNames.eq(i).remove();
    }
    fileNames.eq(0).text('');
    confirmDialog.dialog("close");
}

var initializeConfirmButton = function (formData, type) {
    $("#file-dialog-confirm").on('click', function() {
        wipeFileNamesAndCloseTheDialog();
        performImport(formData, type);
    });
};

$("#file-dialog-cancel").on('click', function () {
    wipeFileNamesAndCloseTheDialog();
});

var allowImport = function(formData, type) {
    $.ajax({
        processData: false,
        contentType: false,
        data: formData,
        type: type,
        url: 'csv_check.php',
        success: function (response) {
            console.log("re", response);
            var receivedData = JSON.parse(response);
            if (receivedData.message) {
                var receivedData = JSON.parse(response);
                var fileNameRow = $(".fileNameRow").eq(0);
                var fileName = $(".fileName");
                for (var i = 0; i < receivedData.fileNames.length; i++) {
                    var fileNameRowClone = fileNameRow.clone();
                    var fileNameClone = fileName.clone();
                    fileNameClone.text(receivedData.fileNames[i]);
                    fileNameRowClone.append(fileNameClone);
                    $("#fileNameBody").append(fileNameRowClone);
                }
                fileNameRow.remove();
                confirmDialog.dialog("open");
                initializeConfirmButton(formData, type);
                return false;
            }
            return true;
        }
    });
};

var performImport = function (formData, type) {
    $.ajax({
        processData: false,
        contentType: false,
        data: formData,
        type: type,
        url: 'csv.php',
        success: function (response) {
            var displayElement;
            if (response) {
                displayElement = $("#import-fail");
            } else {
                displayElement = $("#import-success");
            }
            displayElement.css("display", "block");
            setTimeout(function () {
                displayElement.css("display", "none");
            }, 2000);
        }
    });
};

$("#fileForm").submit(function (event) {

    var formData = new FormData($("#fileForm")[0]);
    var type = $(this).attr('method');
    var url = $(this).attr('action');
    if (allowImport(formData, type)) {
        performImport(formData, type, action);
    }

    /*$.ajax({
        processData: false,
        contentType: false,
        data: new FormData($("#fileForm")[0]),
        type: $(this).attr('method'),
        url: $(this).attr('action'),
        success: function (response) {
            var objResponse = JSON.parse(response);
            window.drawImportedTable(objResponse);
        }
    });*/
    return false;
});

var handleLastUsedFilesClicks = function (dataTable) {
    $("#filesDisplay").on('click', 'tbody td', function () {
        var currentRowData = dataTable.row(this).data();
        $.ajax({
           url: 'data_of_load.php',
           data: {
               load_id: currentRowData.id
           },
           method: 'POST',
           success: function (m) {
               window.drawImportedTable(JSON.parse(m));
           }
        });
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
