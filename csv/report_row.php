<?php

require_once('vehicle_record.php');

class ReportRow {

	public $totalLitersSum;
	public $monthStartLitersRemainings;
	public $monthEndLitersRemainings;
	public $usedLiters;
	public $litersAverage;
	public $speedometerMonthStart;
	public $speedometerMonthEnd;
	public $importedFrom;

	public static function fromDbRow($row) {
		$vehicleRecord = VehicleRecord::fromReportRow($row);
		$reportInstance = new self();
		$reportInstance -> monthStartLitersRemainings =
			$vehicleRecord -> firstTankMonthStart +
			$vehicleRecord -> secondTankMonthStart;
		$reportInstance -> monthEndLitersRemainings =
			$vehicleRecord -> firstTankMonthEnd +
			$vehicleRecord -> secondTankMonthEnd;
		$reportInstance -> totalLitersSum =
			$reportInstance -> monthStartLitersRemainings +
			$reportInstance -> monthEndLitersRemainings;
		$reportInstance -> usedLiters =
			$reportInstance -> monthStartLitersRemainings -
			$reportInstance -> monthEndLitersRemainings;
		$reportInstance -> speedometerMonthStart =
			$vehicleRecord -> speedometerMonthStart;
		$reportInstance -> speedometerMonthEnd =
			$vehicleRecord -> speedometerMonthEnd;
		$reportInstance -> litersAverage =
			$reportInstance -> usedLiters * 100 /
			($reportInstance -> speedometerMonthStart -
			 $reportInstance -> speedometerMonthEnd);
		$reportInstance -> importedFrom =
			$vehicleRecord -> fileName;
		return $reportInstance;
	}

}