<?php

class FuelLoad {

	public $id;
	public $licensePlates;
	public $date;
	public $time;
	public $place;
	public $payType;
	public $remainingLiters;
	public $loadId;

	public function __construct($result) {
		$this -> id = $result['ID'];
		$this -> licensePlates = $result['Ken'];
		$this -> date = $result['Lie_d'];
		$this -> time = $result['Lie_z'];
		$this -> place = $result['Nam'];
		$this -> payType = $result['War'];
		$this -> remainingLiters = $result['Men'];
		$this -> loadId = $result['load_id'];
	}
}