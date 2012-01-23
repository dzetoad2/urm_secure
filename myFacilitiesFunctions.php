<?php
//input: username
//output:  all the rows
function getMyFacilitiesRowsHtml($userId){
	/* 
	 * select userFacility.id, facility.name from user
	   join userFacility on user.id = userFacility.userid
	   join facility on userFacility.facilityId = facility.id
	   where user.username = 'admin'
	 */
	$userId = cleanStrForDb($userId);
	
	$result = mysql_query("select userFacility.id AS id, facility.name, facility.address, facility.city, facility.state, facility.zip, facilityType.id AS facilityTypeId, facilityType.title from user
join userFacility on user.id = userFacility.userid
left join facilityType on facilityType.id = userFacility.facilityTypeId
join facility on userFacility.facilityId = facility.id
where user.id = '".$userId."' ");           //check un/pw against db.
	if($result === FALSE){
		throwMyExc('getMyFacilitiesRowsHtml: query failed');
	}
	$numrows = mysql_num_rows($result);
	if(!$numrows){
		return " ";
	}
	if ($numrows == 0){
		return " ";
	}
	$o = '';
	while($row = mysql_fetch_array($result))
	{
		$title = $row['title'];
		if(!$title ||  trim($title==""))
		  $title = "Unknown(error)";
		$userFacilityRowClass = 'grayText';
		$facilityTypeIdClass = '';
		$linkGrayClass = 'transparent';
		$editCell = '<td class="transparent"><img src="images/b_edit.png"/></td>';
		if($row['facilityTypeId']!=6  &&   $row['facilityTypeId']!=9){
		  $userFacilityRowClass = 'userFacilityRow';
		  $facilityTypeIdClass='facilityTypeId';
		  $linkGrayClass = '';
		  $editCell = '<td ><img class="editFacility" src="images/b_edit.png"/></td>';
		}  
		$dropCell = ''; //<td ><img class="editFacility" src="images/b_drop.png"/></td>';

		if(defined('DEBUG')){
		$o .=  '<tr class="'.$userFacilityRowClass.'" id="'.$row['id'].'" >'.$dropCell.$editCell.'<td>'.$row['id'].'</td><td class="nameCell" id="'.$row['name'].'">'.$row['name'].'</td><td>'.$row['address'].'</td><td>'.$row['city'].
  		           '</td><td>'.$row['state'].'</td><td>'.$row['zip'].'</td><td class="'.$facilityTypeIdClass.'" id="'.$row['facilityTypeId'].'"><a class="unclickable '.$linkGrayClass.'" href="" >'.$title.'</a></td><td class="facilityTypeId" id="'.$row['facilityTypeId'].'" >'.$row['facilityTypeId'].'</td></tr>';
		}else{
		$o .=  '<tr class="'.$userFacilityRowClass.'" id="'.$row['id'].'" >'.$dropCell.$editCell.'<td class="nameCell" id="'.$row['name'].'">'.$row['name'].'</td><td>'.$row['address'].'</td><td>'.$row['city'].
  		           '</td><td>'.$row['state'].'</td><td>'.$row['zip'].'</td><td class="'.$facilityTypeIdClass.'" id="'.$row['facilityTypeId'].'"><a class="unclickable '.$linkGrayClass.'" href="" >'.$title.'</a></td></tr>';
		}
	}
	return $o;
}

function clearFacilities($userId){
//  1.    know these are normal facil, not custom.
//  2. delete from surveyAnswer all answers corresponding to this userid and that are normal facilities.
	$userId = cleanStrForDb($userId);
	$result = mysql_query("delete from userFacility where userId = $userId");
	if($result===false)
      throwMyExc("clearFacilities: Error in delete query from userFacility");
	$n = mysql_affected_rows();
	$r2 = mysql_query("delete from surveyAnswer where userId = ".$userId." and isCustomFacility = 0");
    if($r2===false){
	  throwMyExc("clearFacilities: Error in (cascade) delete from surveyAnswer (cuz userFacility successfully deleted all facilities for this user)");
	}
	return $n;
}

function clearCustomFacilities($userId){
//  1. get list of customfacility id.  know these are custom facil, not normal.
//  2. delete from surveyAnswer all answers corresponding to this customfacilityid and userid.
	$userId = cleanStrForDb($userId);
	$result = mysql_query("delete from customFacility where userid = $userId");
	if($result===false) 
	  throwMyExc("clearCustomFacilities: mysql_query delete error");
	$n = mysql_affected_rows();
	$r2 = mysql_query("delete from surveyAnswer where userId = ".$userId." and isCustomFacility = 1");
    if($r2===false){
	  throwMyExc("clearCustomFacilities: Error in (cascade) delete from surveyAnswer (cuz userFacility successfully deleted all facilities for this user)");
	}    
	return $n;
}





