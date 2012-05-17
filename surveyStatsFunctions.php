 <?php

require_once('activitiesSupplementalFunctions.php');
 
require_once('DAO/surveyCategoryStatsRowListDAO.php'); 
use urm\urm_secure\DAO\surveyCategoryStatsRowListDAO;
require_once('DAO/userStatsRowListDAO.php');
use urm\urm_secure\DAO\userStatsRowListDAO;
require_once('DAO/userStatsStartedButIncompleteDAO.php');
use urm\urm_secure\DAO\userStatsStartedButIncompleteDAO;

function getTotalNumAnsweredActivities($userId){

		//table is  userid, username, totalnumactivitiesanswered.
		$r = mysql_queryCustom("select count(*) as c from surveyAnswer where userId=".$userId);
    	$row = mysql_fetch_array($r);
		$c = $row['c'];
		return $c;
    	
}

/* table stuf:  surveyAnswer has activityId,    activity  has activityCategoryId,   activityCategory  has surveyCategoryId.
 * 
 */
function getTotalNumAnsweredActivitiesInSurveyCategoryForFacility($userId, $surveyCategoryId, $fid, $isCustomFacility){
		$q =  
		 "select count(*) as c 
		  from surveyAnswer join activity 
		  on surveyAnswer.activityId = activity.id
		  join activityCategory
		  on activity.activityCategoryId = activityCategory.id
		  join surveyCategory
		  on activityCategory.surveyCategoryId = surveyCategory.id
		     where surveyAnswer.userId = ".$userId." and surveyCategory.id = ".$surveyCategoryId.
		    " and surveyAnswer.facilityId = ".$fid." and surveyAnswer.isCustomFacility = ".$isCustomFacility.
		    " and isCustomActivity = 0 "		
		;
		$r = mysql_queryCustom($q);
	    
		if($r=== false){
			die('error: gettotalnumansweredactivitiesinsurveycategoryforfacility: query fail, q: '.$q);
		}
    	$row = mysql_fetch_array($r);
		$c = $row['c'];
		return $c;
}


//output: an integer string.
function getTotalFacilitiesRegistered($userId){
		$userId = cleanStrForDb($userId);
		$q = "select count(*) as c from userFacility where userid=".$userId;
		$r = mysql_queryCustom($q);
    	$row = mysql_fetch_array($r);
		$count = $row['c'];
		return $count;
}
function getTotalCustomFacilitiesRegistered($userId){
		$userId = cleanStrForDb($userId);
		$q = "select count(*) as c from customFacility where userid = ".$userId;
		$r = mysql_queryCustom($q);
		$row = mysql_fetch_array($r);
		$count = $row['c'];
		return $count;
}


function getStats1RowsHtml(){
	//loop through all users in user table.
	$o='';
	$r = mysql_queryCustom("select id,username from user");
	while($row = mysql_fetch_array($r)){
		$userId = $row['id'];
		$username = $row['username'];
		$o.='<tr><td>' . $userId.'</td><td>' .$username.'</td><td>' .getTotalFacilitiesRegistered($userId). '</td><td>'. getTotalCustomFacilitiesRegistered($userId). '</td><td>'.getTotalNumAnsweredActivities($userId). '</tr>';
	}
	return $o;

}

function getStats2RowsHtml(){
	/*
	 * col:  surv type (name),     Total # of this *complete* by any user.
	 * alg:
	 *  1. get cols from surveyCategory and user.   has int and description.  we'l output the title
	 *  2. for each col id,  get the total # of complete surveys among all users. 
	 *      (in surveyCategories functions). 
	 *      Breakdown:
	 *		 - get list of user ids.
	 *       - get total # of answers of each surveyCategory.
	 *       - in surveyanswer,  check # of answers with a given surveyCategory id for each user.
	 */
	//$q0 = "select id from user where 1";
	//$r0 = mysql_queryCustom($q0);
	$sd = new surveyCategoryStatsRowListDAO(); //autopopulates array        get the survey category info.  id and title
	$ud = new userStatsRowListDAO();  //autopopulates array                 get the user info.  id and username
	
	$o = '';
	//return $sd->toRowsHtml(); //works fine
	//get count of surveyanswers for the given userid and surveycategory.
	foreach($sd->list as $sdrow){
		$surveyCategoryTitle = $sdrow->title;
		$surveyCategoryId = $sdrow->id;
		$count = 0;
		foreach($ud->list as $udrow){
			$userId = $udrow->id;
			if( isset($udrow->facilityIdArr)  &&  count($udrow->facilityIdArr) > 0  )  {  
				foreach($udrow->facilityIdArr as $facilityId){
				  if(true ===  isSurveyCategoryComplete($userId, $facilityId, 0,   $surveyCategoryId   )){
					//echo 'complete: userid '.$userId.', fid: '.$facilityId.', normal facil, sur category: '.$surveyCategoryTitle.'<br/>';
				  	$count++;
				  }
				}
			}  // if facil arr is valid
			if( isset($udrow->customFacilityIdArr)  &&  count($udrow->customFacilityIdArr) > 0  )  {  
				foreach($udrow->customFacilityIdArr as $customFacilityId){
				  if(true ===  isSurveyCategoryComplete($userId, $customFacilityId, 1,   $surveyCategoryId   )){
					//echo 'complete: userid '.$userId.', fid: '.$facilityId.', custom facil, sur category: '.$surveyCategoryTitle.'<br/>';
				  	$count++;
				  }
				}
			}
			// surveyanswer fields:    userId  facilityId  isCustomFacility  isCustomActivity
			/*
			if(true ===  isSurveyCategoryComplete($userId, $facilityId,$isCustomFacility,   $surveyCategoryId   )){
				$count++;
			}
			*/
			
			
		
		}
		//echo 'surv category id: '.$surveyCategoryId. ', finished_count: '.$count.'<br/>';
		$o .= '<tr><td>'.$surveyCategoryTitle.'</td><td>'.$count.'</td></tr>';
	}
	return $o;
	
}

function getStats3RowsHtml(){
/* alg:  for each surv cat, loop through all users to find who have started but not completed that cat.  
 * 
 */
	$sd = new surveyCategoryStatsRowListDAO(); //autopopulates array        get the survey category info.  id and title
	$ud = new userStatsRowListDAO();  //autopopulates array                 get the user info.  id and username
	
	$o = '';
	//return $sd->toRowsHtml(); //works fine
	//get count of surveyanswers for the given userid and surveycategory.
	foreach($sd->list as $sdrow){
		$surveyCategoryTitle = $sdrow->title;
		$surveyCategoryId = $sdrow->id;
		$sdActivityTotalCount =  $sdrow->activityTotalCount;
		$count = 0;
		foreach($ud->list as $udrow){
			$userId = $udrow->id;
			
			if( isset($udrow->facilityIdArr)  &&  count($udrow->facilityIdArr) > 0  )  {  
				foreach($udrow->facilityIdArr as $facilityId){
				  $fInfoDao = $udrow::getFacilityInfo($facilityId, 0);
				  $facilityName = $fInfoDao->getName();
				  $facilityState = $fInfoDao->getState();
				  if(true ===  isSurveyCategoryStarted($userId, $facilityId, 0, $surveyCategoryId)  &&    false ===  isSurveyCategoryComplete($userId, $facilityId, 0, $surveyCategoryId   )){
					//echo 'complete: userid '.$userId.', fid: '.$facilityId.', normal facil, sur category: '.$surveyCategoryTitle.'<br/>';
				  	 $o .= '<tr><td>'.$surveyCategoryTitle.'</td><td>'.$udrow->username.'</td><td>'. $facilityName.'</td><td>'.$facilityState.'</td><td>Not Custom</td><td>'.getTotalNumAnsweredActivitiesInSurveyCategoryForFacility($userId, $surveyCategoryId, $facilityId, 0).'</td><td>'.$sdActivityTotalCount.'</td></tr>';
				  }
				}
			}  // if facil arr is valid
			if( isset($udrow->customFacilityIdArr)  &&  count($udrow->customFacilityIdArr) > 0  )  {  
				foreach($udrow->customFacilityIdArr as $customFacilityId){
				  $cfInfoDao = $udrow::getFacilityInfo($customFacilityId, 1);
				  $customFacilityName =  $cfInfoDao->getName();
				  $customFacilityState = $cfInfoDao->getState();
				  if(true ===  isSurveyCategoryStarted($userId, $customFacilityId, 1, $surveyCategoryId)  && false ===  isSurveyCategoryComplete($userId, $customFacilityId, 1, $surveyCategoryId   )){
					//echo 'complete: userid '.$userId.', fid: '.$facilityId.', custom facil, sur category: '.$surveyCategoryTitle.'<br/>';
				  	 $o .= '<tr><td>'.$surveyCategoryTitle.'</td><td>'. $udrow->username.'</td><td>'.$customFacilityName.'</td><td>'.$customFacilityState.'</td><td>Yes (is Custom)</td><td>'.getTotalNumAnsweredActivitiesInSurveyCategoryForFacility($userId, $surveyCategoryId, $customFacilityId, 1).'</td><td>'.$sdActivityTotalCount.'</td></tr>';
				  }
				}
			}
		}
		
	}//outside foreach
	return $o;	
	
}

function getStats4RowsHtml(){
/* alg:  for each surv cat, loop through all users to find who have started but not completed that cat.  
 * 
 */
	$sd = new surveyCategoryStatsRowListDAO(); //autopopulates array        get the survey category info.  id and title
	$ud = new userStatsRowListDAO();  //autopopulates array                 get the user info.  id and username
	
	$o = '';
	//return $sd->toRowsHtml(); //works fine
	//get count of surveyanswers for the given userid and surveycategory.
	foreach($sd->list as $sdrow){
		$surveyCategoryTitle = $sdrow->title;
		$surveyCategoryId = $sdrow->id;
		$sdActivityTotalCount =  $sdrow->activityTotalCount;
		$count = 0;
		foreach($ud->list as $udrow){
			$userId = $udrow->id;
			
			if( isset($udrow->facilityIdArr)  &&  count($udrow->facilityIdArr) > 0  )  {  
				foreach($udrow->facilityIdArr as $facilityId){
				  $facilityName = $udrow::getFacilityInfo($facilityId, 0);
				  if(true ===  isSurveyCategoryStarted($userId, $facilityId, 0, $surveyCategoryId)    ){
				  	 if(false ===  isSurveyCategoryComplete($userId, $facilityId, 0, $surveyCategoryId   )){
				  	 	$isComplete = "Incomplete";
				  	 	$isCompleteHighlight = "highlight";
				  	 }else{
				  	 	$isComplete = "Complete";
				  	 	$isCompleteHighlight = "";
				  	 }
					//echo 'complete: userid '.$userId.', fid: '.$facilityId.', normal facil, sur category: '.$surveyCategoryTitle.'<br/>';
				  	 $o .= '<tr><td>'.$surveyCategoryTitle.'</td><td>'.$udrow->username.'</td><td>'. $facilityName.'</td><td>Not Custom</td><td class="'.$isCompleteHighlight.'">'.getTotalNumAnsweredActivitiesInSurveyCategoryForFacility($userId, $surveyCategoryId, $facilityId, 0).'</td><td >'.$sdActivityTotalCount.'</td></tr>';
				  }
				}
			}  // if facil arr is valid
			if( isset($udrow->customFacilityIdArr)  &&  count($udrow->customFacilityIdArr) > 0  )  {  
				foreach($udrow->customFacilityIdArr as $customFacilityId){
				  $customFacilityName = $udrow::getFacilityInfo($customFacilityId, 1);
				  if(true ===  isSurveyCategoryStarted($userId, $customFacilityId, 1, $surveyCategoryId)    ){
				  	if(false ===  isSurveyCategoryComplete($userId, $customFacilityId, 1, $surveyCategoryId   )){
				  		$isComplete = "Incomplete";
				  		$isCompleteHighlight = "highlight";
				  	}else{
				  		$isComplete = "Complete";
				  		$isCompleteHighlight = "";
				  	}
					//echo 'complete: userid '.$userId.', fid: '.$facilityId.', custom facil, sur category: '.$surveyCategoryTitle.'<br/>';
				  	 $o .= '<tr><td>'.$surveyCategoryTitle.'</td><td>'. $udrow->username.'</td><td>'.$customFacilityName.'</td><td>Yes (is Custom)</td><td class="'.$isCompleteHighlight.'">'.getTotalNumAnsweredActivitiesInSurveyCategoryForFacility($userId, $surveyCategoryId, $customFacilityId, 1).'</td><td >'.$sdActivityTotalCount.'</td></tr>';
				  }
				}
			}
		}
		
	}//outside foreach
	return $o;	
	
}
 
function getStats5RowsHtml(){
/* alg:  for each surv cat, loop through all users to find who have started but not completed that cat.  
 * 
 */
	$sd = new surveyCategoryStatsRowListDAO(); //autopopulates array        get the survey category info.  id and title
	$ud = new userStatsRowListDAO();  //autopopulates array                 get the user info.  id and username
	
	$o = '';
	//return $sd->toRowsHtml(); //works fine
	//get count of surveyanswers for the given userid and surveycategory.
	foreach($sd->list as $sdrow){
		$surveyCategoryTitle = $sdrow->title;
		$surveyCategoryId = $sdrow->id;
		$sdActivityTotalCount =  $sdrow->activityTotalCount;
		$count = 0;
		foreach($ud->list as $udrow){
			$userId = $udrow->id;
			
			if( isset($udrow->facilityIdArr)  &&  count($udrow->facilityIdArr) > 0  )  {  
				foreach($udrow->facilityIdArr as $facilityId){
				  $fInfoDao = $udrow::getFacilityInfo($facilityId, 0);
				  $facilityName = $fInfoDao->getName();
				  $facilityState = $fInfoDao->getState();
				  if(true ===  isSurveyCategoryStarted($userId, $facilityId, 0, $surveyCategoryId)  &&    true ===  isSurveyCategoryComplete($userId, $facilityId, 0, $surveyCategoryId   )){
					//echo 'complete: userid '.$userId.', fid: '.$facilityId.', normal facil, sur category: '.$surveyCategoryTitle.'<br/>';
				  	 $o .= '<tr><td>'.$surveyCategoryTitle.'</td><td>'.$udrow->username.'</td><td>'. $facilityName.'</td><td>'.$facilityState.'</td><td>Not Custom</td><td>'.getTotalNumAnsweredActivitiesInSurveyCategoryForFacility($userId, $surveyCategoryId, $facilityId, 0).'</td><td>'.$sdActivityTotalCount.'</td></tr>';
				  }
				}
			}  // if facil arr is valid
			if( isset($udrow->customFacilityIdArr)  &&  count($udrow->customFacilityIdArr) > 0  )  {  
				foreach($udrow->customFacilityIdArr as $customFacilityId){
				  $cfInfoDao = $udrow::getFacilityInfo($customFacilityId, 1);
				  $customFacilityName = $cfInfoDao->getName();
				  $customFacilityState = $cfInfoDao->getState();
				  if(true ===  isSurveyCategoryStarted($userId, $customFacilityId, 1, $surveyCategoryId)  && true ===  isSurveyCategoryComplete($userId, $customFacilityId, 1, $surveyCategoryId   )){
					//echo 'complete: userid '.$userId.', fid: '.$facilityId.', custom facil, sur category: '.$surveyCategoryTitle.'<br/>';
				  	 $o .= '<tr><td>'.$surveyCategoryTitle.'</td><td>'. $udrow->username.'</td><td>'.$customFacilityName.'</td><td>'.$customFacilityState.'</td><td>Yes (is Custom)</td><td>'.getTotalNumAnsweredActivitiesInSurveyCategoryForFacility($userId, $surveyCategoryId, $customFacilityId, 1).'</td><td>'.$sdActivityTotalCount.'</td></tr>';
				  }
				}
			}
		}
		
	}//outside foreach
	return $o;	
	
}



 																	//$row = mysql_fetch_assoc($r);  //what does this do? was in sessionstatefunctions hmm.




/*
 * we want the total count of surveys completed, for each type of the 7 surveys (surveycategories).
 * 
 * get info for normal facilities and custom facilities,  which is the userFacility and the customFacility tables' ids , as those are in the surveyAnswer table.
 * each user has arrays of their normalfacil and customfacil.  check*.
 * 
 * 
 * 
 * 
 * 
 * 
 */