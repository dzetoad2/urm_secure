<?php 
require_once('facilityFunctions.php');
//ADDS A ROW TO THE USERFACILITY TABLE.
//return the # of rows affected ! 
function updateUserFacility($userId,$userFacilityId,  
    $isMoreThan26TLB,
    $isCriticalAccessHospital,
    $totalFacilityBeds,
    $medicalSurgicalIntensiveCareBeds,
    $neoNatalIntensiveCareBeds,
    $otherIntensiveCareBeds,
    $pediatricIntensiveCareBeds){  //according to the user, set the facilityId.

    if($isCriticalAccessHospital=='yes'){
	    $ftiStr=' facilityTypeId=2, ';  //'facilittypeid string'
	}elseif(getIsCriticalAccessHospitalForUserFacility($userFacilityId)=='yes' && $isCriticalAccessHospital!='yes'  ){
    	$ftiStr=' facilityTypeId=8, '; //resets as 'undefined' type, if this is a change from yes to no.
	}else{
		$ftiStr='';
	}
	
    $userId = cleanStrForDb($userId);
	$userFacilityId = cleanStrForDb($userFacilityId);
	$isMoreThan26TLB = cleanStrForDb($isMoreThan26TLB);
	$isCriticalAccessHospital = cleanStrForDb($isCriticalAccessHospital);
	$totalFacilityBeds = cleanStrForDb($totalFacilityBeds);
	$medicalSurgicalIntensiveCareBeds = cleanStrForDb($medicalSurgicalIntensiveCareBeds);
	$neoNatalIntensiveCareBeds = cleanStrForDb($neoNatalIntensiveCareBeds);
	$otherIntensiveCareBeds = cleanStrForDb($otherIntensiveCareBeds);
	$pediatricIntensiveCareBeds = cleanStrForDb($pediatricIntensiveCareBeds);
	
	$r = mysql_query('
	 update userFacility
	 set '.$ftiStr.'  isMoreThan26TLB="'.$isMoreThan26TLB.'", isCriticalAccessHospital="'. $isCriticalAccessHospital.'", 
         totalFacilityBeds='.$totalFacilityBeds.', medicalSurgicalIntensiveCareBeds='.$medicalSurgicalIntensiveCareBeds.', 
         neoNatalIntensiveCareBeds='.$neoNatalIntensiveCareBeds.', otherIntensiveCareBeds='.$otherIntensiveCareBeds.', 
         pediatricIntensiveCareBeds='.$pediatricIntensiveCareBeds.'  
	 where userId='.$userId.' and id='.$userFacilityId.'  
	 
	 ');
	if($r===false){
		throwMyExc('updateUserFacility: Update failed: query has: isMoreThan26TLB="'.$isMoreThan26TLB.'", isCriticalAccessHospital="'. $isCriticalAccessHospital.'", 
         totalFacilityBeds='.$totalFacilityBeds.', medicalSurgicalIntensiveCareBeds='.$medicalSurgicalIntensiveCareBeds.', 
         neoNatalIntensiveCareBeds='.$neoNatalIntensiveCareBeds.', otherIntensiveCareBeds='.$otherIntensiveCareBeds.', 
         pediatricIntensiveCareBeds='.$pediatricIntensiveCareBeds.'  
	     where userId='.$userId.' and id='.$userFacilityId.'  ');
	}else{
	 $n = mysql_affected_rows();
	 return $n;
	}
}

//assume ufbean is already populated and not null.
function getUserFacilityRowHtml($ufBean){
	if($ufBean===null){
		throwMyExc('getuserfacilityrowhtml: incoming ufbean was null');
	}
	 $o='';
	 $o.='<tr><td>'.$ufBean->zip.'</td><td>'.$ufBean->name.'</td><td>'.$ufBean->address.'</td><td>'.$ufBean->city
	  .'</td><td>'.$ufBean->state.'</td><tr/>';
	 return $o;
	
} 
 

function getUserFacilityFullBean($userFacilityId){
    $r = mysql_query('
	  select userFacility.userId, userFacility.facilityId, userFacility.isMoreThan26TLB, userFacility.isCriticalAccessHospital,
	   userFacility.totalFacilityBeds, userFacility.medicalSurgicalIntensiveCareBeds, userFacility.neoNatalIntensiveCareBeds,
	   userFacility.otherIntensiveCareBeds, userFacility.pediatricIntensiveCareBeds, 
	   facility.name, facility.zip, facility.address, facility.city, facility.state 
	  from userFacility 
	  join facility
	  on userFacility.facilityId = facility.id 
	  where userFacility.id = '.$userFacilityId);
	if($r===false){
		throwMyExc('getUserFacilityRowHtml: query failed, userfacility id was: '.$userFacilityId);
	}
	$n = mysql_num_rows($r);
	if($n==0){
		//row not found??? error!
		throwMyExc('getuserfacilityrowhtml: row not found forthis userfacil id we passed in! ');
	}elseif($n!=1){
		//error
		throwMyExc('getuserfacilityrowhtml:  numrows not 1 and not 0. >1? corruption');
	}else{
		//ok
		$row = mysql_fetch_array($r); //false if no more rows
		if($row===false){
			$em='getUserFacilityFullBean: no rows found';
			throwMyExc($em);
		}
		$ufBean = new UserFacilityBean();
		$ufBean->userId = $row['userId'];
		$ufBean->facilityId = $row['facilityId'];
		$ufBean->isMoreThan26TLB = $row['isMoreThan26TLB'];
		$ufBean->isCriticalAccessHospital = $row['isCriticalAccessHospital'];
		$ufBean->totalFacilityBeds = $row['totalFacilityBeds'];
		$ufBean->medicalSurgicalIntensiveCareBeds = $row['medicalSurgicalIntensiveCareBeds'];
		$ufBean->neoNatalIntensiveCareBeds = $row['neoNatalIntensiveCareBeds'];
		$ufBean->otherIntensiveCareBeds = $row['otherIntensiveCareBeds'];
		$ufBean->pediatricIntensiveCareBeds = $row['pediatricIntensiveCareBeds'];
		
		$ufBean->name = $row['name'];
		$ufBean->address = $row['address'];
		$ufBean->city = $row['city'];
		$ufBean->state = $row['state'];
		$ufBean->zip = $row['zip'];
		
	    return $ufBean;		
	}
}




function getIsCriticalAccessHospitalForUserFacility($userFacilityId){
	$id = $userFacilityId;
	$res = mysql_query('select isCriticalAccessHospital from userFacility where id='.$userFacilityId);
	if($res===false){
		throwMyExc('getIsCriticalAccessHospitalForUserFacility(): query fail, mysqlerror is: '.mysql_error());
	}
	$row = mysql_fetch_array($res);
	if($row===false){
		$em='no rows found';
		throwMyExc($em);
	}
	$isCAH = $row['isCriticalAccessHospital'];
	if($isCAH=='yes' || $isCAH=='no' || $isCAH=='na'){
		return $isCAH;
	}else{
		throwMyExc('getIsCriticalAccessHospitalForUserFacility(): value is: '.$isCAH.', and is not valid : not yes, nor no, nor na. mysqlerror: '.mysql_error());
	}
	//if it is "otherUserAnswered" then 
	//	we called this function in the wrong context - only is "otherUserAnswered" when we have no permission to edit this
}




















 