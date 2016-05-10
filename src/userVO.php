<?php 

class userVO {

	public function __construct() {
	}

	private $sumRegion;
	private $sumName;

	public function getSumRegion(){
		return $this->sumRegion;
	}

	public function setSumRegion($sumRegion){
		$this->sumRegion = $sumRegion;
	}

	public function getSumName(){
		return $this->sumName;
	}

	public function setSumName($sumName){
		$this->sumName = $sumName;
	}
}

 ?>