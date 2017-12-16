<?php
require_once('FuelLoad.php');
$dbc = mysqli_connect( 'localhost', 'root', '', 'university_project' );
mysqli_set_charset($dbc, "utf8");
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON);
$query = $dbc->prepare(
	"UPDATE prad_d SET
 	Lie_d = ? ,
 	Lie_z = ?,
 	Men = ?,
 	Ken = ?,
 	Nam = ? 
	WHERE id = ? AND load_id = ? ");
$query->bind_param('ssdssii',
	$input->date,
	$input->time,
	$input->remainingLiters,
	$input->licensePlates,
	$input->place,
	$input->id,
	$input->loadId);
$query->execute();
$query = $dbc->prepare("SELECT * FROM prad_d WHERE load_id = ?");
$query->bind_param('i', $input->loadId);
$query->execute();
$loadIdResults  = $query->get_result();
$itemsArray = array();
while ($row = mysqli_fetch_array($loadIdResults)) {
	$itemFromDatabase = new FuelLoad($row);
	array_push($itemsArray, $itemFromDatabase);
}
echo json_encode( $itemsArray);
