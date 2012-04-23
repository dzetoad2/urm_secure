<?php
//input: username
//output:  all the rows
require_once('activityCategoriesFunctions.php');

function getMyFacilitiesRowsHtml($userId){
	/* 
	 * select userFacility.id, facility.name from user
	   join userFacility on user.id = userFacility.userid
	   join facility on userFacility.facilityId = facility.id
	   where user.username = 'admin'
	 */
	$userId = cleanStrForDb($userId);
	
	$result = mysql_queryCustom("select userFacility.id AS id, facility.name, facility.address, facility.city, facility.state, 
	facility.zip, facilityType.id AS facilityTypeId, facilityType.myOrder AS myOrder, facilityType.title from user
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
		$userFacilityRowClass = 'userFacilityRow';
		$facilityTypeIdClass = 'grayText';
		$linkGrayClass = 'transparent';
		$editCell = '<td class="transparent"><img src="images/b_edit.png"/></td>';
		$typeLinkStr =  $title;
		if($row['myOrder'] > 0){          // clickable!
//		  $userFacilityRowClass = 'userFacilityRow';
		  $facilityTypeIdClass='facilityTypeId';
		  $linkGrayClass = ''; //makes it normal and look like clickable
		  $editCell = '<td ><img class="editFacility" src="images/b_edit.png"/></td>';
		  $typeLinkStr = '<a class="unclickable" href="" >'.$title.'</a>';  //**this is actually clickable, thru jquery.
		}
		if($row['myOrder'] == 0){  //0 : critical access hospital. should NOT be clickable.
//			$userFacilityRowClass = 'userFacilityRow';
			$facilityTypeIdClass = 'grayText';
			$editCell = '<td ><img class="editFacility" src="images/b_edit.png"/></td>';
			$typeLinkStr =  $title;
		}  
		$dropCell = ''; //<td ><img class="editFacility" src="images/b_drop.png"/></td>';

		$o .=  '<tr class="'.$userFacilityRowClass.'" id="'.$row['id'].'" >'.$dropCell.$editCell.'<td class="nameCell" id="'.$row['name'].'">'.$row['name'].'</td><td>'.$row['address'].'</td><td>'.$row['city'].
  		           '</td><td>'.$row['state'].'</td><td>'.$row['zip'].'</td><td class="'.$facilityTypeIdClass.'" id="'.$row['facilityTypeId'].'">'.$typeLinkStr.'</td></tr>';
		
	}
	return $o;
}

function clearFacilities($userId){
//  *  know these are normal facil, not custom.
//  1.  clear the user facilities.
//  2.  delete from surveyAnswer all answers corresponding to this userid and that are normal facilities.
//  3.  new**:  clear any related customactivities, and surveyanswers for them.

	//3:
	$affected_rows = deleteCustomActivitiesForUserInNormalFacilities($userId);
	
	$userId = cleanStrForDb($userId);
	$q = "delete from userFacility where userId = $userId";  //1
	$result = mysql_queryCustom($q);
	if($result===false)
      throwMyExc("clearFacilities: Error in delete query from userFacility, q: ".$q);
	$n = mysql_affected_rows();
	$q2 = "delete from surveyAnswer where userId = ".$userId." and isCustomFacility = 0";   //2   (those are already gone if surveyanswer table is foreignkey linked to iscustomfacil.
	$r2 = mysql_queryCustom($q2);
    if($r2===false){
	  throwMyExc("clearFacilities: r2 query error - Error in (cascade?) delete from surveyAnswer (cuz userFacility already successfully deleted all facilities for this user)");
	}
	
	
	return $n;
}

function clearCustomFacilities($userId){
//  1. get list of customfacility id.  know these are custom facil, not normal.
//  2. delete from surveyAnswer all answers corresponding to this customfacilityid and userid.
//  3.  new**:  clear any related customactivities, and surveyanswers for them.
	
	//3: 
	$affected_rows = deleteCustomActivitiesForUserInCustomFacilities($userId);
	
	$userId = cleanStrForDb($userId);
	$result = mysql_queryCustom("delete from customFacility where userid = $userId");
	if($result===false) 
	  throwMyExc("clearCustomFacilities: query  delete error");
	$n = mysql_affected_rows();
	$r2 = mysql_queryCustom("delete from surveyAnswer where userId = ".$userId." and isCustomFacility = 1");
    if($r2===false){
	  throwMyExc("clearCustomFacilities: Error in (cascade) delete from surveyAnswer (cuz userFacility successfully deleted all facilities for this user)");
	}    
	
	
	return $n;
}





