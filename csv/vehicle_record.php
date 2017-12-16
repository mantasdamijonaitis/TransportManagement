<?php

class VehicleRecord {

	public $id;
	public $vehicle = "";
	public $loadId = "";
	public $firstTankMonthEnd = 0;
	public $secondTankMonthEnd = 0;
	public $speedometerMonthEnd = 0;
	public $speedometerMonthStart = 0;
	public $firstTankMonthStart = 0;
	public $secondTankMonthStart= 0;
	public $driver = "";
	public $order = "";
	public $fileName = "";

	public static function fromDatabaseRow($result) {
		$instance = new self();
		if (array_key_exists('Id', $result)) {
			$instance->id = $result['Id'];
		}
		if (array_key_exists('Vehicle', $result)) {
			$instance->vehicle = $result['Vehicle'];
		}
		if (array_key_exists('LoadId', $result)) {
			$instance->loadId = $result['LoadId'];
		}
		$instance -> firstTankMonthEnd = $result['FirstTankMonthEnd'];
		$instance -> secondTankMonthEnd = $result['SecondTankMonthEnd'];
		$instance -> speedometerMonthEnd = $result['SpeedometerMonthEnd'];
		$instance -> speedometerMonthStart = $result['SpeedometerMonthStart'];
		$instance -> firstTankMonthStart = $result['FirstTankMonthStart'];
		$instance -> secondTankMonthStart = $result['SecondTankMonthStart'];
		$instance -> driver = $result['Driver'];
		return $instance;
	}

	public static function fromPreviousRecord($result) {
		$instance = self::fromDatabaseRow($result);
		$instance -> firstTankMonthStart = $instance -> firstTankMonthEnd;
		$instance -> secondTankMonthStart = $instance -> secondTankMonthEnd;
		$instance -> speedometerMonthStart = $instance -> speedometerMonthEnd;
		$instance -> firstTankMonthEnd = 0;
		$instance -> secondTankMonthEnd = 0;
		$instance -> speedometerMonthEnd = 0;
		return $instance;
	}

	public static function getDeltaObject($previousDbRow, $currentJson) {
		$previousRecordInstance = self::fromDatabaseRow($previousDbRow);

		$currentRecordInstance = self::fromJson($currentJson);
		$currentRecordInstance -> speedometerMonthStart =
			$currentRecordInstance->speedometerMonthEnd -
			$previousRecordInstance -> speedometerMonthEnd;

		$currentRecordInstance -> firstTankMonthStart =
			$currentRecordInstance->firstTankMonthEnd -
			$previousRecordInstance -> firstTankMonthEnd;

		$currentRecordInstance -> secondTankMonthStart =
			$currentRecordInstance->secondTankMonthEnd -
			$previousRecordInstance -> secondTankMonthEnd;

		return $currentRecordInstance;
	}

	public static function fromLoadIdAndPlates($loadId, $licencePlates) {
		$instance = new self();
		$instance -> loadId = $loadId;
		$instance -> vehicle = $licencePlates;
		return $instance;
	}

	public static function fromJson($result) {
		$instance = new self();
		$instance -> id = $result -> id;
		$instance -> vehicle = $result -> vehicle;
		$instance -> loadId = $result -> loadId;
		$instance -> firstTankMonthEnd = $result -> firstTankMonthEnd;
		$instance -> secondTankMonthEnd = $result -> secondTankMonthEnd;
		$instance -> speedometerMonthEnd = $result -> speedometerMonthEnd;
		$instance -> speedometerMonthStart = $result -> speedometerMonthStart;
		$instance -> firstTankMonthStart = $result -> firstTankMonthStart;
		$instance -> secondTankMonthStart = $result -> secondTankMonthStart;
		$instance -> driver = $result -> driver;
		return $instance;
	}

	public static function fromReportRow($row) {
		$instance = self::fromDatabaseRow($row);
		$instance -> fileName = $row['filename'];
		return $instance;
	}

}