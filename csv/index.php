<?php

?>

<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/></head>
    <link rel="stylesheet" type="text/css" href="/DataTables/datatables.min.css"/></head>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4">
            <form id="fileForm" method="post" enctype="multipart/form-data" action="csv.php" target="csviukas">
                <div class="form-group":>
                    <input name="csv" type="file" /> <br>
                    <input name="auto" type="text" value="HUT662" class="form-control"/> <br>
                    <input name="ok" type="submit" value="Ikelti" class="btn btn-default" /> <br>
                    <iframe name="csviukas" style="visibility:hidden;display:none"></iframe>
                </div>
            </form>
        </div>
    </div>
    <table id="dataDisplay" class="display table table-bordered table-responsive table-hover table-striped" style="display: none">
        <thead>
            <tr>
                <th>Data</th>
                <th>Litrai</th>
                <th>Spidometro rodmenys</th>
                <th>Šalis</th>
                <th>Ad Blue suma</th>
                <th>Vairuotojo ID</th>
            </tr>
        </thead>
        <tfoot>
        <tr>
            <th>Data</th>
            <th>Litrai</th>
            <th>Spidometro rodmenys</th>
            <th>Šalis</th>
            <th>Ad Blue suma</th>
            <th>Vairuotojo ID</th>
        </tr>
        </tfoot>
    </table>
</div>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/DataTables/datatables.min.js"></script>
<script type="text/javascript" src="/csv/index.js"></script>
</body>
</html>
