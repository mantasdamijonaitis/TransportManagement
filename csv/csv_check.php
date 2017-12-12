<?php
$dbc = mysqli_connect( 'localhost', 'root', '', 'university_project' );
$importedFileNames = array();
$filesWithSameSize = mysqli_query($dbc, 'select filename from CSV_load WHERE filesize = ' . $_FILES['csv']['size']);
if($filesWithSameSize->num_rows > 0) {
	while ( $row = mysqli_fetch_array( $filesWithSameSize ) ) {
		array_push($importedFileNames, $row['filename']);
	}
	$alreadyImportedFileData = new stdClass();
	$alreadyImportedFileData -> message = 'FILE_ALREADY_IMPORTED';
	$alreadyImportedFileData -> fileNames = $importedFileNames;
	echo json_encode($alreadyImportedFileData);
	return;
}
