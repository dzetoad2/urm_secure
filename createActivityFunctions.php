<?php
function createCustomActivity($title,$descr,$surveyCategoryId,$userId)
{
	//insert into db.
	$title = cleanStrForDb($title);
	$descr = cleanStrForDb($descr);
	$surveyCategoryId = cleanStrForDb($surveyCategoryId);
	$userId = cleanStrForDb($userId);
	
	 $res=mysql_queryCustom("INSERT INTO customActivity (title, description, surveyCategoryId, userid)
		     VALUES ('".$title."','".$descr."', ".$surveyCategoryId.", ".$userId.")");
	 if($res===false){
	 	throwMyExc("createcustomactivity: insert query returned false");	
	 }
	 $r = mysql_affected_rows(); //shows -1 if last query failed.
	 if($r===false){
	 	throwMyExc("createcustomactivity(): Affected rows was false, not a number");
	 }
	 if($r!=1){
	 	throwMyExc('createcustomactivity(): affected rows not 1, something went wrong.');
	 }
	 $id = mysql_insert_id();
	 if($id === false || $id === 0){
   	 	throwMyExc('createcustomactivity(): mysql insert id is false or 0 - something went wrong.');
	 }
	 return $id; // this is the id of the newly created custom activity.
}
//======ONLY USED BY ADMIN===== MIGHT BE A LITTLE ROUGH IN PRESENTATION=====
//function createActivity($title,$descr,$activityCategoryId)
//{
//	$title = cleanStrForDb($title);
//	$descr = cleanStrForDb($descr);
//	$activityCategoryId = cleanStrForDb($activityCategoryId);
//
//	$isForAdult = "yes";
//	$isForPediatric = "yes";
//	$isForNatal = "yes";
//	//insert into db.
//	
//	 $res = mysql _queryCustom("INSERT INTO activity (title, description, activityCategoryId, isForAdult, isForPediatric, isForNatal)
//		     VALUES ('".$title."','".$descr."', ".$activityCategoryId.",'".$isForAdult."','".$isForPediatric."','".$isForNatal."')");
//	 if($res===false){
//	 	throwMyExc("createactivity: insert query returned false");	
//	 }
//	 $r = mysql_affected_rows(); //-1 if last query failed
//	 if($r===false){
//	 	throwMyExc("createactivity: Affected rows was false, not a number");
//	 }
//	 if($r!=1){
//	 	throwMyExc('createcustomactivity(): affected rows not 1, something went wrong.');
//	 }
//	 return $r;  //returns 0 if the insert affected 0 rows
//}






