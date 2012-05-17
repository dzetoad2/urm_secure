<?php
namespace urm\urm_secure\DAO;


class facilityInfoDAO{
	
	private $name, $state;
	
	public function __construct($_name, $_state){
		$this->name = $_name;
		$this->state = $_state;
	}
	public function getName(){
		return $this->name;
	}
	public function getState(){
		return $this->state;
	}
}