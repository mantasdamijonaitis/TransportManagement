<?php

$loadId = $_GET['loadId'];

$storagePath = "./temp_arch/";

$dbc = mysqli_connect( 'localhost', 'root', '', 'university_project' );
$curl = curl_init();

$licensePlatesQuery = $dbc ->
	prepare("SELECT DISTINCT Ken from prad_d WHERE load_id = ?");
$licensePlatesQuery -> bind_param('i', $loadId);
$licensePlatesQuery -> execute();
$licensePlatesResult = $licensePlatesQuery -> get_result();
if ($licensePlatesResult -> num_rows > 0) {
	$zipFileName = $storagePath . $loadId . ".zip";
	$zip = new ZipArchive;
	$zip -> open($zipFileName, ZipArchive::CREATE);
	while ($row = mysqli_fetch_array($licensePlatesResult)) {
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => 'localhost/csv/auto_report.php?loadId=' . $loadId . '&vehicle=' . $row['Ken'],
			CURLOPT_USERAGENT => 'MD agent'
		));
		$resp = curl_exec($curl);
		$fileName = $storagePath . $row['Ken'] . ".csv";
		file_put_contents($fileName, $resp);
		$zip -> addFile($fileName, $row['Ken'] . ".csv");
		if(!curl_exec($curl)){
			die('Error: "' . curl_error($curl) . '" - Code: ' . curl_errno($curl));
		}
	}
	$zip->close();
	if (file_exists($zipFileName)) {
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=".$zipFileName);
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($zipFileName));
		readfile($zipFileName);
		array_map('unlink', glob($storagePath . "*"));
		exit;
	}
}
