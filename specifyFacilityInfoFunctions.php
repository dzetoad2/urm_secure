<?php 
require_once 'facilityFunctions.php';

//ADDS A ROW TO THE USERFACILITY TABLE.
//return the # of rows affected ! 
function addUserFacility($userId,$facilityId,$facilityTypeId,   $isMoreThan26TLB,
$isCriticalAccessHospital,
$totalFacilityBeds,
$medicalSurgicalIntensiveCareBeds,
$neoNatalIntensiveCareBeds,
$otherIntensiveCareBeds,
$pediatricIntensiveCareBeds){  //according to the user, set the facilityId.

    $userId = cleanStrForDb($userId);
	$facilityId = cleanStrForDb($facilityId);
	$facilityTypeId = cleanStrForDb($facilityTypeId);
	$isMoreThan26TLB = cleanStrForDb($isMoreThan26TLB);
	$isCriticalAccessHospital = cleanStrForDb($isCriticalAccessHospital);
	$totalFacilityBeds = cleanStrForDb($totalFacilityBeds);
	$medicalSurgicalIntensiveCareBeds = cleanStrForDb($medicalSurgicalIntensiveCareBeds);
	$neoNatalIntensiveCareBeds = cleanStrForDb($neoNatalIntensiveCareBeds);
	$otherIntensiveCareBeds = cleanStrForDb($otherIntensiveCareBeds);
	$pediatricIntensiveCareBeds = cleanStrForDb($pediatricIntensiveCareBeds);
	//set the facilityId to be the rowId coming in.
	//1. regex verify the facilityId is a positive integer, and is a positive match of an id from facility table.
	if(  !preg_match('/^[0-9]+$/',$facilityId)  ||  !isValidFacilityId($facilityId)){   //if not digit, or if not validfacility id, then return false;
		throwMyExc('Error: facilityId is not a digit, or facilityId is not a valid Id in the facility table.');
	}
	if(false === hasUserFacilityEntry($userId,$facilityId)){
	   return insertUserFacilityEntry($userId,$facilityId,$facilityTypeId,$isMoreThan26TLB,
				$isCriticalAccessHospital,
				$totalFacilityBeds,
				$medicalSurgicalIntensiveCareBeds,
				$neoNatalIntensiveCareBeds,
				$otherIntensiveCareBeds,
				$pediatricIntensiveCareBeds);
	}else{
	   return 0;  // 'hasuserfaciliityr' returned 0.   so already has it. so return "0 rows"
	}
	  
}

 




















//
//{
//
//	//write to db.
//	  //taken out 'serviceCode, '
//	 mysql_query("INSERT INTO customFacility (userid, facilityTypeId,name, address,city,state,zip,
//		     phone,isMoreThan26TLB,isCriticalAccessHospital, totalFacilityBeds,
//		     medicalSurgicalIntensiveCareBeds, neoNatalIntensiveCareBeds, otherIntensiveCareBeds,
//		     pediatricIntensiveCareBeds)
//
//		     VALUES (
//		     ".$userId.",
//		     0,
//		     '".$name."',
//		     '".$address."',
//		     '".$city."',
//		     '".$state."',
//		     '".$zip."',
//		     '".$phone."',
//		     '".$isMoreThan26TLB."',
//		     '".$isCriticalAccessHospital."',
//		     ".$totalFacilityBeds.",
//		     ".$medicalSurgicalIntensiveCareBeds.",
//		     ".$neoNatalIntensiveCareBeds.",
//		     ".$otherIntensiveCareBeds.",
//		     ".$pediatricIntensiveCareBeds." 
//	   )");
//	 $r = mysql_affected_rows();
//	 if(!$r){
//	 	throwMyExc("Affected rows was false, not a number");
//	 }
//	 return $r;
//	
//}