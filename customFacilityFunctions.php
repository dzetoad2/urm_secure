<?php

require_once('validationFunctions.php');
require_once('customFacilityFullBean.php');

function createCustomFacility($userId,
$name,
$address,
$city,
$state,
$zip,
$phone,
$isOutpatientPRCFlag,
$isMoreThan26TLB,
$isCriticalAccessHospital,
//$serviceCode,
$totalFacilityBeds,
$medicalSurgicalIntensiveCareBeds,
$neoNatalIntensiveCareBeds,
$otherIntensiveCareBeds,
$pediatricIntensiveCareBeds   )

{
	$facilityTypeId = 8;
	if($isCriticalAccessHospital == 'yes'){
		$facilityTypeId = 2;
	}
	if($isOutpatientPRCFlag=='isOutpatientPRC'){
		$facilityTypeId = 9; //outpatient prc, the newest type. if
	}
	$facilityTypeId = cleanStrForDb($facilityTypeId);
	
	$userId = cleanStrForDb($userId);
	$name = cleanStrForDb($name);
	$address = cleanStrForDb($address);
	$city = cleanStrForDb($city);
	$state = cleanStrForDb($state);
	$zip = cleanStrForDb($zip);
	$phone = cleanStrForDb($phone);
	$isMoreThan26TLB = cleanStrForDb($isMoreThan26TLB);
	$isCriticalAccessHospital = cleanStrForDb($isCriticalAccessHospital);
//	$serviceCode = cleanStrForDb($serviceCode);
	$totalFacilityBeds = cleanStrForDb($totalFacilityBeds);
	$medicalSurgicalIntensiveCareBeds = cleanStrForDb($medicalSurgicalIntensiveCareBeds);
	$neoNatalIntensiveCareBeds = cleanStrForDb($neoNatalIntensiveCareBeds);
	$otherIntensiveCareBeds = cleanStrForDb($otherIntensiveCareBeds);
	$pediatricIntensiveCareBeds = cleanStrForDb($pediatricIntensiveCareBeds);

	//write to db.
	 $res = mysql_queryCustom("INSERT INTO customFacility (userid, facilityTypeId, name, address,city,state,zip,
		     phone,isMoreThan26TLB,isCriticalAccessHospital, totalFacilityBeds,
		     medicalSurgicalIntensiveCareBeds, neoNatalIntensiveCareBeds, otherIntensiveCareBeds,
		     pediatricIntensiveCareBeds)

		     VALUES (
		     ".$userId.",".
		     $facilityTypeId.",
		     '".$name."',
		     '".$address."',
		     '".$city."',
		     '".$state."',
		     '".$zip."',
		     '".$phone."',
		     '".$isMoreThan26TLB."',
		     '".$isCriticalAccessHospital."',
		     ".$totalFacilityBeds.",
		     ".$medicalSurgicalIntensiveCareBeds.",
		     ".$neoNatalIntensiveCareBeds.",
		     ".$otherIntensiveCareBeds.",
		     ".$pediatricIntensiveCareBeds." 
	   )");
   	 if($res===false){
   	 	$em='createCustomFacility(): query failed';
   	 	throwMyExc($em);
   	 }
	 $r = mysql_affected_rows();
	 if($r!=0  && $r!=1){
	 	throwMyExc("createCustomFacility: Affected rows was not 0 and not 1 ");
	 }
	 return $r;
	
}

function updateCustomFacility($customFacilityId, $userId,
$name,
$address,
$city,
$state,
$zip,
$phone,
$isMoreThan26TLB,
$isCriticalAccessHospital,
$totalFacilityBeds,
$medicalSurgicalIntensiveCareBeds,
$neoNatalIntensiveCareBeds,
$otherIntensiveCareBeds,
$pediatricIntensiveCareBeds   )

{
	$userId = cleanStrForDb($userId);
	$name = cleanStrForDb($name);
	$address = cleanStrForDb($address);
	$city = cleanStrForDb($city);
	$state = cleanStrForDb($state);
	$zip = cleanStrForDb($zip);
	$phone = cleanStrForDb($phone);
	$isMoreThan26TLB = cleanStrForDb($isMoreThan26TLB);
	$isCriticalAccessHospital = cleanStrForDb($isCriticalAccessHospital);
	$totalFacilityBeds = cleanStrForDb($totalFacilityBeds);
	$medicalSurgicalIntensiveCareBeds = cleanStrForDb($medicalSurgicalIntensiveCareBeds);
	$neoNatalIntensiveCareBeds = cleanStrForDb($neoNatalIntensiveCareBeds);
	$otherIntensiveCareBeds = cleanStrForDb($otherIntensiveCareBeds);
	$pediatricIntensiveCareBeds = cleanStrForDb($pediatricIntensiveCareBeds);

    if($isCriticalAccessHospital=='yes'){
	    $ftiStr=' facilityTypeId=2, ';
    }elseif( getIsCriticalAccessHospitalForCustomFacility($customFacilityId)=='yes' && $isCriticalAccessHospital!='yes'){
    	$ftiStr=' facilityTypeId=8, '; //resets as 'undefined' type, if this is a change from yes to no.
    }else{
		//  it was no, and still is no. so CHANGE NOTHING w.r.t. facil type.  
		$ftiStr=' '; // so this is not QUITE what we want... need to get the current state of critaccesshosp.
	}
	$ftiStr = cleanStrForDb($ftiStr);
	
	 $res = mysql_queryCustom('update customFacility 
	         set 
	         '.$ftiStr.'  
	         userid='.$userId.', 
	         name="'.$name.'", 
	         address="'.$address.'", 
	         city="'.$city.'", 
	         state="'.$state.'", 
	         zip="'.$zip.'", 
		     phone="'.$phone.'", 
		     isMoreThan26TLB="'.$isMoreThan26TLB.'",  
		     isCriticalAccessHospital="'.$isCriticalAccessHospital.'", 
		     totalFacilityBeds='.$totalFacilityBeds.', 
		     medicalSurgicalIntensiveCareBeds='.$medicalSurgicalIntensiveCareBeds.',  
		     neoNatalIntensiveCareBeds='.$neoNatalIntensiveCareBeds.',  
		     otherIntensiveCareBeds='.$otherIntensiveCareBeds.', 
		     pediatricIntensiveCareBeds='.$pediatricIntensiveCareBeds.' 
		     where id='.$customFacilityId
	   );
	 if($res===false){
	 	$em='updateCustomFacility(): query failed';
	 	throwMyExc($em);
	 }
	 $r = mysql_affected_rows(); //gives -1 if the last query failed
	 if($r===false){
	 	throwMyExc("updateCustomFacility: Affected rows was false, not a number");
	 }elseif($r===-1){
	 	$str = 'updateCustomFacility(): '. $r.', mysqlerror: '.mysql_error();
	 	return $str;
	 }
	 if($r!=1 && $r!=00){
	 	$em='updateCustomFacility(): affected rows neither 0 nor 1';
	 }
	 return $r; // better be 0 or 1 or its wroooong.
}//updatecustomfacility func


function getCustomFacilityFullBean($customFacilityId){
	$customFacilityId = cleanStrForDb($customFacilityId);
	$r = mysql_queryCustom('select id, userId, facilityTypeId, name, address,
	     city, state, zip, phone, isMoreThan26TLB, isCriticalAccessHospital, totalFacilityBeds,
	     medicalSurgicalIntensiveCareBeds, neoNatalIntensiveCareBeds,
	     otherIntensiveCareBeds, pediatricIntensiveCareBeds,
	     completionStatus      from customFacility where id='.$customFacilityId);
	if($r===false){
		 throwMyExc('getcustomfacilityfullbean: query fail, customfacilityid: '.$customFacilityId);
	}
	$n = mysql_num_rows($r);
	if($n!=1){
		 throwMyExc('getcustomfacilityfullbean: num rows != 1, corruption');
	}
	$row = mysql_fetch_array($r);
	$cfBean = new CustomFacilityFullBean();
	$cfBean->facilityTypeId = $row['facilityTypeId'];
	
	$cfBean->userId = $row['userId'];
	$cfBean->name = $row['name'];
	$cfBean->address = $row['address'];
	$cfBean->city = $row['city'];
	$cfBean->state = $row['state'];
	$cfBean->zip = $row['zip'];
	$cfBean->phone = $row['phone'];
	
	$cfBean->isMoreThan26TLB = $row['isMoreThan26TLB'];
	$cfBean->isCriticalAccessHospital = $row['isCriticalAccessHospital'];
	$cfBean->totalFacilityBeds = $row['totalFacilityBeds'];
	$cfBean->medicalSurgicalIntensiveCareBeds = $row['medicalSurgicalIntensiveCareBeds'];
    $cfBean->neoNatalIntensiveCareBeds = $row['neoNatalIntensiveCareBeds'];
    $cfBean->otherIntensiveCareBeds = $row['otherIntensiveCareBeds']; 
    $cfBean->pediatricIntensiveCareBeds = $row['pediatricIntensiveCareBeds'];
    
    return $cfBean;
}




function getMyCustomFacilitiesRowsHtml($userId){
	/*
	 * select userFacility.id, facility.name from user
	 join userFacility on user.id = userFacility.userid
	 join facility on userFacility.facilityId = facility.id
	 where user.username = 'admin'
	 */
	$userId = cleanStrForDb($userId);
	$result = mysql_queryCustom("  select customFacility.id, customFacility.name, customFacility.address, 
	customFacility.city, customFacility.state, customFacility.zip, facilityType.title, customFacility.facilityTypeId AS
	  facilityTypeId  from customFacility
	left join facilityType on facilityType.id = customFacility.facilityTypeId 
	where userid = ".$userId." ");           //check un/pw against db.
	if($result === FALSE){
		$em='getMyCustomFacilitiesRowsHtml: query fail';
		throwMyExc($em);
	}
	$numrows = mysql_num_rows($result);
	if($numrows===false){
		$em='getMyCustomFacilitiesRowsHtml: numrows false';
		throwMyExc($em);
	}
	if ($numrows == 0){
		return " ";
	}
	$o = '';
	while($row = mysql_fetch_array($result))
	{
		$title = $row['title'];
		if(!$title ||  trim($title=="")){
		  $title = "UNK";
		}
		$customFacilityTypeIdClass = 'grayText';
		$typeLink = $title;
		$facilityTypeId = $row['facilityTypeId'];
		if($facilityTypeId != 9  &&  $facilityTypeId != 2){
			//type is clickable.       
		  $customFacilityTypeIdClass = 'customFacilityTypeId';
		  $typeLink = '<a class="unclickable" href="" >'.$title.'</a>';
		}
		$editCell = '<td ><img class="editCustomFacility" src="images/b_edit.png"/></td>';
		$o .=  '<tr class="customFacilityRow" id="'.$row['id'].'">'.$editCell.'<td class="nameCell" id="'.$row['name'].'">'.$row['name'].'</td><td>'.$row['address'].'</td><td>'.$row['city'].
  		           '</td><td>'.$row['state'].'</td><td>'.$row['zip'].'</td><td class="'.$customFacilityTypeIdClass.'" id="'.$row['facilityTypeId'].'">'.$typeLink.'</td></tr>';
		 
	}
	return $o;
	 
}

function getIsCriticalAccessHospitalForCustomFacility($customFacilityId){
	$customFacilityId = cleanStrForDb($customFacilityId);
	$res = mysql_queryCustom('select isCriticalAccessHospital from customFacility where id='.$customFacilityId);
	if($res===false){
		throwMyExc('getiscriticalaccesshospital(): query fail');
	}
	$row = mysql_fetch_array($res);
	if($row===false){
		throwMyExc('getiscriticalaccesshospital(): fetcharray row is false, fail');
	}
	$isCAH = $row['isCriticalAccessHospital'];
	if($isCAH=='yes' || $isCAH=='no' || $isCAH=='na'){
		return $isCAH;
	}else{
		throwMyExc('getiscriticalaccesshospital(): value is: '.$isCAH.', and is not valid : not yes, nor no, nor na.   ');
	}
}

function getStatesRowsHtml($state){
	//state input is which one should be 'selected'.   selected="selected" for an option.
	//get rows of option element with id and name from state table
	$state=trim($state);
	$res = mysql_queryCustom('select id,name from state  order by name');
	if($res===false){
		throwMyExc('getstatesrowshtml: query fail');
	}
	$o='';
	$selStr='';
	$stateFound = false;
	while($row = mysql_fetch_array($res)){
		if($state==$row['name']){
			$selStr='selected="selected"';
			$stateFound=true;
		}
		$o.= '<option id="'.$row['id'].'" '.$selStr.' >'.$row['name'].'</option>';
		$selStr='';
	}
	if($stateFound===false){
		$o = '<option id="0" selected="selected">Choose a State</option>' . $o;
	}
	return $o; 
	
}
function isValidState($state){
	//if the state is not a 2char combo found in db, return false. else return true.
	$state = cleanStrForDb($state);
	$res = mysql_queryCustom('select count(name) as c from state where name="'.$state.'"   ');
	$row = mysql_fetch_array($res);
	$c = $row['c'];
	if($c==='1'){
		return true;
	}elseif($c==='0'){
		return false;
	}else{
		throwMyExc('isvalidstate:  count in state table is not 0 and not 1!');
	}
}