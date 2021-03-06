<?php

require_once('validationFunctions.php');
require_once('surveyCategoriesFunctions.php');


function getActivityData($activityId){
	$activityId = cleanStrForDb($activityId);
	$o = array();
	$id = "uninit id";
	$title = "uninit title";
	$descr = "uninit descr";
	$o['id'] = $id;
	$o['title'] = $title;
	$o['descr'] = $descr;
	$result = mysql_queryCustom("  select  id,title, description, isForAdult, isForPediatric, isForNatal from activity where id = ".$activityId.";");           //check un/pw against db.
	if($result === FALSE){
		throwMyExc(' getActivityData(): query failed');
	}
	$numrows = mysql_num_rows($result);
	if($numrows===false){
		throwMyExc(' getActivityData(): numrows failed');
	}
	if ($numrows == 0){
		return $o;
	}
	if ($numrows > 1){
		return false;
	}
	while($row = mysql_fetch_array($result))
	{
		$id = $row['id'];
		$title = $row['title'];
		$descr = $row['description'];
		$isForAdult = $row['isForAdult'];
		$isForPediatric = $row['isForPediatric'];
		$isForNatal = $row['isForNatal'];
	}
	//check the 'isfor...' stuff.
	if($isForAdult!="yes" && $isForAdult!="no"){
		$errorMsg='getactivitydata: isforadult field is neither yes nor no. this is not allowed';
		throwMyExc($errorMsg);
	}
	if($isForPediatric!="yes" && $isForPediatric!="no"){
		$errorMsg = 'getactivitydata: isforpediatric field is neither yes nor no. this is not allowed';
		throwMyExc($errorMsg);
	}
	if($isForNatal!="yes" && $isForNatal!="no"){
		$errorMsg = 'getactivitydata: isfornatal field is neither yes nor no. this is not allowed';
		throwMyExc($errorMsg);
	}
	$o['id'] = $id;
	$o['title'] = cleanDocString($title);
	$o['descr'] = cleanDocString($descr);
	$o['isForAdult'] = $isForAdult;
	$o['isForPediatric'] = $isForPediatric;
	$o['isForNatal'] = $isForNatal;
	
	return $o;
	 
}
function getCustomActivityData($userId, $customActivityId){
	$userId = cleanStrForDb($userId);
	$customActivityId = cleanStrForDb($customActivityId);
	$o = array();
	$id = "uninit id";
	$title = "uninit title";
	$descr = "uninit descr";
	$fid = "uninit fid";
	$is_cf = "uninit is_cf";
	$o['id'] = $id;
	$o['title'] = $title;
	$o['descr'] = $descr;
	$o['fid'] = $fid;
	$o['is_cf'] = $is_cf;
	$result = mysql_queryCustom("select id,title, description, fid, is_cf from customActivity where userid=".$userId." and id = ".$customActivityId.";");           //check un/pw against db.
	if($result === FALSE){
		$errorMsg='getCustomActivityData() query failed';
		throwMyExc($errorMsg);
	}
	$numrows = mysql_num_rows($result);
	if($numrows===false){
		return false;
		$errorMsg='getCustomActivityData() - num rows = false';
		throwMyExc($errorMsg);
	}
	if ($numrows == 0){
		return $o;
	}
	if ($numrows > 1){
		return false;
	}
	while($row = mysql_fetch_array($result))
	{
		$id = $row['id'];
		$title = $row['title'];
		$descr = $row['description'];
		$fid = $row['fid'];
		$is_cf = $row['is_cf'];
	}
	$o['id'] = $id;
	$o['title'] = cleanDocString($title);
	$o['descr'] = cleanDocString($descr);
	$o['fid'] = cleanDocString($fid);
	$o['is_cf'] = cleanDocString($is_cf);
	
	return $o;
}

function getActivityCategoryIdFromActivityId($activityId){
	// check the activity table
	$activityId = cleanStrForDb($activityId);
	$q = 'select activityCategoryId from activity where id = '.$activityId;
	$r = mysql_queryCustom($q);
	if($r===false){
		$em='getactivitycategoryidfromactivityid - query fail, q: '.$q;
		throwMyExc($em);
	}
	if(mysql_num_rows($r) != 1){
		$em='getact cat id from acti id  - numrows not = 1';
		throwMyExc($em);
	}
	$row = mysql_fetch_assoc($r);
	$acId = $row['activityCategoryId'];
	return $acId;
}

function submitSurveyAnswer($userId, $_fid,$_aid,$_is_cf,$_is_ca,$_isPerformedAdult,$_isPerformedPediatric,$_isPerformedNatal,
 					 $_hasTimestandardAdult,$_hasTimestandardPediatric,$_hasTimestandardNatal,$_durationAdult,$_durationPediatric,
 					 		 $_durationNatal,$_volumeAdult,$_volumePediatric,$_volumeNatal,$methodologyAdult,$methodologyPediatric,$methodologyNatal){
 					 		 
 	$userId = cleanStrForDb($userId);
	$_fid = cleanStrForDb($_fid);
	$_aid = cleanStrForDb($_aid);
	$_is_cf = cleanStrForDb($_is_cf);
	$_is_ca = cleanStrForDb($_is_ca);
	$_isPerformedAdult = cleanStrForDb($_isPerformedAdult);
	$_isPerformedPediatric = cleanStrForDb($_isPerformedPediatric);
	$_isPerformedNatal = cleanStrForDb($_isPerformedNatal);
	$_hasTimestandardAdult = cleanStrForDb($_hasTimestandardAdult);
	$_hasTimestandardPediatric = cleanStrForDb($_hasTimestandardPediatric);
	$_hasTimestandardNatal = cleanStrForDb($_hasTimestandardNatal);
	$_durationAdult = cleanStrForDb($_durationAdult);
	$_durationPediatric = cleanStrForDb($_durationPediatric);
	$_durationNatal = cleanStrForDb($_durationNatal);
	$_volumeAdult = cleanStrForDb($_volumeAdult);
	$_volumePediatric = cleanStrForDb($_volumePediatric);
	$_volumeNatal = cleanStrForDb($_volumeNatal);
	$methodologyAdult = cleanStrForDb($methodologyAdult);
	$methodologyPediatric = cleanStrForDb($methodologyPediatric);
	$methodologyNatal = cleanStrForDb($methodologyNatal);				 		 
 					 		 
	if(false===surveyAnswerPresent($userId, $_fid,$_aid,$_is_cf,$_is_ca)){
		return insertSurveyAnswer($userId, $_fid,$_aid,$_is_cf,$_is_ca,$_isPerformedAdult,$_isPerformedPediatric,$_isPerformedNatal,
 					 $_hasTimestandardAdult,$_hasTimestandardPediatric,$_hasTimestandardNatal,$_durationAdult,$_durationPediatric,
 					 		 $_durationNatal,$_volumeAdult,$_volumePediatric,$_volumeNatal,$methodologyAdult,$methodologyPediatric,$methodologyNatal);
	}else{
	   return updateSurveyAnswer($userId, $_fid,$_aid,$_is_cf,$_is_ca,$_isPerformedAdult,$_isPerformedPediatric,$_isPerformedNatal,
 					 $_hasTimestandardAdult,$_hasTimestandardPediatric,$_hasTimestandardNatal,$_durationAdult,$_durationPediatric,
 					 		 $_durationNatal,$_volumeAdult,$_volumePediatric,$_volumeNatal,$methodologyAdult,$methodologyPediatric,$methodologyNatal);
	}
		
}
function surveyAnswerPresent($userId, $fid,$aid,$is_cf,$is_ca){
	$userId = cleanStrForDb($userId);
	$fid = cleanStrForDb($fid);
	$aid = cleanStrForDb($aid);
	$is_cf = cleanStrForDb($is_cf);
	$is_ca = cleanStrForDb($is_ca);
	
	$result = mysql_queryCustom("select  durationAdult from surveyAnswer where userId=".$userId."  and  facilityId = ".$fid."
                   and activityId=".$aid." and isCustomFacility=".$is_cf." and isCustomActivity=".$is_ca."  ");           //check un/pw against db.
	if($result === FALSE){
		throwMyExc('surveyanswerpresent(): select from surveyanswer table - query failed, userId:'.$userId.', fid:'.$fid.', aid:'.$aid.',is_cf:'.$is_cf.', is_ca:'.$is_ca);
	}
	$numrows = mysql_num_rows($result);
	if($numrows===FALSE){
		throwMyExc('surveyanswerpresent(): mysql num rows failed');
	}
	if ($numrows == 1){
		return true;
	}
	elseif ($numrows == 0){
		return false;
	}else{
		throwMyExc('surveyanswerpresent(): rows found for these survey answer identifiers was not 1, and not 0. error!');
	}
}


/*   this works:
 insert  into surveyAnswer   (userid, facilityid, activityId, isCustomFacility,
					isCustomActivity, isPerformedAdult, isPerformedPediatric, isPerformedNatal, 
					hasTimestandardAdult, hasTimestandardPediatric,hasTimestandardNatal, 
					durationAdult, durationPediatric, durationNatal,volumeAdult,volumePediatric,volumeNatal,methodologyAdult,methodologyPediatric,methodologyNatal)     
	               values (1,32,2,0,0,'na','na','na',  'na','na','na',1,1,1,3,3,3,'na','na','na')
 
 *  INPUTS ALREADY CLEAN FROM ABOVE CALLER.
 *  
 */
function insertSurveyAnswer($userId, $_fid,$_aid,$_is_cf,$_is_ca,$_isPerformedAdult,$_isPerformedPediatric,$_isPerformedNatal,
 					 $_hasTimestandardAdult,$_hasTimestandardPediatric,$_hasTimestandardNatal,$_durationAdult,$_durationPediatric,
 					 		 $_durationNatal,$_volumeAdult,$_volumePediatric,$_volumeNatal,$_methodologyAdult,$_methodologyPediatric,$_methodologyNatal){
	//try to insert a survey answer in surveyAnswer table. fail : false.    
	$res = mysql_queryCustom("insert  into surveyAnswer   (userid, facilityid, activityId, isCustomFacility,
					isCustomActivity, isPerformedAdult, isPerformedPediatric, isPerformedNatal, 
					hasTimestandardAdult, hasTimestandardPediatric,hasTimestandardNatal, 
					durationAdult, durationPediatric, durationNatal,volumeAdult,volumePediatric,volumeNatal,methodologyAdult,methodologyPediatric,methodologyNatal)     
	               values (".$userId.",".$_fid.",".$_aid.",".$_is_cf.",".$_is_ca.",'".$_isPerformedAdult."','".$_isPerformedPediatric."','".
				    $_isPerformedNatal."','".$_hasTimestandardAdult."','".$_hasTimestandardPediatric."','".$_hasTimestandardNatal."',".
				    $_durationAdult.",".$_durationPediatric.",".$_durationNatal.",".$_volumeAdult.",".$_volumePediatric.",".$_volumeNatal.",'".$_methodologyAdult."','".$_methodologyPediatric.
				    "','".$_methodologyNatal."')");
	if($res===false){
		$msg= "insertsurveyanswer(): query failed. must have therefore failed trying to submit survey answer";
		throwMyExc($msg);
	}
	
	//======= debug test!
//$msg= "insertsurveyanswer(): DEBUG ERROR";
//throwMyExc($msg);
	//===================
	
	
	
	
	$n = mysql_affected_rows();
	if($n===0){
		return "Failed to submit survey answer";//not a query error
	}elseif( $n===1){
		return "Successfully submitted survey answer";
	}elseif( $n!=1){
		throwMyExc('insertsurveyanswer():  num affected rows was neither 0 nor 1, db corruption');
	}
	//succeeded!
 	//	if(!mysql_queryCustom("insert  into surveyAnswer   (userid, facilityid, activityId, isCustomFacility,
//					isCustomActivity, durationAdult, durationPediatric, durationNatal,volume,status)     
//	               values ( 1, 32, 2,0,0,1,2, 3,789,'unknown-doofus')")){
//	 return "failed super test! bad";
//	               }
}
/*
 * INPUTS ALREADY CLEAN FROM CALLER. 
 */
function updateSurveyAnswer($userId, $_fid,$_aid,$_is_cf,$_is_ca,$_isPerformedAdult,$_isPerformedPediatric,$_isPerformedNatal,
 					 $_hasTimestandardAdult,$_hasTimestandardPediatric,$_hasTimestandardNatal,$_durationAdult,$_durationPediatric,
 					 		 $_durationNatal,$_volumeAdult,$_volumePediatric,$_volumeNatal,$_methodologyAdult,$_methodologyPediatric,$_methodologyNatal){
	$res = mysql_queryCustom("update    surveyAnswer    set 
		isPerformedAdult='".$_isPerformedAdult."',
		isPerformedPediatric='".$_isPerformedPediatric."',
		isPerformedNatal='".$_isPerformedNatal."',
		hasTimestandardAdult='".$_hasTimestandardAdult."',
		hasTimestandardPediatric='".$_hasTimestandardPediatric."',
		hasTimestandardNatal='".$_hasTimestandardNatal."',
		durationAdult=".$_durationAdult.",
		durationPediatric=".$_durationPediatric.",
		durationNatal=".$_durationNatal.",
		volumeAdult=".$_volumeAdult.",
		volumePediatric=".$_volumePediatric.",
		volumeNatal=".$_volumeNatal.",
		methodologyAdult='".$_methodologyAdult."',   
		methodologyPediatric='".$_methodologyPediatric."',
		methodologyNatal='".$_methodologyNatal."' 
		where userid=".$userId." and  facilityid=".$_fid." and activityId=".$_aid." and isCustomFacility=".$_is_cf."
					 and isCustomActivity=".$_is_ca."   ");
	if($res===false){ 
		throwMyExc('updatesurveyanswer(): query failed');
	}
	$n = mysql_affected_rows();
	if($n===0){
	    //try to insert a survey answer in surveyAnswer table. fail : false.
		return "Failed trying to update survey answer - no change in answer?";
	}elseif($n==1){
		//succeeded!
		return "Successfully updated survey answer";
	}else{
		throwMyExc('updatesurveyanswer(): mysql affectd rows neither 0 nor 1: unknown error');
	}
}

function markStatus($userId, $fid, $isCustomFacility, $surveyCategoryId){
	if($surveyCategoryId != 1){  // if surv cat id is not 1, we dont care - do nothing, exit.
		return;
	}
	$userId = cleanStrForDb($userId);
	$fid = cleanStrForDb($fid); //either userfacility id or customfacility id
	$isCustomFacility = cleanStrForDb($isCustomFacility);
	
	if($isCustomFacility==0){//use userFacility table
		$tableName = 'userFacility';		
	}elseif($isCustomFacility==1){
 		$tableName = 'customFacility';		 
	}else{
		$em = 'error: markStatus() - isCustomfacility is neither 0 nor 1.';
		throwMyExc($em);
	}
	
	$q1 = 'select completionStatus from '.$tableName.' where id = '.$fid.'  and userId = '.$userId.'  ;    ';
	$res = mysql_queryCustom($q1);    //do a get of current completion status.
	if($res===false){
		$em = 'markstatus: q1 fail: '.$q1;
		throwMyExc($em);
	}
	$numrows = mysql_num_rows($res);
	if($numrows!=1){
		$em = 'markstatus: q1 numrows not = 1, q1: '.$q1;
		throwMyExc($em);
	}
	$row = mysql_fetch_array($res);
	$cStat = trim($row['completionStatus']);
	if($cStat == '0000000'){   //only applicable for acute care hospital survey.  it's id = 1, Acute Care Inpatient Services
		         				// in surveyCategory table. 
		if($surveyCategoryId == 1){  //ok! it's applicable, and it was unfinished.
			//die('surveycategoryid == 1!');
			//set new cStat.
			$newCStat = '1000000';
			//write it to the db.
			$q2 = 'update '.$tableName.' set completionStatus = "'.$newCStat.'" where id = '.$fid.'  and userId = '.$userId.'; ';
			$res = mysql_queryCustom($q2); //does an update, so return is true on sucess, false on error.
			if($res===false){
				$em = 'markstatus: q2 fail: '.$q2;
				throwMyExc($em); 
			}
			$n = mysql_affected_rows();
			if($n!=1){
				$em = 'markstatus: mysql affected rows for q2 was not = 1, thus invalid.  q2: '.$q2;
			}
			//confirmed one row updated to be '1000000'. now, send the email to the user.
			sendCompletionStatusEmail($userId,$surveyCategoryId);
			
			
		}else{
			//do nothing. cuz its other survey categories.
		}
	}elseif($cStat == '1000000'){
		//do nothing, cuz it's either already finished, or maybe another survey category.
	}else{
		$em='markstatus:  current cStat was neither 0000000 nor 1000000, impossible: cstat: '.$cStat;
		throwMyExc($em);
	}
	
	
}

function sendCompletionStatusEmail($userId,$surveyCategoryId){
	if($surveyCategoryId != 1){
		$em= 'sendcompletionstatusemail:  surv cat id not 1, invalid';
		throwMyExc($em);
	}
	$username = getUsername($userId);
	$to = $username;
    $from = "URM_Notifications--do_not_reply@aarc.org"; 
    $subject = "URM Notification"; 
	$headers  = "From: $from\r\n"; 
    $headers .= "Content-type: text/html\r\n"; 
	$message =  completionText::$completionMessage;
	
	$e = '';
    if(!mail($to,$subject,$message,$headers)){
  	  $em .=  'sendcompletionstatusemail():  Failure sending email<br/>';
  	  throwMyExc($em);
    }
}





function getDurationAdult($userId, $fid,$aid,$is_cf,$is_ca){
	$userId = cleanStrForDb($userId);
	$fid = cleanStrForDb($fid);
	$aid = cleanStrForDb($aid);
	$is_cf = cleanStrForDb($is_cf);
	$is_ca = cleanStrForDb($is_ca);
	$result = mysql_queryCustom("  select  durationAdult from surveyAnswer where userid=".$userId."  and  facilityId = ".$fid."
                   and activityId=".$aid." and isCustomFacility=".$is_cf." and isCustomActivity=".$is_ca."  ");           //check un/pw against db.
	if($result === FALSE){
		throwMyExc('getDurationadult(): query failed');
	}
	$numrows = mysql_num_rows($result);
	if($numrows === FALSE){
		throwMyExc('getDurationadult(): numrows failed');
	}
    if ($numrows == 0){
		return "";
	}
	else if ($numrows == 1){
	 $row = mysql_fetch_array($result);
		$dur = $row['durationAdult'];
	 return $dur;
	}
	else{
		return "Error: check surveyanswer for duplicate rows.  numrows neither 0 nor 1 in getDurationAdult! its ".$numrows;
	}
}

function getDurationPediatric($userId, $fid,$aid,$is_cf,$is_ca){
	$userId = cleanStrForDb($userId);
	$fid = cleanStrForDb($fid);
	$aid = cleanStrForDb($aid);
	$is_cf = cleanStrForDb($is_cf);
	$is_ca = cleanStrForDb($is_ca);
	$result = mysql_queryCustom("  select  durationPediatric from surveyAnswer where userid=".$userId."  and  facilityId = ".$fid."
                   and activityId=".$aid." and isCustomFacility=".$is_cf." and isCustomActivity=".$is_ca."  ");           //check un/pw against db.
	if($result == FALSE){
		throwMyExc('getDurationPediatric(): query failed');
	}
	$numrows = mysql_num_rows($result);
		//return "test debugline  in getDuration!";
	if($numrows === FALSE){
		throwMyExc('getDurationPediatric(): numrows failed');
	}
    if ($numrows == 0){
		return "";
	}
	else if ($numrows == 1){
	 $row = mysql_fetch_array($result);
		$dur = $row['durationPediatric'];
	 return $dur;
	}
	else{
		return "Error: numrows neither 0 nor 1 in getDurationPediatric!";
	}
}
function getDurationNatal($userId, $fid,$aid,$is_cf,$is_ca){
	$userId = cleanStrForDb($userId);
	$fid = cleanStrForDb($fid);
	$aid = cleanStrForDb($aid);
	$is_cf = cleanStrForDb($is_cf);
	$is_ca = cleanStrForDb($is_ca);
		
	$result = mysql_queryCustom("  select  durationNatal from surveyAnswer where userid=".$userId."  and  facilityId = ".$fid."
                   and activityId=".$aid." and isCustomFacility=".$is_cf." and isCustomActivity=".$is_ca."  ");           //check un/pw against db.
	if($result === FALSE){
		throwMyExc('getdurationnatal(): query failed');
	}
	$numrows = mysql_num_rows($result);
		//return "test debugline  in getDuration!";
	if($numrows === FALSE){
		throwMyExc('getdurationnatal(): num rows failed');
	}
    if ($numrows == 0){
		return "";
	}
	else if ($numrows == 1){
	 $row = mysql_fetch_array($result);
		$dur = $row['durationNatal'];
	 return $dur;
	}
	else{
		throwMyExc('Error: numrows neither 0 nor 1 in getDurationNatal!');
	}
	
}
function getVolume($userId, $fid,$aid,$is_cf,$is_ca){
	$userId = cleanStrForDb($userId);
	$fid = cleanStrForDb($fid);
	$aid = cleanStrForDb($aid);
	$is_cf = cleanStrForDb($is_cf);
	$is_ca = cleanStrForDb($is_ca);

	$result = mysql_queryCustom("  select  volume from surveyAnswer where userid=".$userId."  and  facilityId = ".$fid."
                   and activityId=".$aid." and isCustomFacility=".$is_cf." and isCustomActivity=".$is_ca."  ");           //check un/pw against db.
	if($result === FALSE){
		throwMyExc('getvolume(): query failed');
	}
	$numrows = mysql_num_rows($result);
		//return "test debugline  in getDuration!";
	if($numrows === FALSE){
		throwMyExc('getvolume(): numrows failed');
	}
    if ($numrows == 0){
		return "";
	}
	else if ($numrows == 1){
	    $row = mysql_fetch_array($result);
		$v = $row['volume'];
		return $v;
	}
	else{
		throwMyExc('Error: numrows neither 0 nor 1 in getDurationNatal!');
	}
	
}
function getSurveyAnswerRow($userId, $fid,$aid,$is_cf,$is_ca){
	$userId = cleanStrForDb($userId);
	$fid = cleanStrForDb($fid);
	$aid = cleanStrForDb($aid);
	$is_cf = cleanStrForDb($is_cf);
	$is_ca = cleanStrForDb($is_ca);
	
	$q = "select * from surveyAnswer where userid=".$userId."  and  facilityId = ".$fid."
                   and activityId=".$aid." and isCustomFacility=".$is_cf." and isCustomActivity=".$is_ca;
	$result = mysql_queryCustom($q);           //check un/pw against db.
	if($result === FALSE){
		throwMyExc('getsurveyanswerrow(): query failed, query-text: '.$q);
	}
	$numrows = mysql_num_rows($result);
		//return "test debugline  in getDuration!";
	if($numrows === FALSE){
		throwMyExc('getsurveyanswerrow(): numrows failed');
	}
    if ($numrows == 0){
		return "";
	}
	else if ($numrows == 1){
	 $row = mysql_fetch_array($result);
	 return $row;
	}
	else{
		$em = "Error: numrows neither 0 nor 1 in getDurationNatal!";
		throwMyExc($em);
	}
}