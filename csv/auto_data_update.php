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

	$previousRecordQuery = $dbc -> prepare(
		" SELECT * FROM auto_data
				WHERE Id = ?" );
	$previousRecordQuery -> bind_param('i', $autoDataJsonRecord -> id);
	$previousRecordQuery -> execute();
	$previousRecordQueryResult = $previousRecordQuery -> get_result();
	if ($previousRecordQueryResult -> num_rows > 0) {
		$firstRow = mysqli_fetch_assoc($previousRecordQueryResult);
		$deltaObject = VehicleRecord::getDeltaObject($firstRow, $autoDataJsonRecord);
		$query = $dbc -> prepare(
			" SELECT * FROM auto_data 
			WHERE Vehicle = ? AND LoadId > ?");
		$query -> bind_param('si',
			$autoDataJsonRecord -> vehicle,
			$autoDataJsonRecord -> loadId);
		$query -> execute();
		$queryResult = $query -> get_result();
		if ($queryResult -> num_rows > 0) {
			while ($row = mysqli_fetch_array($queryResult)) {
				$recordToUpdate = VehicleRecord::fromDatabaseRow($row);
				$updateQuery = $dbc -> prepare(
					" UPDATE auto_data
							SET SpeedometerMonthStart = ?,
								FirstTankMonthStart = ?,
								SecondTankMonthStart = ?,
								SpeedometerMonthEnd = ?,
								FirstTankMonthEnd = ?,
								SecondTankMonthEnd = ?
								WHERE Id = ?");

				echo 'before';
				var_dump($recordToUpdate);

				$recordToUpdate->speedometerMonthStart =
					$recordToUpdate->speedometerMonthStart +
					$deltaObject -> speedometerMonthStart;
				$recordToUpdate->firstTankMonthStart =
					$recordToUpdate->firstTankMonthStart +
					$deltaObject -> firstTankMonthStart;
				$recordToUpdate->secondTankMonthStart =
					$recordToUpdate->secondTankMonthStart +
					$deltaObject -> secondTankMonthStart;

				$recordToUpdate->speedometerMonthEnd =
					$recordToUpdate->speedometerMonthEnd +
					$deltaObject -> speedometerMonthStart;

				$recordToUpdate->firstTankMonthEnd =
					$recordToUpdate->firstTankMonthEnd +
					$deltaObject -> firstTankMonthStart;

				$recordToUpdate->secondTankMonthEnd =
					$recordToUpdate->secondTankMonthEnd +
					$deltaObject -> secondTankMonthStart;

				echo 'after';
				var_dump($recordToUpdate);

				$updateQuery->bind_param('ddddddi',
					$recordToUpdate->speedometerMonthStart,
					$recordToUpdate->firstTankMonthStart,
					$recordToUpdate->secondTankMonthStart,
					$recordToUpdate->speedometerMonthEnd,
					$recordToUpdate->firstTankMonthEnd,
					$recordToUpdate->secondTankMonthEnd,
					$recordToUpdate->id);

				$updateQuery->execute();

			}
		}
	}

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
