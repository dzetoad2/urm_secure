<?php
 require_once('activitiesSupplementalFunctions.php');


/*
 * Goal is to get groups of activities(rows, formatted), with a unclickable bold header for each of them.
 */
function getActivityGroupRowsHtml($userId, $facilityId, $isCustomFacility, $isCustomActivity, $activityCategoryDocId){
	/* alg: we need the header lines - one per activityCategory. so get the activityCategoryIds for this one activityCategoryDocId. 
	 * 1. get activitycategoryids (array) for this one activitycategorydocid.
	 */
	$o = '';
	$acRows =   getActivityCategoryRowsPerActivityCategoryDocId($activityCategoryDocId);
	 
	foreach($acRows as $acRow){
		$o .=  '<tr><td class="bold">'.$acRow['idNum'].'</td><td class="bold">'.$acRow['title'].'</td><td></td></tr>';
		
		$o .= getActivityRowsHtml($userId, $facilityId, $isCustomFacility, $isCustomActivity, $acRow['id']);
	}
	
	//for each activityCategoryId, do the getActivityRowsHtml below.
	
	return $o;
}

function getActivityCategoryRowsPerActivityCategoryDocId($activityCategoryDocId){
	$activityCategoryDocId = cleanStrForDb($activityCategoryDocId);
	$q = 'select * from activityCategory where activityCategoryDocId = '.$activityCategoryDocId;
	$r = mysql_queryCustom($q);
	if($r===false) {
		$em = 'getactivitycategoryidsperactivitycategorydocid: query fail, q: '.$q;
	}
	$acRows = array();
	while($row = mysql_fetch_assoc($r)){
		$acRows[] = $row;
	}
	return $acRows;
	
}
function getActivityRowsHtml($userId, $facilityId, $isCustomFacility, $isCustomActivity, $activityCategoryId){
	/*
	 * select userFacility.id, facility.name from user
	 join userFacility on user.id = userFacility.userid
	 join facility on userFacility.facilityId = facility.id
	 where user.username = 'admin'
	 */
	$activityCategoryId = cleanStrForDb($activityCategoryId);
	$q = "  select  id, idNum, title from activity where activityCategoryId = ".$activityCategoryId."   order by idNum ";
	$result = mysql_queryCustom($q);           //check un/pw against db.
	if($result === FALSE){
		throwMyExc('getActivityRowsHtml: query failed , query was: '.$q);
	}
	$numrows = mysql_num_rows($result);
	if($numrows===false){
		throwMyExc('getActivityRowsHtml:   numrows was false');
	}
	if ($numrows == 0){
		return " ";
	}
	$o = '';
	while($row = mysql_fetch_array($result))
	{
		$title = $row['title'];
		if(!$title ||  trim($title==""))
		$title = "UNK";
		if(isActivityAnswered($userId,$facilityId,$row['id'], $isCustomFacility,$isCustomActivity)){
		  $rowStatus = '<img src="images/b_check.png"/>';
		}else{
		  $rowStatus = '';//blank
		}
		if(defined('DEBUG')){
		 $o .=  '<tr class="activityRow" id="'.$row['id'].'"> <td class="cell1" id="'.$row['id'].'">'.$row['id'].''.'</td><td >'.$row['idNum'].''.'</td><td class="nameCell clickable" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$rowStatus.'</td></tr>';
		}else{
		 $o .=  '<tr class="activityRow" id="'.$row['id'].'"><td >'.$row['idNum'].''.'</td><td class="nameCell clickable" id="'.$row['title'].'"><a class="unclickable" href="" >'.$row['title'].'</a></td><td>'.$rowStatus.'</td></tr>';
		}
	}
	return $o;
}

//only admin can delete activities.
// in case admin makes survey answers, those also get deleted. they are not useful in any case.
function deleteActivity($activityId){
	$activityId = cleanStrForDb($activityId);
	$res1 = mysql_queryCustom("  delete from activity where id = ".$activityId."");  
	$r = mysql_affected_rows();
	$res2 = mysql_queryCustom("  delete from surveyAnswer where userId = 1 and isCustomActivity=0 and activityId=".$activityId."");  
	if($res1 === TRUE && $res2 === TRUE){
		return "Success deleting activity and its related survey answers.";
	}else if ($res1 === TRUE && $res2 === FALSE){
		return "Success deleting activity, but error deleting its related survey answers";
	}else if ($rest1 === FALSE && $res2 === TRUE){
		return "Error deleting activity but success deleting its survey answers";
	}else{
		return "Error both deleting activity and its related survey answers.";
	}
}



//input: username
//output:  all the
//function getActivityRowsAdminHtml($userId, $facilityId, $isCustomFacility, $isCustomActivity, $activityCategoryId){
//	/*
//	 * select userFacility.id, facility.name from user
//	 join userFacility on user.id = userFacility.userid
//	 join facility on userFacility.facilityId = facility.id
//	 where user.username = 'admin'
//	 */
//	$result = mysql_queryCustom("  select  activity.id as id, activity.idNum as idNum, activity.title as title  from activity  where activityCategoryId = ".$activityCategoryId.";");           //check un/pw against db.
//	if($result === FALSE){
//		throwMyExc('getActivityRowsAdminHtml(): query failed');
//	}
//	$numrows = mysql_num_rows($result);
//	if($numrows===false){
//		throwMyExc('getActivityRowsAdminHtml(): numrows failed');
//	}
//	if ($numrows == 0){
//		return " ";
//	}
//	$o = '';
//	while($row = mysql_fetch_array($result))
//	{
//		$title = $row['title'];
//		if(!$title ||  trim($title==""))
//		$title = "UNK";
//		
//		if(isActivityAnswered($userId,$facilityId,$row['id'], $isCustomFacility,$isCustomActivity)){
//		  $rowStatus = '<img src="images/b_check.png"/>';
//		}else{
//		  $rowStatus = '';//blank
//		}
//		
//		if(defined('DEBUG')){
//		 $o .=  '<tr class="activityRow clickable" id="'.$row['id'].'"><td><img class="edit" src="images/b_edit.png"/></td><td><img class="drop" src="images/b_drop.png"/></td><td class="cell1" id="'.$row['id'].'">'.$row['id'].''.'</td><td >'.$row['idNum'].''.'</td><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$rowStatus.'</td></tr>';
//		}else{
//		 $o .=  '<tr class="activityRow clickable" id="'.$row['id'].'"><td><img class="edit" src="images/b_edit.png"/></td><td><img class="drop" src="images/b_drop.png"/></td><td >'.$row['idNum'].''.'</td><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$rowStatus.'</td></tr>';
//		}
//	}
//	return $o;
//}
