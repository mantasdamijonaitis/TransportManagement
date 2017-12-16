<?php
require_once('vehicle_record.php');
$dbc = mysqli_connect( 'localhost', 'root', '', 'university_project' );
mysqli_set_charset($dbc, "utf8");
$selectedLoadId = $_POST['loadId'];
$query = $dbc->prepare(
	"SELECT DISTINCT Ken FROM prad_d WHERE load_id = ?");
$query->bind_param('i', $selectedLoadId);
$query->execute();
$queryResult = $query->get_result();
$vehiclesArray = array();
$vehicleDataArray = array();
while($row = mysqli_fetch_array($queryResult)) {
	array_push($vehiclesArray, $row['Ken']);
}
$vehicleDataQuery = $dbc->prepare(
	"SELECT * FROM auto_data WHERE LoadId = ? AND Vehicle = ?" );

function getVehicleDataByLoadIdAndLicensePlates($loadId,
	$vehicleDataQuery, &$vehiclesArray, &$vehiclesDataArray, $order) {
	foreach ( $vehiclesArray as $i=>$licensePlate ) {
		$vehicleDataQuery->bind_param( 'is', $loadId, $licensePlate );
		$vehicleDataQuery->execute();
		$queryResult = $vehicleDataQuery->get_result();
		if ( $queryResult->num_rows > 0 ) {
			while ( $row = mysqli_fetch_array( $queryResult ) ) {
				if ( $order == 0 ) {
					$vehicleRecord = VehicleRecord::fromDatabaseRow( $row );
				} else {
					$vehicleRecord = VehicleRecord::fromPreviousRecord($row);
				}
				$vehicleRecord->order = $order;
				array_push( $vehiclesDataArray, $vehicleRecord);
				unset($vehiclesArray[$i]);
			}
		}
	}
}
//echo 'Vehicles array size before first iteration ' . sizeof($vehiclesArray);
if (sizeof($vehiclesArray) > 0) {
	getVehicleDataByLoadIdAndLicensePlates($selectedLoadId, $vehicleDataQuery,
		$vehiclesArray, $vehicleDataArray, 0);
}
//echo 'Vehicles array size after first iteration ' . sizeof($vehiclesArray);
$query = $dbc -> prepare("SELECT DISTINCT LoadId FROM auto_data WHERE Vehicle = ? ORDER BY LoadId DESC");
if (sizeof($vehiclesArray) > 0) {
	foreach ($vehiclesArray as $licensePlate) {
		$query->bind_param('s', $licensePlate);
		$query->execute();
		$queryResult = $query->get_result();
		if ($queryResult->num_rows > 0) {
			while($row = mysqli_fetch_array($queryResult)) {
				getVehicleDataByLoadIdAndLicensePlates($row['LoadId'], $vehicleDataQuery,
					$vehiclesArray, $vehicleDataArray, 1);
				break;
			}
		}
	}
}

if (sizeof($vehiclesArray) > 0) {
	foreach ($vehiclesArray as $licensePlate) {
		$vehicleData = VehicleRecord::fromLoadIdAndPlates($selectedLoadId, $licensePlate);
		$vehicleData->order = 2;
		array_push($vehicleDataArray, $vehicleData);
	}
}
echo json_encode($vehicleDataArray);
