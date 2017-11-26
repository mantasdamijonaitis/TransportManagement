var drawTable = function (receivedData) {
    console.log("receivedData", receivedData);
    var dataDisplay = $("#dataDisplay");
    dataDisplay.css("display", "table")
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
            console.log("objResponse", objResponse)
            window.drawTable(objResponse);
        }
    });
    return false;
});


