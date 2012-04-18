<?php


require_once('DAO/activityCategoryDocDAO.php');

use urm\urm_secure\DAO\activityCategoryDocDAO;



function getBreadCrumbF($userFacilityId){   //facility only
	return getFacilityName($userFacilityId);
}
function getBreadCrumbCF($userFacilityId){    //customfacility  only
	return getCustomFacilityName($userFacilityId);
}

function getActivityCategoryName($activityCategoryId){
	$activityCategoryId = cleanStrForDb($activityCategoryId);
	$result = mysql_queryCustom("  SELECT title FROM activityCategory    
	              WHERE  id = ".$activityCategoryId." ");           //check un/pw against db.
	if($result === FALSE){
		$em= 'getActivityCategoryName(): result was false -  getSurveyCategoryName';
		throwMyExc($em);
	}
	$numrows = mysql_num_rows($result);
	if($numrows===false){
		$em= 'getActivityCategoryName(): Breadcrumb: numrows is false in getSurveyCategoryName. ';
		throwMyExc($em);
	}
	if ($numrows != 1){
		throwMyExc('getActivityCategoryName(): Breadcrumb: numrows != 1 in getSurveyCategoryName.');
	}	
	$row = mysql_fetch_array($result);
	if($row===false){
		throwMyExc('getActivityCategoryName(): bad row results');
	}
	return $row['title'];
}
/*
 * get a single doc title.
 */
function getActivityCategoryDocDAO($activityCategoryDocId){
	$activityCategoryDocId = cleanStrForDb($activityCategoryDocId);
	$r = mysql_queryCustom("select * from activityCategoryDoc where id = ".$activityCategoryDocId);
	if($r===false){
		$em='getactivitycategorydoctitle: q1 failed, id was: '.$activityCategoryId;
		throwMyExc($em);
	}
	if(mysql_num_rows($r)!=1){
		$em='getactivitycategorydoctitle: count of rows not 1, count: ' .mysql_num_rows($r);
		throwMyExc($em);
	}
	$row = mysql_fetch_assoc($r);
	
	$acDocDAO = new activityCategoryDocDAO();
	$acDocDAO->populateFromArr($row);
	return $acDocDAO;
}

function getActivityCategoryDoc($activityCategoryId){
	$activityCategoryId = cleanStrForDb($activityCategoryId);
	$result = mysql_queryCustom("  SELECT doc FROM activityCategory left join activityCategoryDoc 
	                      on activityCategory.activityCategoryDocId = activityCategoryDoc.id
	                      where activityCategory.id = ".$activityCategoryId.";");
	if($result === FALSE){
		$em= 'getActivityCategoryDoc: result was false -  getSurveyCategoryName';
		throwMyExc($em);
		
	}
	$numrows = mysql_num_rows($result);
	if($numrows===false){
		$em = 'getActivityCategoryDoc(): numrows is false in getSurveyCategoryName. ';
		throwMyExc($em);
	}
	if ($numrows != 1){
		$em = "getActivityCategoryDoc: Breadcrumb: numrows != 1 in getSurveyCategoryName.";
		throwMyExc($em);
	}	
	$row = mysql_fetch_array($result);
	if($row===false){
		$em= "getActivityCategoryDoc: bad row results";
		throwMyExc($em);
	}
	return cleanDocString($row['doc']);
}

function getSurveyCategoryName($surveyCategoryId){
	$surveyCategoryId = cleanStrForDb($surveyCategoryId);
	$result = mysql_queryCustom("  SELECT title FROM surveyCategory    
	              WHERE  id = ".$surveyCategoryId." ");           //check un/pw against db.
	if($result === FALSE){
		$em= "getSurveyCategoryName: result was false -  getSurveyCategoryName";
		throwMyExc($em);
	}
	$numrows = mysql_num_rows($result);
	if($numrows===false){
		$em= "getSurveyCategoryName: Breadcrumb: numrows is false in getSurveyCategoryName. ";
		throwMyExc($em);
	}
	if ($numrows != 1){
		$em= "getSurveyCategoryName: Breadcrumb: numrows != 1 in getSurveyCategoryName.";
		throwMyExc($em);
	}	
	$row = mysql_fetch_array($result);
	if($row===false){
		$em= "getSurveyCategoryName: bad row results";
		throwMyExc($em);
	}
	return $row['title'];
}



function getSurveyCategoryDoc($surveyCategoryId){
	$surveyCategoryId = cleanStrForDb($surveyCategoryId);
	$result = mysql_queryCustom("  SELECT doc FROM surveyCategory    
	              WHERE  id = ".$surveyCategoryId." ");           //check un/pw against db.
	if($result === FALSE){
		$em= "getSurveyCategoryDoc: result was false -  getSurveyCategoryName";
		throwMyExc($em);
	}
	$numrows = mysql_num_rows($result);
	if($numrows===false){
		$em= "getSurveyCategoryDoc: Breadcrumb: numrows is false in getSurveyCategoryName. ";
		throwMyExc($em);
	}
	if ($numrows != 1){
		$em= "getSurveyCategoryDoc: Breadcrumb: numrows != 1 in getSurveyCategoryName.";
		throwMyExc($em);
	}	
	$row = mysql_fetch_array($result);
	if($row===false){
		$em= "getSurveyCategoryDoc: bad row results";
		throwMyExc($em);
	}
	return cleanDocString($row['doc']);   //   base64_decode is an option 
}
function getSurveyCategoryDoc2($surveyCategoryId){
	$surveyCategoryId = cleanStrForDb($surveyCategoryId);
	$result = mysql_queryCustom(" SELECT doc2 FROM surveyCategory    
	              WHERE  id = ".$surveyCategoryId." ");           //check un/pw against db.
	if($result === FALSE){
		$em= "getSurveyCategoryDoc2: result was false -  getSurveyCategoryName";
		throwMyExc($em);
	}
	$numrows = mysql_num_rows($result);
	if($numrows===false){
		$em= "getSurveyCategoryDoc2: Breadcrumb: numrows is false. ";
		throwMyExc($em);
	}
	if ($numrows != 1){
		$em= "getSurveyCategoryDoc2: Breadcrumb: numrows != 1.";
		throwMyExc($em);
	}	
	$row = mysql_fetch_array($result);
	if($row===false){
		$em= "getSurveyCategoryDoc2: bad row results";
		throwMyExc($em);
	}
	return ($row['doc2']);   //   base64_decode is an option
}

//
//SELECT name
//FROM  `facility` 
//LEFT JOIN userFacility ON userFacility.facilityId = facility.id
//WHERE userFacility.id =32

function getFacilityName($userFacilityId){  //userfacilityid, so its a facility, not a customFacility.
	$userFacilityId = cleanStrForDb($userFacilityId);
	$result = mysql_queryCustom("  SELECT name FROM facility left join userFacility on userFacility.facilityId = facility.id 
	              WHERE userFacility.id = ".$userFacilityId." ");           //check un/pw against db.
	if($result === FALSE){
		$em= "getFacilityName: result was false -  getfacilityname";
		throwMyExc($em);
	}
	$numrows = mysql_num_rows($result);
	if($numrows===false){
		$em= "getFacilityName: Breadcrumb: numrows is false in getFacilityname. ";
		throwMyExc($em);
	}
	if ($numrows != 1){
		$em= "getFacilityName: Breadcrumb: numrows != 1 in getFacilityname.";
		throwMyExc($em);
	}	
	$row = mysql_fetch_array($result);
	if($row===false){
		$em= "getFacilityName: bad row results in getFacilityname";
		throwMyExc($em);
	}
	return $row['name'];

}

function getCustomFacilityName($customFacilityId){
	$customFacilityId = cleanStrForDb($customFacilityId);
	$result = mysql_queryCustom("  SELECT name FROM customFacility    
	              WHERE customFacility.id = ".$customFacilityId." ");           //check un/pw against db.
	if($result === FALSE){
		$em = "getCustomFacilityName(): result was false";
		throwMyExc($em);
	}
	$numrows = mysql_num_rows($result);
	if($numrows===false){
		$em= "getCustomFacilityName(): Breadcrumb: numrows is false in getCustomFacilityName. ";
		throwMyExc($em);
	}
	if ($numrows != 1){
		$em= "getCustomFacilityName(): Breadcrumb: numrows != 1";
		throwMyExc($em);
	}	
	$row = mysql_fetch_array($result); 
	if($row===false){ //false if there are no more rows.
		$em= "getCustomFacilityName(): false as row result in getCustomFacilityName";
		throwMyExc($em);
	}
	return $row['name'];
}