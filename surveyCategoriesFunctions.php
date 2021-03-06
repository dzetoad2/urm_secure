<?php

require_once('activitiesSupplementalFunctions.php');

require_once('DAO/surveyCategoryRowsDAO.php');
use urm\urm_secure\DAO\surveyCategoryRowsDAO;



//input: username
//output:  all the survey categories.
function getSurveyCategoriesRowsHtml($userId, $facilityId, $isCustomFacility){
	/*
	 * select userFacility.id, facility.name from user
	 join userFacility on user.id = userFacility.userid
	 join facility on userFacility.facilityId = facility.id
	 where user.username = 'admin'
	 
	 If it's of type freestanding PR - then we should only return that single result.
	 */
	$userId = cleanStrForDb($userId);
	$facilityId = cleanStrForDb($facilityId);
	$isCustomFacility = cleanStrForDb($isCustomFacility);
	
	
	$facilityTypeTitleStr = getFacilityTypeStrFromFacilityEntry($userId, $facilityId, $isCustomFacility);
	
	$conditionStr='';
	
	//if(true === strpos($facilityTypeTitleStr, 'Pulmonary' )){
	//	$conditionStr = 'WHERE  id = 6';
		//die('condition NOT met.  fttstr: '.$facilityTypeTitleStr);
	//}
	
	
	//CLEAN - OK.
	$result = mysql_queryCustom("  select  id, title from surveyCategory ".$conditionStr);           //check un/pw against db.
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
	$atLeastOneSurveyCategoryIsComplete=false;
	while($row = mysql_fetch_array($result))
	{
		$title = $row['title'];
		if(!$title ||  trim($title==""))
		  $title = "UNK";
		if(isSurveyCategoryComplete($userId,$facilityId, $isCustomFacility, $row['id'])){    // iscustomactivity (1 and 0 both will be checked.    row[id]: surveycategoryid.  
		  $rowStatus = '<img src="images/b_check.png"/>';
		  $atLeastOneSurveyCategoryIsComplete=true;
		}else{
 		  $rowStatus = '';//blank
		}
		//========DEFAULT UNLESS WE NEED OTHERWISE=====
		$isAllowed=true;
		$classRow = 'surveyCategoryRow';
		
		
		$titleLinkStr = '<a class="unclickable '. $classRow.'" href="" >'.$row['title'].'</a>';
		
		
		
		$dropCellStr = '<td class="drop clickable"><img src="images/b_drop.png" /></td>'   ;
		
		$surveyCategoryOwner='';
		$surveyCategoryOwnerCellStr = ''; //<td>iscustomfacility var: '.$isCustomFacility.'</td>';
		
		if($isCustomFacility==0){
		  $surveyCategoryOwner = getSurveyCategoryOwner($facilityId,$isCustomFacility,$row['id']); //row-id is survey cat id.  owner is the username.
		  $surveyCategoryOwnerId = getUserId($surveyCategoryOwner);
  		  $surveyCategoryOwnerCellStr = '<td>'.$surveyCategoryOwner.'</td>';
  		  if($surveyCategoryOwner==''){
  		  		$dropCellStr = '<td></td>';
  		  }
		  if($userId != $surveyCategoryOwnerId   && $surveyCategoryOwnerId != '' && !defined('debugSurveyCategories')){ //if this is not owned by 'me'...
		   //deny access.
		    $isAllowed = false;
		    $classRow = 'grayText';
		    
		    
		    
		    
		    $titleLinkStr =  $row['title'];
		    
		    
		    
		    
		    $dropCellStr = '<td></td>';
		  }
		  
		}elseif($isCustomFacility==1){
			
			if(false == isSurveyCategoryStarted($userId,$facilityId,$isCustomFacility,$row['id']) ){  //if the surv cat is not started yet...
  				$dropCellStr = '<td></td>';  //then its impossible to delete answers, so blank this out.
  				$surveyCategoryOwnerCellStr = '';
  			}else{
				if($rowStatus==''){
  					$rowStatus = 'started';
				}
  			}
			
  			
		}else{
			$em='iscustomfacility is not 1 and not 0, error!';
			throwMyExc($em);
		}

		
		 $o .=  '<tr class="'.$classRow.'" id="'.$row['id'].'">'.$dropCellStr.'<td class="nameCell" id="'.$row['title'].'">'.$titleLinkStr.'</td><td>'.$rowStatus.'</td>'.$surveyCategoryOwnerCellStr .'</tr>';
		 
	}
	
	$scrDao = new surveyCategoryRowsDAO();
	$scrDao->o = $o;
	$scrDao->atLeastOneSurveyCategoryIsComplete = $atLeastOneSurveyCategoryIsComplete;
	
	return $scrDao;
	
	
	
}

/* Drops all answers for this user in this survey category.
 *     surveyanswer table requires:    userid  fid  is_cf.   
 *        Do lookup to find which activityIds go with the surveyCategoryId here. (do this first)
 *   return: count of records successfully deleted, or else exception.
 *   - delete answers for those.
 *   - then delete customactivities for this user and this surveycategoryid and this facility (fid, is_cf).  There is a preexisting routine to do that - call it.
 */
function dropSurveyAnswers($userId,$fid,$is_cf,$surveyCategoryId){
   //get list of activityIds which are under the surveyCategoryId specified.
   $userId = cleanStrForDb($userId);
   $fid = cleanStrForDb($fid);
   $is_cf = cleanStrForDb($is_cf);
   $surveyCategoryId = cleanStrForDb($surveyCategoryId);
   
   if($surveyCategoryId==1){
	   if($is_cf==0){
	   	 $tableName = 'userFacility';
	   }elseif($is_cf==1){
	   	 $tableName = 'customFacility';
	   }else{
	   	 $em= 'dropsurveyanswers:  is_cf neither 0 nor 1, impossible';
	   	 throwMyExc($em);
	   }
	   $q0 = 'update '.$tableName.' set completionStatus = "0000000" where userId = '.$userId.' and id = '.$fid.'; ';
	   $r0 = mysql_queryCustom($q0);
	   if($r0===false){
	   	 $em='dropsurveyanswers: q0 fail, q0: '.$q0;
	   	 throwMyExc($em);
	   }
	   $n = mysql_affected_rows();
	   if($n!=1  && $n!=0){
	   	$em='dropsurveyanswers: mysql affected rows for the completionstatus update is not 1! invalid. n: '.$n.', q0: '.$q0;
	   	throwMyExc($em);
	   }
   }else{
   	// not in acute surv cat (cat id#1) so don't do anything.
   }
   
   $q1 = "  
      select  activity.id as aid    
      from activity 
      inner join (
        activityCategory inner join surveyCategory
        on activityCategory.surveyCategoryId = surveyCategory.id
      )
      on activity.activityCategoryId = activityCategory.id
      where activityCategory.surveyCategoryId = ". $surveyCategoryId."
        ";
   
   
   $res1 = mysql_queryCustom($q1);
   if($res1 === FALSE){
     throwMyExc("query failed: first query for dropsurveyanswers , q1: ".$q1);
   }
   $count = 0;
    while($row = mysql_fetch_array($res1)){  //delete all the surveyanswers for normal activities i asnwered for this surveyCategory.
      $aid = $row['aid'];
      $qtext = "
        delete from surveyAnswer
        where userId = ".$userId.
       " and facilityId = ".$fid.
       " and isCustomFacility = ".$is_cf.
       " and isCustomActivity = 0 ". 
       " and activityId = ".$aid ;
      $res2 = mysql_queryCustom($qtext);
      if($res2===FALSE)
        throwMyExc("dropsurveyanswers:  query failed: 2nd query, count so far is: ".$count.", querytext: ".$qtext.", mysqlerror: ".mysql_error());
      $n = mysql_affected_rows();
      if($n===1)
        $count++;
      else if($n===0){
       //dont add to count
      }else{
        throwMyExc("Error: mysql affected rows was neither 0 nor 1, n is: ".$n.";  ");
      }
    }
    //now drop the customactivities i did for this surveycategory.
    //get list of customActivities for this user.
    
    $q4 = 'select id from customActivity where userId = '.$userId.' and surveyCategoryId = '.$surveyCategoryId.' and fid = '.$fid.' and is_cf = '.$is_cf.' ; ';
    $r4 = mysql_queryCustom($q4);
    if($r4===false){
		$em='dropsurveyanswers: q4 query failed, q4: '.$q4;
    	throwMyExc($em);
    }
    //loop thru these ids, and try a delete from surveyanswer for each.
    while($row= mysql_fetch_assoc($r4)){
    	$aid = $row['id'];
    	$q5 = "
        delete from surveyAnswer
        where userId = ".$userId.
        " and facilityId = ".$fid.
        " and isCustomFacility = ".$is_cf.
        " and isCustomActivity = 1 ". 
        " and activityId = ".$aid ;
        $r5 = mysql_queryCustom($q5);
    	if($r5===false){
    		$em='dropsurveyanswers: r5 query failed, q5: '.$q5;
    		throwMyExc($em);
    	}
    	$n = mysql_affected_rows();
    	if($n===0){
    		//do not increase count
    	}elseif($n===1){
    		$count++;
    	}else{
    		throwMyExc("in loop after query r4 - Error: mysql affected rows was neither 0 nor 1, n is: ".$n.";  ");
    	}
    	
    }
    	//=========== NOW delete those customActivities.=====
    //   only delete the customactivites for this user, fid, is_cf, surveyCategoryId.
    $q6 = 'delete from customActivity where userId = '.$userId.' and surveyCategoryId = '.$surveyCategoryId.' and fid = '.$fid.' and is_cf = '.$is_cf;
    $r6 = mysql_queryCustom($q6);
    if($r6===false){
    	$em='dropsurveyanswer: query 6 failed, q6: '.$q6;
    	throwMyExc($em);
    }
    
    
	return $count;   //this is the count of total surveyanswers deleted.
}

function getSurveyCategoryOwnerId($userFacilityId, $is_cf, $surveyCategoryId){
   $un = getSurveyCategoryOwner($userFacilityId, $is_cf, $surveyCategoryId);
   if($un==='')
	 return '';
   else
     return getUserId($un);
}

/*find out if , for this exact facility (fid and is_cf), who if anyone has started this surveycategory.   No custom activities counted here! only normal ones. 
//return:  either "" for no owner, or username (its an email addr) for an actual owner.  Not counting administrator.
    alg:
      1. get full list of activities under that surveycategorid.
      2. get the actual facilityId (from facility table) , using lookup of userFacility table via $fid.  
      2.  check all survey answers with  fid=y and iscustomfacility=is_cf and isCustomActivity=0
           which match the activityId from subquery 1. stop with first one with an 'owner'. 
*/
function getSurveyCategoryOwner($userFacilityId, $is_cf, $surveyCategoryId){
   $res1 = getActivitiesInSurveyCategory($surveyCategoryId); //cached for sure.
   $fid = getFacilityIdFromUserFacility($userFacilityId); //cached usually.
   $o = '';
   $un_arr = array();
   
   $is_cf = cleanStrForDb($is_cf);
   
   while($row = mysql_fetch_array($res1)){ 
      //in list of activities now. for this activity, see if any answers match.
      $res2 = mysql_queryCustom("
      select surveyAnswer.userId as userId 
      from surveyAnswer 
      join userFacility 
      on surveyAnswer.facilityId = userFacility.id 
      join facility 
      on userFacility.facilityId = facility.id 
      where facility.id = ".$fid."   
      and surveyAnswer.isCustomFacility = ".$is_cf."  
      and surveyAnswer.isCustomActivity = 0 
      and surveyAnswer.activityId = ".$row['aid']." 
     ");
     if($res2===false)
       throwMyExc('error in getsurveyCategoryOwner, first query. fid: '.$fid);
     $row2 = mysql_fetch_array($res2);
     if(mysql_num_rows($res2) === 0){ //if there are no rows - then no answer was found.
       //echo 'no owner found:';
       //echo ', survcategoryid:'.$surveyCategoryId.', aid:'.$row['aid'].', fid:'.$fid.', $is_cf:'.$is_cf.'<br/>';
       //no owner for that activity -- move on.
     }elseif(mysql_num_rows($res2) != 1){
	   //error
	   throwMyExc('Error (getsurveycategoryowner): More than one owner of surveyCategory for this facility.');     
     }else{
          //one owner: report it as the owner of the activity.  get the owner name.
          $userId = $row2['userId'];
          $un = getUsername($userId);
          //echo ' owner found, owner='.$un.'. ';
		  if(!in_array($un,$un_arr)){
		    $un_arr[] = $un;
		  }
     } 
	}//end while
	
	if(count($un_arr)>1) // multiple people started this survey ! error.
	  throwMyExc('Error: Multiple users started the same survey for this facility - this is not allowed.');  //maybe go to system error page,
	if(count($un_arr)===0)
	  return '';
	 
	    //log an error?
	$comma_separated_username_list = implode(',',$un_arr);
    
	return $comma_separated_username_list;	
	
}
 

// return the mysql resultset.
function getActivitiesInSurveyCategory($surveyCategoryId){
	$surveyCategoryId = cleanStrForDb($surveyCategoryId);
	
    $r = mysql_queryCustom("
select activity.id as aid, activity.title, activityCategory.id as actCat_id, surveyCategory.id  as scat_id 
from activity
join activityCategory
on activity.activityCategoryId = activityCategory.id
join surveyCategory
on activityCategory.surveyCategoryId = surveyCategory.id
where surveyCategory.id = ".$surveyCategoryId." 
  ");
  if($r===false)
     throwMyExc("getActivitiesinsurveycategory (surv cat ".$surveyCategoryId." ) had error in query.");
  return $r;
}















/* in:   
 * out:  the facility type id
 * 
 */
function getFacilityTypeStrFromFacilityEntry($userId, $facilityId, $isCustomFacility){
	if($isCustomFacility==1){
		// fid = customFacilityId -   table = customFacility      get facilityTypeId
		$customFacilityId = $facilityId;
		$customFacilityId = cleanStrForDb($customFacilityId);
		$q1 = 'select facilityTypeId, title
				 from customFacility 
				 join facilityType ON  customFacility.facilityTypeId = facilityType.id
				 where customFacility.id = '.$customFacilityId;
		$r1 = mysql_queryCustom($q1);
		if($r1===false){ 
			$em='getfacilitytypeidfromfacilityid: query q1 fail';
			throwMyExc($em);
		}
		$row = mysql_fetch_array($r1);
		$facilityTypeTitleStr = $row['title'];
		
		 
		
	}elseif($isCustomFacility==0){
		// fid = userFacilityId        table = userFacility     get  facilityTypeId
		$userFacilityId = $facilityId;
		$userFacilityId = cleanStrForDb($userFacilityId);
		$q2 = 'select facilityTypeId, title 
				from userFacility 
		        join facilityType  ON  userFacility.facilityTypeId = facilityType.id
				where userFacility.id = '.$userFacilityId;
		$r2 = mysql_queryCustom($q2);
		if($r2===false){
			$em='getfacilitytypeidfromfacilityid: query q2 fail';
			throwMyExc($em);
		}
		$row = mysql_fetch_array($r2);
		$facilityTypeTitleStr = $row['title'];
		
		
		
	}else{
		$em='getfacilitytypeidfromfacilityid:  iscustomfacil is not 0 and not 1';
		throwMyExc($em);
	}
	
	
	if($facilityTypeTitleStr==''){
			$em = 'getFacilityTypeStrFromFacilityEntry:  facil type str empty! userid: '.$userId.', fid: '.$facilityId.
			    ', iscf: '.$isCustomFacility;
			throwMyExc($em);
			
	}
	return $facilityTypeTitleStr;
}





















// Only call this for facilities - not valid for customFacilities!
function getFacilityIdFromUserFacility($userFacilityId){
  //check the userfacility table for this given id. get the facility id
  $userFacilityId = cleanStrForDb($userFacilityId);
  $r = mysql_queryCustom("select facilityId from userFacility where id = ".$userFacilityId);
  if($r===false)
   throwMyExc('getfacilityidfromuserfacility: query failed');
  if(mysql_num_rows($r) === 0)
   throwMyExc('getfacilityidfromuserfacility: fid answer was blank, thus userfacilityid was invalid (no entry in userfacility table with that id)');
  if(mysql_num_rows($r) > 1)
   throwMyExc("getfacilityidfromuserfacility: num rows > 1, this should never happen");
  if(mysql_num_rows($r) === 1){
   //normal here. 
   $row = mysql_fetch_array($r);
   $fid = $row['facilityId'];
   return $fid;
  }else{
   throwMyExc("getfacilityidfromuserfacility: num rows is invalid");
  }
  
}





//get a userid from username
function getUserId($un){
 $un = cleanStrForDb($un);
 $r = mysql_queryCustom("select id from user where username = '".$un."' ");
 if($r===false)
  throwMyExc('getUserId: query failed, username was: '.$un);
 if(mysql_num_rows($r) === 0){
  return '';
 }elseif(mysql_num_rows($r) > 1){
  //error
    throwMyExc('error in getuserid: userid > 1 for this username : user table corrupt or multiple users have same name (not allowed)');
 }elseif(mysql_num_rows($r) === 1){
  $row = mysql_fetch_array($r);
  return $row['id'];
 }
}

/*
 * 
 * 
 * sandbox:
 * 
 * 
 * create view ... 
 *  
 * alter view activitiesInSurveyCategory1 as

select activity.title, activityCategory.id as actCat_id, surveyCategory.id  as scat_id 
from activity
join activityCategory
on activity.activityCategoryId = activityCategory.id
join surveyCategory
on activityCategory.surveyCategoryId = surveyCategory.id
where surveyCategory.id = 1
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 * 
 */