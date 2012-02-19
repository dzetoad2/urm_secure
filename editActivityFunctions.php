<?php
function  updateCustomActivity($title,$descr,$customActivityId)
{
	$title = cleanStrForDb($title);
	$descr = cleanStrForDb($descr);
	$customActivityId = cleanStrForDb($customActivityId);
	
	//insert into db.
	 $res=mysql_queryCustom("update   customActivity  set title='".$title."', description='".$descr."'
	                 where id = ".$customActivityId." ");
	 if($res===false){
	 	$em='updateCustomActivity: query failed';
	 	throwMyExc($em);
	 }
	 $r = mysql_affected_rows();
	 if($r == 0){
	   return $r;
	 }
	 if($r != 1 ){
	 	throwMyExc("updateCustomActivity: Affected rows was not one");
	 }
	 return $r;
}
function updateActivity($title,$descr,$activityId)
{
	$title = cleanStrForDb($title);
	$descr = cleanStrForDb($descr);
	$activityId = cleanStrForDb($activityId);
	
	//insert into db.
	 $res=mysql_queryCustom("update   activity  set title='".$title."', description='".$descr."'
	                 where id = ".$activityId." ");
	 if($res===false){
	 	$em='updateActivity: query failed';
	 	throwMyExc($em);
	 }
	 $r = mysql_affected_rows();
	 if($r == 0){
	   return $r;
	 }
	 if($r != -1 ){
	 	throwMyExc("updateActivity: Affected rows was not one");
	 }
	 return $r;
}