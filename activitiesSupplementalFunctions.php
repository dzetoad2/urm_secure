<?php
 
function isActivityAnswered($userId, $facilityId, $activityId, $isCustomFacility, $isCustomActivity){
	$result = mysql_query("  select  id from surveyAnswer where userId=".$userId." and activityId = ".$activityId." and facilityId=".$facilityId." and isCustomFacility=".$isCustomFacility.
	          " and isCustomActivity=".$isCustomActivity."   ");
	if($result===false){
		throwMyExc('isActivityAnswered(): query failed');
	}
	$numrows = mysql_num_rows($result);
	if($numrows===false){
		throwMyExc('isActivityAnswered(): numrows failed');
	}
	if($numrows > 1){
	  throwMyExc('isActivityAnswered(): numrows > 1! there cannot be more than 1 answer in the surveyanswers table for this survey question');
	}
	if ($numrows == 1){
		return true;
	}
	return false;
}
 
/*
 * Checks if THIS activitycategory is complete.
 */
function isActivityCategoryComplete($userId, $facilityId, $activityCategoryId, $isCustomFacility){
	$result = mysql_query("  select  id from activity where activityCategoryId=".$activityCategoryId);
	if($result===FALSE){
		$errorMsg='isActivityCategoryComplete(): got false as query result on activitycategory table, activitycat id is: '.$activityCategoryId;
		throwMyExc($errorMsg);
		
	}
	while($row = mysql_fetch_array($result)){
		$aid = $row['id'];
		if(!isActivityAnswered($userId, $facilityId,      $aid,      $isCustomFacility,0)){  //$isCustomActivity = 0
			return false;
		}
	}
	return true;
}

/*   This checks if the OTHER categories, in this activitycategory's same group, complete.  It does not check THIS activitycategory for completion.
 *     Group is based on activitycategorydocid.  if same docid -> same group.
 */
function isActivityCategoryGroupOtherCategoriesComplete($userId, $facilityId, $activityCategoryId, $isCustomFacility){
	/* 1. find the all activitycategories (ids) with same activitycategorydocid as this one
	 * 
	 */
	$q = "select activityCategoryDocId from activityCategory where id = ".$activityCategoryId;
	$r1 = mysql_query($q);
	if($r1===false)throwMyExc('isActivityCategoryGroupOtherCategoriesComplete: query r1 false, query str: '.$q);
	$row = mysql_fetch_assoc($r1);
	$activityCategoryDocId = $row['activityCategoryDocId'];
	if($activityCategoryDocId == '999'){
		return true;//this group is complete - for all who dont belong to subgroups.
	}
	$q2 = "  select id from activityCategory where activityCategoryDocId = ".$activityCategoryDocId;
	$r2 = mysql_query($q2);
	if($r2===false)throwMyExc('isActivityCategoryGroupOtherCategoriesComplete: query r2 false, query str: '.$q2);
	
	while($row = mysql_fetch_assoc($r2)){
		$activityCategoryId = $row['id'];
		if(!isActivityCategoryComplete($userId, $facilityId, $activityCategoryId, $isCustomFacility)){
			return false;
		}
	}
	return true; //this group is complete
}

/*
 * Check all of this group's activitycategories for completeness.
 * same docid -> same group.
 */
function isActivityCategoryGroupComplete($userId, $facilityId, $activityCategoryDocId, $isCustomFacility, 	$isCustomActivity){
	/* 1. find the all activitycategories (ids) with same activitycategorydocid as this one
	 */
	$q2 = "  select id from activityCategory where activityCategoryDocId = ".$activityCategoryDocId;
	$r2 = mysql_query($q2);
	if($r2===false)throwMyExc('isactivitycategorygroupcomplete: query r2 false, query str: '.$q2);
	
	while($row = mysql_fetch_assoc($r2)){
		$activityCategoryId = $row['id'];
		if(!isActivityCategoryComplete($userId, $facilityId, $activityCategoryId, $isCustomFacility, $isCustomActivity)){
			return false;
		}
	}
	return true; //this group is complete
}



 // $activityId - taken out of param list..
function getNextActivityId_FromOwnAC($userId, $facilityId, $activityCategoryId, $isCustomFacility,$isCustomActivity){
	//1. get list of activities in this activitycategory.
	//2. get list of activities in surveyAnswer that conform to our inputs.
	//3. make a list of the ones in 1 that are not in 2, then grab one from that.
	//3. return  one of those if there is one or more remaining, else return -1 if all activiites 
 	//in this category are already done.
    $result_activitiesNotAnswered = mysql_query("
    SELECT activity.id as activity_Id
    from
    activity
    where
    activity.activityCategoryId = ". $activityCategoryId." 
    and 
    activity.id not in (
      select activityId from surveyAnswer
      where
      userId = ".$userId." 
      and
      facilityId = ".$facilityId." 
      and
      isCustomFacility = ".$isCustomFacility." 
      and
      isCustomActivity = ".$isCustomActivity." 
    ) 
    order by idNum
    		
    ");
    
	if($result_activitiesNotAnswered===FALSE){
		$errorMsg= 'error: isactivitycategorycomplete got false as query result on activitycategory table, activitycat id is: '.$activityCategoryId;
		throwMyExc($errorMsg);
	}
	//grab the first one and return it! else throw exception!
	$row = mysql_fetch_array($result_activitiesNotAnswered);
	if($row===false){
		throwMyExc("getNextActivityId_FromOwnAC() msyql fetch array failed");
	}
	if ($row['activity_Id'] <= 0){
		throwMyExc('error: getNextActivityId_FromOwnAC failed - expected activityId > 0, got something 0 or less ');
	}
	return $row['activity_Id'];

}

/*
 * returns array - with activityCategoryId, and activityId.
 */
function getNextActivityId_WithinACGroup($userId, $facilityId, $activityCategoryId, $isCustomFacility,$isCustomActivity){
	/*  get the group of activityCategoryIds, based on the docid.  loop thru looking for category not complete yet, if found then hand 
	 *  it off to getNextActivityId_FromOwnAC.
	 * 
	 *  1. get this ac's docid.
	 *  2. use that docid to get all the ids of ac's with same docid.
	 *    for each of those ac's, check if its complete or not, if not complete, then return the getnextacivityid_fromownac answer of that specific one.
	 */
	$q = "select activityCategoryDocId from activityCategory where id = ".$activityCategoryId;
	$r1 = mysql_query($q);
	if($r1===false)throwMyExc('getNextActivityId_WithinACGroup: query r1 false, query str: '.$q);
	$row = mysql_fetch_assoc($r1);
	$activityCategoryDocId = $row['activityCategoryDocId'];
	if($activityCategoryDocId == '999'){
		$em='getNextActivityId_WithinACGroup: activitycategorydocid = 999; not possible! we only call this func if we already know the activitycategorydocId is NOT 999.';
		throwMyExc($em);
	}	
	$q2 = "  select id from activityCategory where activityCategoryDocId = ".$activityCategoryDocId;
	$r2 = mysql_query($q2);
	if($r2===false)throwMyExc('getNextActivityId_WithinACGroup: query r2 false, query str: '.$q2);
	
	while($row = mysql_fetch_assoc($r2)){
		$activityCategoryId = $row['id'];
		if(!isActivityCategoryComplete($userId, $facilityId, $activityCategoryId, $isCustomFacility, $isCustomActivity)){
			//we found one thats not complete, so we need to get the next activity within THAT one.
			//die('check1. ac_Id: '.$activityCategoryId);
			$arr = array();
			$arr['activityCategoryId'] = $activityCategoryId; 
			$arr['activityId'] = getNextActivityId_FromOwnAC($userId, $facilityId, $activityCategoryId, $isCustomFacility,$isCustomActivity);
			return $arr;
		}else{
			//do nothing, skip to next row
		}
	}
	//return true; //this group is complete
	$em='getNextActivityId_WithinACGroup:   all the ACs are complete?? impossible, we already know at least one must be incomplete.';
	throwMyExc($em);
	
}


function isSurveyCategoryComplete($userId, $facilityId,$isCustomFacility,   $surveyCategoryId   ){
	//for this var user, this var facility, this var isCustomFacility, and this var  surveycategoryid, 
	   // we must check all the activityCategories *and* the customactivities within.
	//1. check activitycategories for normal activities.
	//2. check customActivities for this surveyCategory.
	//1.
	$result = mysql_query("  select  id from activityCategory where surveyCategoryId=".$surveyCategoryId);
	if($result===FALSE){
		$errorMsg='error: issurveycategorycomplete got false as query result on activitycategory table, surveycat id is: '.$surveyCategoryId;
		$_SESSION['errorMsg'] = $errorMsg;
		header('location: errorPage.php');
		exit();
	}
	while($row = mysql_fetch_array($result)){  //loop thru the activity category ids.
		if(!isActivityCategoryComplete($userId, $facilityId, $row['id'], $isCustomFacility, 0)){ //0: normal activities, not custom.
			return false;
		}
	}
	if(!isCustomActivitiesComplete($userId,$facilityId, $isCustomFacility,$surveyCategoryId)){
		//return false;
		
	
	
	}
	return true;
		
}













function hasCustomActivities($userId,  $surveyCategoryId  ){
    //check to see if this user has any custom activities defined for this survey category  (customacdtivty has only surveycategoryid and userid.  so thats it)
  	$r = mysql_query("  select  id from customActivity where userid=".$userId." and surveyCategoryId=".$surveyCategoryId);
  	if($r===false){
  		$em='hascustomactivities: query fail';
  		throwMyExc($em);
  	}
  	$n = mysql_num_rows($r);
  	if($n==0){
  		return false;
  	}else{
  		return true;
  	}
}














//function getCustomActivityRowsHtml uses :  ->    $userId, $facilityId, $isCustomFacility, $isCustomActivity,    $surveyCategoryId
function isCustomActivitiesComplete($userId, $facilityId, $isCustomFacility, $surveyCategoryId){
	//1. loop thru the defined custom activities for this user.  each one has to be 'isActivityAnswered() == true' to return true, else we return false.
	//===============================================    
    $result = mysql_query("  select  id from customActivity where userid=".$userId." and surveyCategoryId = ".$surveyCategoryId.";");
	if($result === FALSE){
		$errorMsg ='iscustomactivitiescomplete(): select id from customactivity: query failed';
		throwMyExc($errorMsg);
	}
	$numrows = mysql_num_rows($result);
	if($numrows === false){
		$errorMsg='error: iscustomactivitiescomplete:  mysqlnum rows was false';
		throwMyExc($errorMsg);
	}
	if ($numrows == 0){
		return true;
	}
	while($row = mysql_fetch_array($result))
	{
		$id = $row['id'];
		if(!isActivityAnswered($userId,$facilityId,$row['id'], $isCustomFacility,1)){  //row[id] refers to the id of customActivity.
			return false;
		}  
	}

	return true;

}

function isFacilityComplete($userId, $fid){
 //loop through survey categories, getting their ids. then, using the userid and fid, and customFac is 0,
 //get whether...
    $result = mysql_query("select id from surveyCategory");
    if($result === FALSE){
		$errorMsg='error: isfacilitycomplete: select id from customactivity: query result was false';
		throwMyExc($errorMsg);
		
    }
	$numrows = mysql_num_rows($result);
	if($numrows === false){
		$errorMsg='error: isfacilitycomplete:  mysqlnum rows was false: -> error.';
		throwMyExc($errorMsg);
		
	}
	while($row = mysql_fetch_array($result)){  //loop thru the activity category ids.
		$surveyCategoryId = $row['id'];
		if(!isSurveyCategoryComplete($userId, $fid, 0,   $surveyCategoryId)){
			return false;
		}
	}
	return true;
}
function isCustomFacilityComplete($userId, $fid){
 //loop through survey categories, getting their ids. then, using the userid and fid, and customFac is 1,
 //get whether...
    $result = mysql_query("select id from surveyCategory");
    if($result === FALSE){
		$errorMsg='error: isfacilitycomplete: select id from customactivity: query result was false';
		throwMyExc($errorMsg);
    }
	$numrows = mysql_num_rows($result);
	if($numrows === false){
		$errorMsg='error: isfacilitycomplete:  mysqlnum rows was false: -> error.';
		throwMyExc($errorMsg);
		
	}
	while($row = mysql_fetch_array($result)){  //loop thru the activity category ids.
		$surveyCategoryId = $row['id'];
		if(!isSurveyCategoryComplete($userId, $fid, 1,   $surveyCategoryId)){
			return false;
		}
	}
	return true;
}

