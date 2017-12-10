<?php
require_once('FuelLoad.php');
$selectedLoadId = $_POST['load_id'];
$dbc = mysqli_connect( 'localhost', 'root', '', 'university_project' );
mysqli_set_charset($dbc, "utf8");
$loadIdResults = mysqli_query($dbc, 'select * from prad_d WHERE load_id = ' . $selectedLoadId);
$itemsArray = array();
while ($row = mysqli_fetch_array($loadIdResults)) {
	$itemFromDatabase = new FuelLoad($row);
	array_push($itemsArray, $itemFromDatabase);
}
echo json_encode( $itemsArray);
?>
