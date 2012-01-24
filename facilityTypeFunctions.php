<?php
function getFacilityTypeTitleFromUsername($un){
	$un = cleanStrForDb($un);

	$result = mysql_query("select user.username, facilityType.title from user join facilityType  on user.facilityTypeid =  facilityType.id  where  user.username = '".$un."' ");
	if($result===false){
		$em='getFacilityTypeTitleFromUsername: query fail';
		throwMyExc($em);
	}
	$numrows = mysql_num_rows($result);
	if ($numrows != 1){
		$errorMsg='getFacilityTypeTitleFromUsername(): numrows not 1';
		throwMyExc($errorMsg);
	}
	$row = mysql_fetch_assoc($result);   //associative. this is first row of the result.
	return $row['title'];

}



/*
 * get the facility type rows, but do restrictions if the beds are > 25...
 * If total bed size is greater than 25 then facility type cannot 
 * have option of critical access hospital 
 */
function getFacilityTypeRowsHtml($fid, $is_cf){
	if($is_cf==0){
	  $res = mysql_query('select isMoreThan26TLB, isCriticalAccessHospital from userFacility where id = '.$fid);   //isMoreThan26TLB : q is "has 26 or more ... ?"
	  if($res===false){
	  	$em='getFacilityTypeRowsHtml: userfacility:  query fail';
	  	throwMyExc($em);
	  }
	}elseif($is_cf==1){
	  $res = mysql_query('select isMoreThan26TLB, isCriticalAccessHospital from customFacility where id = '.$fid);
	  if($res===false){
	  	$em='getFacilityTypeRowsHtml: customfacility: query fail';
	  	throwMyExc($em);
	  }
	}else throwMyExc('getfacilitytyperowshtml: is_cf is invalid ');
	$row = mysql_fetch_assoc($res);
	$isMoreThan26TLB = $row['isMoreThan26TLB'];
	$isCriticalAccessHospital = $row['isCriticalAccessHospital'];
	if($isMoreThan26TLB=='yes'  || ($isMoreThan26TLB=='no' && $isCriticalAccessHospital=='no') ){
		$cahAllowed = false;
	}elseif($isMoreThan26TLB=='no'){
		$cahAllowed = true;
	}elseif($isMoreThan26TLB=='otherUserAnswer'){
		throwMyExc('getFacilityTypeRowsHtml: ismorethan26tlb is otheruseranswer : this error should NEVER happen
		  because user is prevented from modifying the type of a [custom]facility if the bed questions were answered by the first user to choose the facility.');
	}elseif($isMoreThan26TLB=='isOutpatientPRCenter_yes' || $isMoreThan26TLB=='isOut'){
	   /*
	    * this is NOT ok.... 
	    */
		throwMyExc('getFacilityTypeRowsHtml: ismorethan26tlb  is isOutpatientPRCenter_yes, or isOut. not allowed. ismorethan26tlb: '.$isMoreThan26TLB.', fid: '.$fid
				.', is_cf: '.$is_cf);
	
	}else{
		throwMyExc('getFacilityTypeRowsHtml: ismorethan26tlb has an erroneous value - db corruption. ismorethan26tlb: '.$isMoreThan26TLB.', fid: '.$fid
		 .', is_cf: '.$is_cf);
	}
	
	//-------
	$result = mysql_query("SELECT * FROM facilityType where myOrder > 0   order by 'order' asc ");           //check un/pw against db.
	if($result === FALSE){
		throwMyExc('getFacilityTypeRowsHtml: query fail');
	}
	$numrows = mysql_num_rows($result);
	if ($numrows == 0){
		return FALSE;
	}
	$o = '';
	while($row = mysql_fetch_array($result))
	{   //critical access hosp is id=2.
		//cahAllowed: true or false.  
		//   so block the row when cahAllowed=false and id==2.
		// dont block the row :
		//       when cahAllowed=true, or when cahAllowed=false and id!=2
 	  if($row['id']!=6){  //8 is undefine, 6 is defined by another user. 
	    if( ($cahAllowed==true) || ($cahAllowed==false && $row['id']!=2) ){
 	  	 if(defined('DEBUG')){
		  $o .=  '<tr class="facilityTypeRow" id="'.$row['id'].'"><td class="cell1" id="'.$row['id'].'">'.$row['id'].''.'</td><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$row['description'].'</td></tr>';
	     }else{
	      $o .=  '<tr class="facilityTypeRow" id="'.$row['id'].'"><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$row['description'].'</td></tr>';
	     }
	    }
	  }
	}
	return $o;
}




function setUserFacilityType($userId,$userFacilityId,$facilityTypeId){
		//set the facilityTypeId to be the rowId coming in. integer.
	$userId = cleanStrForDb($userId);
	$userFacilityId = cleanStrForDb($userFacilityId);
	$facilityTypeId = cleanStrForDb($facilityTypeId);
    $result = mysql_query("update  userFacility  set facilityTypeId = ".$facilityTypeId." where id = ".$userFacilityId." and userid=".$userId."  ");   //try to update the facility id num in the user row. if fail, return false.
	if($result===false){
		throwMyExc("setUserFacilityType: query fail");
	}
	$c = mysql_affected_rows();	
	return TRUE;
}

function setCustomFacilityType($userId,$customFacilityId,$facilityTypeId){
		//set the facilityTypeId to be the rowId coming in. integer.
	$userId = cleanStrForDb($userId);
	$customFacilityId = cleanStrForDb($customFacilityId);
	$facilityTypeId = cleanStrForDb($facilityTypeId);
	 
	$result = mysql_query("update  customFacility  set facilityTypeId = ".$facilityTypeId." where id = ".$customFacilityId." and userid=".$userId."  ");   //try to update the facility id num in the user row. if fail, return false.
	if($result===false){
		throwMyExc("setCustomFacilityType: update query fail");
	}
	$c = mysql_affected_rows();	
	return TRUE;
}




/*
 * this is not implemented yet - on hold.
 */
//function getFacilityTypeOptionList(){
//	$result = mysql_query("SELECT * FROM facilityType");           //check un/pw against db.
//	if($result === FALSE){
//		throwMyExc('Error: getfacilitytypeoptionlist: query result was false');
//	}
//	$numrows = mysql_num_rows($result);
//	if ($numrows == 0){
//		throwMyExc('Error: getfacilitytypeoptionlist: numrows of facilitytype table was 0');
//	}
//	$o = '';
//	while($row = mysql_fetch_array($result))
//	{
//	     //$o .=   .$row['id'].'"><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$row['description'].'</td></tr>';
//	}
//	return $o;
//	
//}





/*
 * Not used at all!   user table has no facil typeid
 */
//function getFacilityTypeIdFromUsername($un){
//	$un = cleanStrForDb($un);
//	$result = mysql_query("SELECT facilityTypeId FROM user WHERE username='$un'");
//	if($result===false){
//		$em='getFacilityTypeIdFromUsername:query fail';
//		throwMyExc($em);
//	}
//	$numrows = mysql_num_rows($result);
//	if ($numrows != 1){
//		$errorMsg='getFacilityTypeIdFromUsername(): numrows not = 1';
//		throwMyExc($errorMsg);
//	}
//	$row = mysql_fetch_assoc($result);   //associative. this is first row of the result.
//	return $row['facilityTypeId'];
//}