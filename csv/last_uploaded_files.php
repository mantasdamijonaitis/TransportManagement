<?php
$dbc = mysqli_connect( 'localhost', 'root', '', 'university_project' );
$lastUploadedFiles = mysqli_query($dbc, 'select id, Data, filename from csv_load WHERE Data >= DATE(NOW()) - INTERVAL 21 DAY ORDER BY Data desc' );
$responseArray = array();
while ($row = mysqli_fetch_array($lastUploadedFiles)) {
	$receivedObject = new stdClass();
	$receivedObject -> id = $row['id'];
	$receivedObject -> data = $row['Data'];
	$receivedObject -> fileName =  $row['filename'];
	array_push($responseArray, $receivedObject);
}
?>
<?php echo json_encode( $responseArray ); ?>
