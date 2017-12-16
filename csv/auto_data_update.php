<?php
require_once('vehicle_record.php');
$dbc = mysqli_connect( 'localhost', 'root', '', 'university_project' );
mysqli_set_charset($dbc, "utf8");
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON);
$autoDataJsonRecord = VehicleRecord::fromJson($input);
if (empty($autoDataJsonRecord->id)) {
	$query = $dbc -> prepare (
		"INSERT INTO auto_data (Vehicle, LoadId, FirstTankMonthEnd,
			SecondTankMonthEnd, SpeedometerMonthEnd, FirstTankMonthStart,
			SecondTankMonthStart, Driver, SpeedometerMonthStart) 
		VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$query->bind_param('siddiddsi',
		$autoDataJsonRecord->vehicle,
		$autoDataJsonRecord->loadId,
		$autoDataJsonRecord->firstTankMonthEnd,
		$autoDataJsonRecord->secondTankMonthEnd,
		$autoDataJsonRecord->speedometerMonthEnd,
		$autoDataJsonRecord->firstTankMonthStart,
		$autoDataJsonRecord->secondTankMonthStart,
		$autoDataJsonRecord->driver,
		$autoDataJsonRecord->speedometerMonthStart);
	$query->execute();
} else {
	$query = $dbc -> prepare (
		" UPDATE auto_data
				SET FirstTankMonthEnd = ?,
					SecondTankMonthEnd = ?,
					SpeedometerMonthEnd = ?,
					FirstTankMonthStart = ?,
					SecondTankMonthStart = ?,
					Driver = ?,
					SpeedometerMonthStart = ?
				WHERE 
					LoadId = ? AND 
					Id = ?" );
		$query -> bind_param('ddiddsiii',
			$autoDataJsonRecord -> firstTankMonthEnd,
			$autoDataJsonRecord -> secondTankMonthEnd,
			$autoDataJsonRecord -> speedometerMonthEnd,
			$autoDataJsonRecord -> firstTankMonthStart,
			$autoDataJsonRecord -> secondTankMonthStart,
			$autoDataJsonRecord -> driver,
			$autoDataJsonRecord -> speedometerMonthStart,
			$autoDataJsonRecord -> loadId,
			$autoDataJsonRecord -> id);
		$query -> execute();
}
