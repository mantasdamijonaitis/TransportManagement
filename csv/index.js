var dialog = $( "#dialog" ).dialog({
    autoOpen: false
});

var idInput = $("#id");
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
        console.log("currentRowData", currentRowData);
        console.log("currentRowData.time", currentRowData.time);
        idInput.val(currentRowData.id);
        dateInput.val(currentRowData.date);
        timeInput.val(currentRowData.time);
        litersInput.val(currentRowData.remainingLiters);
        licensePlatesInput.val(currentRowData.licensePlates);
        placeInput.val(currentRowData.place);
        dialog.dialog('open');
    });

};

$("#dialogDelete").on("click", function (e) {

});

$("#dialogCancel").on("click", function(e){
    e.preventDefault();
    dialog.dialog("close");
});

$("#accordion").accordion({
   collapsible: true,
    heightStyle: "content"
});

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
