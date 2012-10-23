<?php
namespace urm\urm_secure\DAO;
require_once('facilityInfoDAO.php');

use urm\urm_secure\DAO\facilityInfoDAO;
/*
 * gets the full set of surveycategory rows. 
 */
class userStatsRowListDAO{
	
	public
	  $list;
	  
	function __construct($facilityType){
		$this->list = array();
		
		$q1 = "select id, username from user where 1 order by username";
		$r1 = mysql_queryCustom($q1);
		while($row = mysql_fetch_array($r1)){
			$id = $row['id'];
			$username = $row['username'];
			//echo '----------'.$id.', '.$username;
			$r = new userStatsRowDAO($id,$username,$facilityType);
			//echo $r->toRowHtml();
			//echo '<br/>';
			$this->list[] = $r;
		}
	}
	function toRowsHtml(){
		$o = '';
		foreach($this->list as $r){
		   $o .= $r->toRowHtml();
		}
		//		$o .= 'size: '.count($this->list);
		return $o;
	}
	function toDebugAllText(){
		$o = '';
		foreach($this->list as $r){
		   $o .= $r->toDebugText();
		}
		//		$o .= 'size: '.count($this->list);
		return $o;
	}
	

}



class userStatsRowDAO {
	
	public 
	 $id, $username, $facilityIdArr, $customFacilityIdArr;
	 
	function __construct($in_id, $in_username,$facilityType){  //facilitytype can be normal , both, custom.  
		$this->id = $in_id;
		$this->username = $in_username;
		$this->facilityType = $facilityType;
		if($in_username != "dzetoad2@gmail.com"  && $in_username != "a@a.com"){
		  
		  if($facilityType === "normal" || $facilityType === "both"){
		    $this->populateFacilityIdArr();
		  }elseif($facilityType === "custom" || $facilityType === "both"){
		  	$this->populateCustomFacilityIdArr();
		  }else{
		  	$em="userstatsrowdao: facilitytype was invalid. must be normal, or custom. facilitytype was: ".$facilityType;
		  	throwMyExc($em);
		  }
		}
	}
	function populateFacilityIdArr(){
		$q="select id, facilityId from userFacility where userId = ".$this->id;
		$r = mysql_queryCustom($q);
		if($r===false){
			$em = 'populatefacilityidArr: query fail';
			throwMyExc($em);
		}
		while($row = mysql_fetch_array($r)){
			$this->facilityIdArr[] = $row['id'];
			//echo ', facilityId: '. $row['facilityId'];
		}
		//echo '<br/>';
		//print_r($this->facilityIdArr); echo '<br/>';
	}
	function populateCustomFacilityIdArr(){
		$q="select id from customFacility where userId = ".$this->id;
		$r = mysql_queryCustom($q);
		if($r===false){
			$em = 'populatecustomfacilityidArr: query fail';
			throwMyExc($em);
		}
		while($row = mysql_fetch_array($r)){
			$this->customFacilityIdArr[] = $row['id'];
		}
		//print_r($this->customFacilityIdArr);
	}
	
	
	
	
	function toRowHtml(){
		$o= '<tr><td>'.$this->id.'</td><td>'.$this->username.'</td></tr>';
		return $o;
	}
	
	function toDebugText(){
		$o=$this->id.', '. $this->username.'<br/>';
	}

	
	
	public static function getFacilityInfo($facilityId, $isCustomFacility){
		if($isCustomFacility===0){
			//userFacility table
			$q = 'select facility.name, facility.state from facility
				  join userFacility
				  on facility.id = userFacility.facilityId
				  where userFacility.id = '.$facilityId;
			
		}elseif($isCustomFacility===1){
			//customFacility table
			$q = 'select name, state from customFacility
				  where id = '.$facilityId;
		}else{
			$em="userStatsRowDao:: getfacilityname,  iscustomfacility is neither 0 nor 1";
			throwMyExc($em);
		}
		$r = mysql_queryCustom($q);
		if($r===false){
			$em="userStatsRowDao:: getfacilityname,  q fail, q: ".$q;
			throwMyExc($em);
		}
		$row = mysql_fetch_array($r);
		$fName = $row['name'];
		$fState = $row['state'];
		$fInfoDao = new facilityInfoDAO($fName,$fState);
		
		return $fInfoDao;
	
	}
	
}
