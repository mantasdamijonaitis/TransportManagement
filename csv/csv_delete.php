<?php
require_once('FuelLoad.php');
$dbc = mysqli_connect( 'localhost', 'root', '', 'university_project' );
mysqli_set_charset($dbc, "utf8");
$idToDelete = $_POST['id'];
$loadId = $_POST['loadId'];
$query = $dbc->prepare("DELETE FROM prad_d WHERE id = ? AND load_id = ?");
$query->bind_param('ii', $idToDelete, $loadId);
$query->execute();
$query = $dbc->prepare("SELECT * FROM prad_d WHERE load_id = ?");
$query->bind_param('i', $loadId);
$query->execute();
$loadIdResults  = $query->get_result();
$itemsArray = array();
while ($row = mysqli_fetch_array($loadIdResults)) {
	$itemFromDatabase = new FuelLoad($row);
	array_push($itemsArray, $itemFromDatabase);
}
echo json_encode( $itemsArray);


