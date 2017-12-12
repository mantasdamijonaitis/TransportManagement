<?php

?>

<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/></head>
    <link rel="stylesheet" type="text/css" href="/DataTables/datatables.min.css"/></head>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css"/></head>
</head>
<body>
<div class="container" id="accordion">
    <h3>Failai, su kuriais buvo dirbama per paskutines 21 dienas</h3>
    <div class="row">
        <div class="row" id="last_files_row" style="display: none">
            <table id="filesDisplay" class="display table table-bordered table-responsive table-hover table-striped">
                <thead>
                    <tr>
                        <th>Įkėlimo data</th>
                        <th>Įkėlimo laikas</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="row" id="table-row" style="display: none">
            <h3>Iš CSV failo gautos reikšmės</h3>
            <table id="dataDisplay" class="display table table-bordered table-responsive table-hover table-striped">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Laikas</th>
                    <th>Litrai</th>
                    <th>Valstybiniai numeriai</th>
                    <th>Šalis</th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <th>Data</th>
                    <th>Laikas</th>
                    <th>Litrai</th>
                    <th>Valstybiniai numeriai</th>
                    <th>Šalis</th>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <h3>Naujo CSV failo įkėlimas</h3>
    <div class="row">
        <div class="col-lg-4 col-md-4 col-sm-4">
            <div class="alert alert-success" id="import-success" style="display: none;">
                <strong>Sveikiname!</strong> Importas įvykdytas sėkmingai.
            </div>
            <div class="alert alert-danger" id="import-fail" style="display: none">
                <strong>Klaida!</strong> Nepavyko įvykdyti importo.
            </div>
            <form id="fileForm" method="post" enctype="multipart/form-data" action="csv.php" target="csviukas">
                <div class="form-group">
                    <input name="csv" type="file" /> <br>
                    <input name="auto" type="text" value="HUT662" class="form-control"/> <br>
                    <input name="ok" type="submit" value="Ikelti" class="btn btn-default" /> <br>
                    <iframe name="csviukas" style="visibility:hidden;display:none"></iframe>
                </div>
            </form>
        </div>
    </div>
    <h3>Automobilio ataskaita</h3>
    <div class="row">
        <form>
            <table class="table table-bordered table-responsive">
                <thead>
                    <tr>
                        <td>Likutis mėnesio pabaigoje „Bakas 1“</td>
                        <td>Likutis mėnesio pabaigoje „Bakas 2“</td>
                        <td>Spidometro dabartiniai parodymai</td>
                        <td>Likutis mėnesio pradžioje „Bakas 1“ </td>
                        <td>Likutis mėnesio pradžioje „Bakas 2“</td>
                        <td>Spidometro parodymai mėnesio pradžioje</td>
                        <td>Vairuotojas</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input class="form-control" /></td>
                        <td><input class="form-control" /></td>
                        <td><input class="form-control" /></td>
                        <td><input class="form-control" /></td>
                        <td><input class="form-control" /></td>
                        <td><input class="form-control" /></td>
                        <td><input class="form-control" /></td>
                    </tr>
                </tbody>
            </table>
            <div class="btn-group">
                <button class="btn btn-primary">Skaičiuoti</button>
                <button class="btn btn-success">Kitas</button>
                <button class="btn btn-warning">Praleisti</button>
            </div>
        </form>
        <h4>Skaičiavimo rezultatai</h4>
        <table class="table table-bordered table-hover table-striped table-responsive">
            <thead>
                <tr>
                    <td>Faktiškai sunaudota kuro</td>
                    <td>Nuvažiuota kilometrų</td>
                    <td>Kuro vidurkis 100 km</td>
                    <td>Bendras bakų likutis</td>
                    <td>Ataskaitos pildymo data</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div id="dialog" title="Įrašo redagavimas">
    <form role="form" id="updateForm">
        <div class="form-group">
            <label>Data</label>
            <input type="text" class="form-control" name="date" id="date"/>
        </div>
        <div class="form-group">
            <label>Laikas</label>
            <input type="text" class="form-control" name="time" id="time"/>
        </div>
        <div class="form-group">
            <label>Likę litrai</label>
            <input type="text" class="form-control" name="remainingLiters" id="remainingLiters" />
        </div>
        <div class="form-group">
            <label>Valstybiniai numeriai</label>
            <input type="text" class="form-control" name="licensePlates" id="licensePlates" />
        </div>
        <div class="form-group">
            <label>Vieta</label>
            <input type="text" class="form-control" name="place" id="place"/>
        </div>
        <div class="btn-group">
            <button class="btn btn-success" id="dialogUpdate">Saugoti</button>
            <button class="btn btn-danger" id="dialogDelete">Šalinti</button>
            <button class="btn btn-primary" id="dialogCancel">Atšaukti</button>
        </div>
    </form>
</div>

<div id="confirm-dialog">
    <h4>Tokio dydžio failai jau įkelti!</h4>
    <h5>Failų pavadinimai:</h5>
    <table class="table table-bordered table-striped table-responsive table-hover">
        <tbody id="fileNameBody">
            <tr class="fileNameRow">
                <label class="fileName"></label>
            </tr>
        </tbody>
    </table>
    <div class="btn-group">
        <button class="btn btn-success" id="file-dialog-confirm">Tęsti importą</button>
        <button class="btn btn-danger" id="file-dialog-cancel">Nutraukti importą</button>
    </div>
</div>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/DataTables/datatables.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="/csv/index.js"></script>
</body>
</html>
