    $("#fileForm").submit(function(event) {
        var encodedData = new FormData($("#fileForm")[0]);
        var encodedType = $(this).attr('method');
        var encodedUrl = $(this).attr('action');
        console.log("encodedData", encodedData);
        console.log("encodedType", encodedType);
        console.log("encodedUrl", encodedUrl);
        $.ajax({
            processData: false,
            contentType: false,
            data: new FormData($("#fileForm")[0]),
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            success: function(response) {
               console.log(response);
            }
        });
        return false;
    });
    /*console.log("receivedObject", receivedObject);
    var dataDisplay = $("#dataDisplay");
    console.log("display", dataDisplay);*/
    /*$("#dataDisplay").DataTable({
        data: receivedObject,
        columns: [
            'Data',
            'Litrai',
            'Spidometras',
            'Šalis',
            'Ad blue suma',
            'Vairuotojo ID'
        ]
    });*/

