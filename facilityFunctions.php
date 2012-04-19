<?php  
require_once('urm_secure/userFacilityBean.php');

function getFacilityRowsHtml($zip,$typeAbbrev){
	$zip = cleanStrForDb($zip);
	$typeAbbrev = cleanStrForDb($typeAbbrev);
 	  $result = mysql_queryCustom('
 	  		SELECT facility.id, zip, name, address, city, state, facilityTypeAbbrev     
 	  		FROM facility 
 	  		WHERE zip REGEXP "'.$zip.'"  AND   facilityTypeAbbrev = "'.$typeAbbrev.'"  ');
      if($result === FALSE){
      	return FALSE;
      	$em='getFacilityRowsHtml: query failed';
      	throwMyExc($em);
      }
 	  $numrows = mysql_num_rows($result);
	  if ($numrows == 0){
	  	return FALSE;
	  }
	  $o = '';
	  while($row = mysql_fetch_array($result))
  	  {
  	  	$type = $row['facilityTypeAbbrev'];
  	  	    if(defined('DEBUG')){
  		      $o .=  '<tr class="facilityRow clickable" id="'.$row['id'].'"><td class="cell1" >'.$row['id'].''.'</td><td>'.$row['zip'].'</td><td class="nameCell" id="'.$row['name'].'">'.$row['name'].'</td><td>'.$type.'</td><td>'.$row['address'].'</td><td>'.$row['city'].
  		             '</td><td>'.$row['state'].'</td></tr>';
  	  	    }else{
  	  	      $o .=  '<tr class="facilityRow clickable" id="'.$row['id'].'"><td>'.$row['zip'].'</td><td class="nameCell" id="'.$row['name'].'">'.$row['name'].'</td><td>'.$type.'</td><td>'.$row['address'].'</td><td>'.$row['city'].
  		             '</td><td>'.$row['state'].'</td></tr>';
  	  	    }
  	  }
  	  return $o;
}

//checks if userFacility table has 1 or 0 entries with userid and facilityId inputs.
function hasUserFacilityEntry($userId,$facilityId){
	$userId = cleanStrForDb($userId);
	$facilityId = cleanStrForDb($facilityId);

	//check if this 'userid-facilityid' entry exists in the table yet.
	$result = mysql_queryCustom("select * from userFacility where userId=".$userId." and facilityid=".$facilityId." ");
	if($result===false){
		throwMyExc("hasUserFacilityEntry(): Fail in db select , result of query was false");
	}
	$numrows = mysql_num_rows($result);
	if($numrows===0){
		return false;
	}else if($numrows === 1){
		return true;
	}else if($numrows > 1){
		throwMyExc(" hasUserFacilityEntry: The numrows for the pair of userid and facilityid > 1:  db corruption! userfacility table");
	}else{
		throwMyExc(" hasUserFacilityEntry: The numrows for the pair of userid and facilityid != 1, and !=0 ,and not > 1.:  ");
	}
}

function insertUserFacilityEntry($userId,$facilityId,$facilityTypeId,$isMoreThan26TLB,
$isCriticalAccessHospital,
$totalFacilityBeds,
$medicalSurgicalIntensiveCareBeds,
$neoNatalIntensiveCareBeds,
$otherIntensiveCareBeds,
$pediatricIntensiveCareBeds){

//NOW OVERRIDE THE FACILITYTYPEID - IF  'ISCRITICAL ACCESS HOSPITAL  IS A 'yes' THEN WE NOW SET THE FACILITYTYPEID 
// TO BE '2', WHICH IS IN THE FACILITTYPE DEFINITION TABLE AS 'CRITICAL ACCESS HOSPITAL'.
	if($isCriticalAccessHospital=='yes'){
	    $facilityTypeId = 2;
	}

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
	
	$queryText = "insert into userFacility  (userId, facilityId,facilityTypeId,isMoreThan26TLB,isCriticalAccessHospital,totalFacilityBeds,
	      medicalSurgicalIntensiveCareBeds,neoNatalIntensiveCareBeds,otherIntensiveCareBeds,pediatricIntensiveCareBeds) values (".$userId.",".$facilityId.",
	      ".$facilityTypeId.",'".$isMoreThan26TLB."','".$isCriticalAccessHospital."',".$totalFacilityBeds.",".
	      $medicalSurgicalIntensiveCareBeds.",".$neoNatalIntensiveCareBeds.",".$otherIntensiveCareBeds.",".$pediatricIntensiveCareBeds.")";
	      
	if(false===mysql_queryCustom($queryText)){  //try to update the facility id num in the user row. if fail, return false.
		throwMyExc('insertUserFacilityEntry: Error inserting into userFacility table:'.$queryText.', mysqlerror: '.mysql_error()); 
	
	}	
		$debugStr = 'userId:'.$userId.',facilityId:'.$facilityId.',ftypeid:'.$facilityTypeId.',ismorethan26:'.$isMoreThan26TLB.',
		    iscritacchosp:'.$isCriticalAccessHospital.',totalfacilbeds:'.$totalFacilityBeds.', medsurg icb:'.$medicalSurgicalIntensiveCareBeds.',neonatal icb:'.$neoNatalIntensiveCareBeds.
		    ', other icb:'.$otherIntensiveCareBeds.', pediatric icb:'.$pediatricIntensiveCareBeds;
	return mysql_affected_rows();
}

function isValidFacilityId($id){
 	$id = cleanStrForDb($id);
 	 $result = mysql_queryCustom("SELECT * FROM facility WHERE id='$id'");           //check un/pw against db.
	 if($result===false){
	 	$em='isValidFacilityId: query fail';
	 	throwMyExc($em);
	 }
 	  $numrows = mysql_num_rows($result);
	  if ($numrows == 0){ //not an error : false -> its not a valid facility
	  	return FALSE;
	  }else if($numrows == 1){
      	return TRUE;
	  }else {
	  	$errorMsg='isvalidfacilityId(): numrows != 0 and != 1';
	  	throwMyExc($errorMsg);
	  }
}
function getFacilityRow($id){ //based on facility id.
	$id = cleanStrForDb($id);
	$result = mysql_queryCustom("SELECT * FROM facility WHERE id='$id'");             
	if($result===false){
		$em='getFacilityRow: query fail';
		throwMyExc($em);
	}
     $numrows = mysql_num_rows($result);
      if ($numrows != 1){
	  	$errorMsg='getFacilityRow(): numrows != 1 ';
	  	throwMyExc($errorMsg); 
	  }
	  $row = mysql_fetch_assoc($result);   //associative. this is first row of the result.
	  $name = $row['name']; //this is the timestamp of expiry
 	  $address = $row['address'];
	  $city= $row['city'];
	  $state = $row['state'];
 	  $zip = $row['zip'];
 	  $data = array();
 	  $data['id'] = $id;
 	  $data['name'] = $name;
 	  $data['address'] = $address;
 	  $data['city'] = $city;
 	  $data['state'] = $state;
 	  $data['zip'] = $zip;
 	  return $data;
}

function getFacilityRowHtml($id){
  $data = getFacilityRow($id);
  //output is html string, format is:  
     // <th>Id</th> -->
     // <th>Zip Code</th>-->
      //<th>Name</th>-->
      //<th>Street Address</th>-->
      //<th>City</th><th>State</th>-->
     if(defined('debug'))
       $idCell = '<td>'.$id.'</td>';
     else
       $idCell = '';
  $o = '<tr>'.$idCell.'<td>'.$data['zip'].'</td><td>'.$data['name'].'</td><td>'.$data['address'].'</td><td>'.$data['city'].'</td><td>'.$data['state'].'</td></tr>';  
  return $o;
}

//function getFacilityIdFromUsername($un){
//	 $un = cleanStrForDb($un);
//	 $result = mysql_ queryCustom("SELECT facilityId FROM user WHERE username='$un'");           //check un/pw against db.
//	 if($result===false){
//	 	$em='getFacilityIdFromUsername: query fail';
//	 	throwMyExc($em);
//	 }
//     $numrows = mysql_num_rows($result);
//     if ($numrows != 1){
//	  	$errorMsg=' getFacilityIdFromUsername(): numrows not 1';
//	  	throwMyExc($errorMsg);
//	 }
//	 $row = mysql_fetch_assoc($result);   //associative. this is first row of the result.
// 	 return $row['facilityId'];  
//}

//function getFacilityNameFromUsername($un){
//	$un = cleanStrForDb($un);
//	$id = getFacilityIdFromUsername($un);
//	$row = getFacilityRow($id);
//	$name = $row['name'];
//	return $name;
//}



/* Find out what other user added this facility first.       Obviously this does not have to do with custom facilities.
 * 
 * Return:  '' if theres no user. or else the username if there IS another user who added it. 
 */
function getOriginalUserUserFacilityBean($facilityId){
  /* Check the userFacility table for other user with exact same facilityId.
   * We expect to see either 0, or > 0 users.  We check for -1 in a bed column. For all other users, the data gets -1 or 'na' in each field.
   *
   * If someone else registered with real bed infos (not -1), that is a legitimate first owner.
   * If someone else registerd with -1 bed infos, AND is outpatient (fa-typeid=9), it ALSO is legitimate first owner.
   * if someone else registered with -1 bed infos, and is NOT fa-type 9, they are all 2nd owners, NOT first owners.
   *
   * facilitytypeid 9: is the outpatient case. 
   */
	$facilityId = cleanStrForDb($facilityId);
   $r = mysql_queryCustom('select * from userFacility where facilityId = '.$facilityId.' and ( totalFacilityBeds != -1  or  facilityTypeId = 9 )');
   if($r===false) 
      throwMyExc('getOriginalUserUserFacilityBean: query failed');
   $n = mysql_num_rows($r);
   if($n===0){  // should be no error, cuz there can be no original owner.
      return '';
   }elseif($n===1){
      //this is the user who first added it.
      $row = mysql_fetch_array($r);
      if($row===false) 
        throwMyExc('getOriginalUserUserFacilityBean: fetch array from the mysql resource r returned false');
      $userId = $row['userId'];
      $userFacilityBean = new UserFacilityBean();
      $userFacilityBean->userId = $userId;
      $userFacilityBean->facilityId = $row['facilityId'];
      $userFacilityBean->facilityTypeId = $row['facilityTypeId'];
      $userFacilityBean->isMoreThan26TLB = $row['isMoreThan26TLB'];
      $userFacilityBean->isCriticalAccessHospital = $row['isCriticalAccessHospital'];
      $userFacilityBean->totalFacilityBeds = $row['totalFacilityBeds'];
      $userFacilityBean->medicalSurgicalIntensiveCareBeds = $row['medicalSurgicalIntensiveCareBeds'];
      $userFacilityBean->neoNatalIntensiveCareBeds = $row['neoNatalIntensiveCareBeds'];
      $userFacilityBean->otherIntensiveCareBeds = $row['otherIntensiveCareBeds'];
      $userFacilityBean->pediatricIntensiveCareBeds = $row['pediatricIntensiveCareBeds'];
      
      return $userFacilityBean;
   }else{
    throwMyExc('getOriginalUserUserFacilityBean:  n not 0 nor 1, this is not allowed, only one user may input bed data for a facility.');
   }
}


