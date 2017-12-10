<?php
require_once('FuelLoad.php');
$dbc = mysqli_connect( 'localhost', 'root', '', 'university_project' );
mysqli_set_charset($dbc, "utf8");
$inputJSON = file_get_contents('php://input');
var_dump($inputJSON);
$input = json_decode($inputJSON); //convert JSON into array
var_dump($input);
echo json_last_error_msg() ;
