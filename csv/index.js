var elOrder = 0;
var entryId = "";
var loadId = "";
var vehicle = "";

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

var firstTankMonthEndInput = $("#firstTankMonthEnd");
var firstTankMonthStartInput = $("#firstTankMonthStart");
var secondTankMonthEndInput = $("#secondTankMonthEnd");
var secondTankMonthStartInput = $("#secondTankMonthStart");
var speedometerMonthEndInput = $("#speedometerMonthEnd");
var speedometerMonthStartInput = $("#speedometerMonthStart");
var driverInput = $("#driver");

var drawAutoData = function (currentLoadId) {
    $.ajax({
        url: 'cars_of_load.php',
        data: {
            loadId: currentLoadId
        },
        method: 'POST',
        success: function (m) {
            loadId = currentLoadId;
            //console.log("m", m);
            window.drawImportedTable(JSON.parse(m));
        }
    });
};

var drawImportedTable = function (receivedData) {
    //console.log("receivedData", receivedData);
    var dataDisplay = $("#table-row");
    dataDisplay.css("display", "block")
    if (window.importsTable == null) {
        window.importsTable =
            $("#dataDisplay").DataTable({
                data: receivedData,
                columns: [
                    {data: 'vehicle'},
                    {data: 'firstTankMonthEnd'},
                    {data: 'firstTankMonthStart'},
                    {data: 'secondTankMonthEnd'},
                    {data: 'secondTankMonthStart'},
                    {data: 'speedometerMonthEnd'},
                    {data: 'speedometerMonthStart'},
                    {data: 'driver'},
                ],
                "rowCallback": function(row, data, index) {
                    var classToAdd = "";
                    console.log("data order", data.order);
                    switch (data.order) {
                        case 0:
                            classToAdd = "currentLoadFill"
                            break;
                        case 1:
                            classToAdd = "previousLoadFill"
                            break;
                        default:
                            classToAdd = "notFilled"
                            break;
                    }
                    $(row).addClass(classToAdd);
                }
            });
    } else {
        window.importsTable.clear();
        window.importsTable.rows.add(receivedData);
        window.importsTable.draw();
    }

    $("#dataDisplay").on('click', 'tbody td', function () {
        var currentRowData = window.importsTable.row(this).data();
        firstTankMonthEndInput.val(currentRowData.firstTankMonthEnd);
        firstTankMonthStartInput.val(currentRowData.firstTankMonthStart);
        secondTankMonthEndInput.val(currentRowData.secondTankMonthEnd);
        secondTankMonthStartInput.val(currentRowData.secondTankMonthStart);
        speedometerMonthEndInput.val(currentRowData.speedometerMonthEnd);
        speedometerMonthStartInput.val(currentRowData.speedometerMonthStart);
        driverInput.val(currentRowData.driver);
        if (currentRowData.order == 1) {
            firstTankMonthStartInput.attr("readonly", true);
            secondTankMonthStartInput.attr("readonly", true);
            speedometerMonthStartInput.attr("readonly", true);
        } else {
            firstTankMonthStartInput.removeAttr("readonly");
            secondTankMonthStartInput.removeAttr("readonly");
            speedometerMonthStartInput.removeAttr("readonly");
        }
        $("#dialogReport").attr("href", "auto_report.php?loadId="
            + currentRowData.loadId + "&vehicle="
            + currentRowData.vehicle);
        entryId = currentRowData.id;
        console.log("entryId", entryId);
        vehicle = currentRowData.vehicle;
        elOrder = currentRowData.order;
        console.log("setOrder", currentRowData.order);
        dialog.dialog('open');
    });

};

$("#updateForm").on("submit", function(event) {
    event.preventDefault();
    console.log("qweqweqweq", elOrder);

    var existingFormData = {
        id: elOrder != 1 ? entryId : null,
        loadId: loadId,
        vehicle: vehicle
    };

    var formData = $('#updateForm').serializeArray().reduce(function(obj, item) {
        console.log("item.name", item.name);
        obj[item.name] = item.value;
        return obj;
    }, {});

    $.extend(existingFormData, formData);
    $.ajax({
        url: 'auto_data_update.php',
        method: 'POST',
        contentType: "application/json",
        /*dataType: "json",*/
        data: JSON.stringify(existingFormData),
        success: function (e) {
            console.log("success");
            console.log("dialog", dialog);
            dialog.dialog("close");
            drawAutoData(existingFormData.loadId);
        }, error: function (e) {
            console.log("error");
            console.log("e", e);
        }
    });
    return false;
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
    $("#filesDisplay").on('click', 'tbody td', function (e) {
        var currentRowData = dataTable.row(this).data();
        $(".odd").removeClass("selectedFile");
        $(".even").removeClass("selectedFile");
        $(e.target).parent().addClass("selectedFile");
        $(dataTable.row(this)).addClass("selectedFile");
        drawAutoData(currentRowData.id);
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
