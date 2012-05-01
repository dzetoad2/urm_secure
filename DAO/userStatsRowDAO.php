<?php
namespace urm\urm_secure\DAO;

 
class userStatsRowDAO {
	
	public 
	 $id, $username, $facilityIdArr, $customFacilityIdArr;
	 
	function __construct($in_id, $in_username){
		$this->id = $in_id;
		$this->username = $in_username;
		$this->populateFacilityIdArr();
		$this->populateCustomFacilityIdArr();
	}
	function populateFacilityIdArr(){
		$q="select id from userFacility where userId = ".$this->id;
		$r = mysql_queryCustom($q);
		if($r===false){
			$em = 'populatefacilityidArr: query fail';
			throwMyExc($em);
		}
		while($row = mysql_fetch_array($r)){
			$this->facilityIdArr[] = $row['id'];
		}
		print_r($this->facilityIdArr);
	}
	function populateCustomFacilityIdArr(){
		
	}
	
	
	
	
	function toRowHtml(){
		$o= '<tr><td>'.$this->id.'</td><td>'.$this->username.'</td></tr>';
		return $o;
	}
	
	
}

 

