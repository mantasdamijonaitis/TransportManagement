<?php

?>

<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/></head>
    <link rel="stylesheet" type="text/css" href="/DataTables/datatables.min.css"/></head>
</head>
<body>
<form id="fileForm" method="post" enctype="multipart/form-data" action="csv.php" target="csviukas">
    <input name="csv" type="file" /> <br>
    <input name="auto" type="text" value="HUT662"/> <br>
    <input name="ok" type="submit" value="Ikelti"/> <br>
    <iframe name="csviukas" style="visibility:hidden;display:none"></iframe>
</form>
<div id="dataDisplay"></div>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<!--<script type="text/javascript" src="/DataTables/datatables.min.js"></script>-->
<script type="text/javascript" src="/csv/index.js"></script>
</body>
</html>
