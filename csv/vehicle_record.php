<?php

class VehicleRecord {

	public $id = "";
	public $vehicle = "";
	public $loadId = "";
	public $firstTankMonthEnd = "";
	public $secondTankMonthEnd = "";
	public $speedometerMonthEnd = "";
	public $firstTankMonthStart = "";
	public $secondTankMonthStart= "";
	public $driver = "";
	public $order = "";

	public static function fromDatabaseRow($result) {
		$instance = new self();
		$instance -> id = $result['Id'];
		$instance -> vehicle = $result['Vehicle'];
		$instance -> loadId = $result['LoadId'];
		$instance -> firstTankMonthEnd = $result['FirstTankMonthEnd'];
		$instance -> secondTankMonthEnd = $result['SecondTankMonthEnd'];
		$instance -> speedometerMonthEnd = $result['SpeedometerMonthEnd'];
		$instance -> firstTankMonthStart = $result['FirstTankMonthStart'];
		$instance -> secondTankMonthStart = $result['SecondTankMonthStart'];
		$instance -> driver = $result['Driver'];
		return $instance;
	}

	public static function fromLoadIdAndPlates($loadId, $licencePlates) {
		$instance = new self();
		$instance -> loadId = $loadId;
		$instance -> vehicle = $licencePlates;
		return $instance;
	}

}