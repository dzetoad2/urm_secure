<?php
 require_once('activitiesSupplementalFunctions.php');
 require_once('DAO/activityCategoryDocDAO.php');
 require_once('activityFunctions.php');
 
 use urm\urm_secure\DAO\activityCategoryDocDAO;
 
//input: username
//output:   
function getActivityCategoriesRowsHtml($userId, $facilityId, $isCustomFacility,  $surveyCategoryId){
	/*
	 * select userFacility.id, facility.name from user
	 join userFacility on user.id = userFacility.userid
	 join facility on userFacility.facilityId = facility.id
	 where user.username = 'admin'
	 */
	
	/* Alg:
	 *   list all the activity categories inside each of the activityCategoryDoc's title group.
	 *       Each activity category has an activityCategoryDoc id, so group according to that. 
	 *   -1. get all the activitycategoryDoc ids that correspond to this surveyCategory.    
	 *   -2. Now for each of those activitycatdoc ids, append the title as a row, then append all the 
	 *      activityCategories that are grouped in it.
	 *
	 *   before:
	 */
	$acDocDAOArr = getActivityCategoryDocDistinctDAOArr($surveyCategoryId); //an array of dao objects
	$o = '';
	foreach($acDocDAOArr as $acDocDAO){
		//If there is a title, print that only. Else, print the normal activitycategory row.
		if(trim($acDocDAO->title) != ''){
		         //check if activitycategory group is complete
            $activityCategoryDocId = $acDocDAO->id;
		    if(isActivityCategoryGroupComplete($userId, $facilityId, $activityCategoryDocId, $isCustomFacility, 	0)){
    		  $rowStatus = '<img src="images/b_check.png"/>';
			}else{
			  $rowStatus = '';//blank
			}
		  $o.= '<tr class="activityCategoryDocRow clickable overGreen" id="'.$acDocDAO->id.'" ><td class="" >'.$acDocDAO->idNum.'</td><td class="bold">'.$acDocDAO->title.'</td><td>'.$rowStatus.'</td></tr>'; 
		}else{
		  $o.= getActivityCategoryGroupRows($userId, $facilityId, $isCustomFacility, $surveyCategoryId, $acDocDAO->id);
		}
	}
	return $o;
	
	
	
	 
}

function getActivityCategoryGroupRows($userId, $facilityId, $isCustomFacility, $surveyCategoryId, $activityCategoryDocId){
	/*
	 * alg: give rows for just the doc id group and of course the surveycategoryid (superfluous but good idea).    
	 */
	$surveyCategoryId = cleanStrForDb($surveyCategoryId);
	$activityCategoryDocId = cleanStrForDb($activityCategoryDocId);
	
	$result = mysql_queryCustom("  select  id, idNum, title from activityCategory where surveyCategoryId = ".$surveyCategoryId." and activityCategoryDocId = ".$activityCategoryDocId. ";");
		if($result == FALSE){
			return " ";
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
					$title = "UNK";
				if(isActivityCategoryComplete($userId,$facilityId, $row['id'], $isCustomFacility)){    //    row[id]: activitycategoryid.
				  $rowStatus = '<img src="images/b_check.png"/>';
				}else{
				  $rowStatus = '';//blank
				}
				if(defined('DEBUG')){
				  //$o .=  '<tr class="activityCategoryRow clickable" id="'.$row['id'].'"><td   >'.$row['idNum'].'</td><td class="cell1" id="'.$row['id'].'">'.$row['id'].''.'</td><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$rowStatus.'</td></tr>';
				}
				else{
				  $o .=  '<tr class="activityCategoryRow clickable overGreen" id="'.$row['id'].'"><td   >'.$row['idNum'].'</td><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$rowStatus.'</td></tr>';
						}
			}
			return $o;
	
}
/*
 * get a list of unique doc ids that appear in activityCategory for the given surveycategoryid.
 */
function getActivityCategoryDocDistinctDAOArr($surveyCategoryId){
	/*
	 *  error check to make sure the array is coming out with valid nums.  
	 *    must be like  	$o['title1'] = '1';
	 */
	$surveyCategoryId = cleanStrForDb($surveyCategoryId);
	$result = mysql_queryCustom("  select  distinct activityCategoryDoc.id, activityCategoryDoc.idNum, activityCategoryDoc.title
			      from activityCategory
			      join activityCategoryDoc 
				  on activityCategory.activityCategoryDocId = activityCategoryDoc.id
			      where surveyCategoryId = ".$surveyCategoryId.";");           
	if($result===false){
		$errorMsg='getACDocIds: query fail.';
		throwMyExc($errorMsg);
	}
	$acDocDAOArr = array();
	while($row = mysql_fetch_array($result)){
		$dao = new activityCategoryDocDAO();
		$dao->populateFromArr($row);
		$acDocDAOArr[] = $dao;
	}
	
	
	
// 	$o = array() ;
// 	$o['title1'] = '1';
// 	$o['title2'] = '2';
// 	$o['title3'] = '3';
	
	return $acDocDAOArr;
}

/*  not used.
 * 
 *  old alg:  
 *    just get all the data of activityCategories for a given surveyCategoryId.
 *    Then print out rows for all those activityCategories.
 *  
 */
//function getActivityCategoriesRowsHtml__oldVersion($userId, $facilityId, $isCustomFacility,  $surveyCategoryId){
//	/*
//	 * select userFacility.id, facility.name from user
//	join userFacility on user.id = userFacility.userid
//	join facility on userFacility.facilityId = facility.id
//	where user.username = 'admin'
//	*/
//
//	/* Alg:
//	 *   list all the activity categories inside each of the activityCategoryDoc's title group.
//	*       Each activity category has an activityCategoryDoc id, so group according to that.
//	*   -1. get all the activitycategoryDoc ids that correspond to this surveyCategory.
//	*   -2. Now for each of those activitycatdoc ids, append the title as a row, then append all the
//	*      activityCategories that are grouped in it.
//	*
//	*   before:
//	*     just get all the data of activityCategories for a given surveyCategoryId.
//	*     Then print out rows for all those activityCategories.
//	*/
//
//
//	$surveyCategoryId = cleanStrForDb($surveyCategoryId);
//	$result = mysql_queryCustom("  select  id, idNum, title from activityCategory where surveyCategoryId = ".$surveyCategoryId.";");           //check un/pw against db.
//	if($result === FALSE){
//		return " ";//throw error exception!
//	}
//	$numrows = mysql_num_rows($result);
//	if(!$numrows){
//		return " ";
//	}
//	if ($numrows == 0){
//		return " ";
//	}
//	$o = '';
//	while($row = mysql_fetch_array($result))
//	{
//		$title = $row['title'];
//		if(!$title ||  trim($title==""))
//			$title = "UNK";
//		if(isActivityCategoryComplete($userId,$facilityId, $row['id'], $isCustomFacility,0)){    // 0 : iscustomactivity.    row[id]: activitycategoryid.
//			$rowStatus = '<img src="images/b_check.png"/>';
//		}else{
//			$rowStatus = '';//blank
//		}
//		if(defined('DEBUG')){
//			$o .=  '<tr class="activityCategoryRow clickable" id="'.$row['id'].'"><td   >'.$row['idNum'].'</td><td class="cell1" id="'.$row['id'].'">'.$row['id'].''.'</td><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$rowStatus.'</td></tr>';
//		}
//		else{
//			$o .=  '<tr class="activityCategoryRow clickable" id="'.$row['id'].'"><td   >'.$row['idNum'].'</td><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$rowStatus.'</td></tr>';
//		}
//	}
//	return $o;
//
//}

//todo: get rid of $isCustomActivity in the sig.
function getCustomActivityRowsHtml($userId, $facilityId, $isCustomFacility, $isCustomActivity,    $surveyCategoryId){
	/*
	 */
	$userId = cleanStrForDb($userId);
	$surveyCategoryId = cleanStrForDb($surveyCategoryId);
	$q = "  select  id,title from customActivity where userid=".$userId." and surveyCategoryId = ".$surveyCategoryId.
		" and fid = ".$facilityId." and is_cf = ".$isCustomFacility.	
	";";
	$result = mysql_queryCustom($q);           //check un/pw against db.
	if($result == FALSE){
		return " ";
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
		$title = "UNK";
		$dropActivityImgStr = '<img class="dropActivity clickable" src="images/b_drop.png"/>';
		
		if(isActivityAnswered($userId,$facilityId,$row['id'], $isCustomFacility,1)){    //row[id] refers to the id of customActivity.
		  $rowStatus = '<img class="" src="images/b_check.png"/>';
		  // $dropActivityAnswerImgStr = '<img class="dropActivityAnswer clickable" src="images/b_drop.png"/>';
		  
		}else{
		  $rowStatus = '';//blank
		  //$dropActivityAnswerImgStr = '';
		}
		  					//if(defined('DEBUG')){
		 //$o .=  '<tr class="customActivityRow clickable" id="'.$row['id'].'""><td><img class="edit" src="images/b_edit.png"/></td><td><img class="drop" src="images/b_drop.png"/></td><td class="cell1" id="'.$row['id'].'">'.$row['id'].''.'</td><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$rowStatus .'</td></tr>';
	 
		 $o .=  '<tr class="customActivityRow   overGreen      " id="'.$row["id"].'" ><td class="" ><img class="edit clickable" src="images/b_edit.png"/></td><td>'.$dropActivityImgStr.'</td><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$rowStatus .'</td></tr>';
		 
		// <td>'.$dropActivityAnswerImgStr.'</td>   //: this was the 'drop answer' column, we disabled it feb 19(sunday).
		
	}
	return $o;
	 
}
//function deleteCustomActivity_original($userId, $customActivityId){
//	
//	
//	$customActivityId = cleanStrForDb($customActivityId);
//	$userId = cleanStrForDb($userId);
//	$r1 = mysql_queryCustom("  delete from customActivity where id = ".$customActivityId."");  
////	$r = mysql_affected_rows();
//	$r2 = mysql_queryCustom("  delete from surveyAnswer where userId = ".$userId." and isCustomActivity=1 and activityId=".$customActivityId."");
//	$o = array();
//	$o['hasError'] = true; 
//	if($r1 === TRUE && $r2 === TRUE){
//		 $o['msg'] = "User Created activity successfully deleted (including its related survey answers)";
//		 $o['hasError'] = false;
//		 return $o;
//	}else if($r1 === TRUE && $r2 === false){
//		$o['msg'] = "User Created activity successfully deleted (but failure deleting its related survey answers)";
//		return $o;
//	}else if($r1===false && $r2 === true){
//		$o['msg']= "Failed to delete user created activity but successfully deleted its related survey answers";
//		return $o;
//	}else{
//		$o['msg']= "Failure to delete user created activity, failure to delete its related survey answers";
//		return $o;
//	}
//}
function deleteCustomActivity($userId, $customActivityId){
//	die('customactivityid is : '.$customActivityId);
	
	$customActivityId = cleanStrForDb($customActivityId);
	$userId = cleanStrForDb($userId);
	$o = array();
	$o['hasError'] = true; 
	
	//1. Are there any survey answers for this customactivity? if so, stop! return an error msg! 
	$q1="  select * from surveyAnswer where userId = ".$userId." and isCustomActivity=1 and activityId=".$customActivityId;
	$r1 = mysql_queryCustom($q1);
	if($r1===false){
		$em='deletecustomactivity: r1 query fail, q1: '.$q1;
		throwMyExc($em);
	}
	$numrows = mysql_num_rows($r1);
	if($numrows>1){
		
	}
	elseif($numrows==1){
		//there exist surveyanswers! so stop, return error msg! (not an Exception)
		$o['msg'] = 'There were '. $numrows .' total survey answer(s) found for this User Created Activity.  Please delete the corresponding answer for each facility first';
		return $o;
	}
	//Ok, so numrows is 0, now go ahead and delete the customActivity.
	$q2 = "delete from customActivity where id = ".$customActivityId;
    $r2 = mysql_queryCustom($q2);  
    if($r2===false){
    	$em='deletecustomactivity: query fail $r2, q2: '.$q2;
    	throwMyExc($em);
    }
	
    $affected_rows = mysql_affected_rows();
    
	if($affected_rows == 1){
		 $o['msg'] = "User Created activity successfully deleted";
		 $o['hasError'] = false;
		 return $o;
	}else{
		$em='deletecustomactivity: affected rows not 1: affected_rows='.$affected_rows.', error!';
		throwMyExc($em);
	}
}
function deleteCustomActivityAnswer($userId, $fid, $is_cf, $customActivityId){
	// we delete just one specific answer so we need:
	/*
	 *  userid, facilityId, iscustomfacility,   iscustomactiivty=1 we know,  activityid we konw, 
	 */
	$q1 = "  delete from surveyAnswer where userId = ".$userId."
	      and  facilityId = ".$fid." 
	      and  activityId=".$customActivityId." 
	      and  isCustomFacility = ".$is_cf." 
	      and  isCustomActivity=1  ";
	
	$r1 = mysql_queryCustom($q1);
	if($r1===false){
		$em='deletecustomactivityanswer: r1 fail, q1: '.$q1;
		throwMyExc($em);
	}
	$affected_rows = mysql_affected_rows();
	if($affected_rows == 1){
		//success:  deleted exactly 1 survey answer!
		$o['msg'] = "Survey Answer (for this facility) for this User Created Activity successfully deleted";
		$o['hasError'] = false;
		 return $o;
	}elseif($affected_rows == 0){
		$o['msg'] = "No Survey Answer available to delete";
		$o['hasError'] = false;
		 return $o;
		
	}else{
		$em='deletecustomactivityanswer: affectedrows not 1 and not 0, so someother num of answers deleted? aff-rows: '.$affected_rows;
		throwMyExc($em);
	}
	
	
}

/*
 * Drops all customactivities made by this user, related to any normal faciltiies.
 * for normal facilities (userfacilities), NOT customFacilities.
 */
function deleteCustomActivitiesForUserInNormalFacilities($userId){
	$q1 = 'delete from customActivity where userId = '.$userId.' and is_cf = 0';
	$r1 = mysql_queryCustom($q1);
	if($r1===false){
		$em='q1 fail, q: '.$q1;
		throwMyExc($em);
	}
	$affected_rows = mysql_affected_rows();
	return $affected_rows;
}

/*
 * Drops all customactivities made by this user, related to any custom faciltiies.
 * for custom facilities deletion only, NOT normal facilities.
 */
function deleteCustomActivitiesForUserInCustomFacilities($userId){
	$q1 = 'delete from customActivity where userId = '.$userId.' and is_cf = 1';
	$r1 = mysql_queryCustom($q1);
	if($r1===false){
		$em='q1 fail, q: '.$q1;
		throwMyExc($em);
	}
	$affected_rows = mysql_affected_rows();
	return $affected_rows;
}




function fillOutThisSurveyCategory($userId, $facilityId,  $isCustomFacility, $surveyCategoryId ){
	 
	 
		//get all activity categories for this survey category
		$q1 = 'select id from activityCategory where surveyCategoryId = '.$surveyCategoryId;
		$r1 = mysql_query($q1);
		$activityCategoriesIds=array();
		$activityIds = array();
		while($row=mysql_fetch_array($r1)){
			//print_r($row);
			$activityCategoriesIds[] = $row['id'];
		}
		
		foreach($activityCategoriesIds as $ac_id){
			$q2 = 'select * from activity where activityCategoryId = '.$ac_id;
			$r2 = mysql_query($q2);
			if($r2===false)die('r2 false!');
			
			while($row = mysql_fetch_array($r2)){
//				echo $row['title'] .'<br/>';
				$activityIds[] = $row['id'];
			}
		}
		//activityIds are now filled out. for each one, enter survey answer of skip.
		foreach($activityIds as $aid){
			 $_fid = $facilityId;
			 $_aid = $aid;
			 $_is_cf = $isCustomFacility;
			 $_is_ca = 0;
			 $_isPerformedAdult = 'no';
			 $_isPerformedPediatric = 'no';
			 $_isPerformedNatal = 'no';
			 $_hasTimestandardAdult = 'na';
			 $_hasTimestandardPediatric = 'na';
			 $_hasTimestandardNatal = 'na';
			 $_durationAdult = -1;
			 $_durationPediatric = -1;
			 $_durationNatal = -1;
			 $_volumeAdult = -1;
			 $_volumePediatric = -1;
			 $_volumeNatal = -1;
			 $_methodologyAdult = -1;
			 $_methodologyPediatric = -1; 
			 $_methodologyNatal = -1;
			 if(!($r = submitSurveyAnswer($userId, $_fid,$_aid,$_is_cf,$_is_ca,$_isPerformedAdult,$_isPerformedPediatric,$_isPerformedNatal,
 					 $_hasTimestandardAdult,$_hasTimestandardPediatric,$_hasTimestandardNatal,$_durationAdult,$_durationPediatric,
 					 $_durationNatal,$_volumeAdult,$_volumePediatric,$_volumeNatal,$_methodologyAdult,
 					 $_methodologyPediatric,$_methodologyNatal))){
    		   $errorMsg = "Error: Submission of survey data encountered an error<br/>";
    		   //throwMyExc($errorMsg);
    		   die($errorMsg);
 			 }
		}
		
		 
	
	
	
	
}
