<?php 
require_once('activitiesSupplementalFunctions.php');
require_once('DAO/myFacilitiesRowsDAO.php');
require_once('DAO/myCustomFacilitiesRowsDAO.php');


use urm\urm_secure\DAO\myFacilitiesRowsDAO;
use urm\urm_secure\DAO\myCustomFacilitiesRowsDAO;



/*
 * for each facility we see here, we must query all the surveycategories beneath it to see if they are complete or not.
 * so we know its Facilities, not customFacilities.
 */
function getMyFacilitiesRowsHtml_WithStatus($userId){
	
	$userId = cleanStrForDb($userId);
	 
	
	$result = mysql_queryCustom("  select userFacility.id, facility.name, facility.address, facility.city, facility.state, facility.zip, facilityType.title, facilityType.id AS facilityTypeId from user
join userFacility on user.id = userFacility.userid
left join facilityType on facilityType.id = userFacility.facilityTypeId 
join facility on userFacility.facilityId = facility.id
where user.id = '".$userId."' ");           //check un/pw against db.
	if($result === FALSE){
		throwMyExc('getmyfacilitiesrowshtml_withstatus failed query');
	}
	$numrows = mysql_num_rows($result);
	if($numrows===false){
		throwMyExc('getmyfacilitiesrowshtml_withstatus - mysql_num_rows failed');
			}
	if ($numrows == 0){
		//return "";
	}
	$o = '';
	$atLeastOneSurveyCategoryIsCompleteForAll = false;
	$facilityCompleteCount=0;
	$facilityPartiallyCompleteCount=0;
	$facilityCount=0;
	while($row = mysql_fetch_array($result))
	{
		$title = $row['title'];
		if(!$title ||  trim($title==""))
		  $title = "Unknown (error)";
		$facilityCompletionStatus = isFacilityComplete($userId,$row['id']);
		if($facilityCompletionStatus == 'complete'){ //row[id] here is the userFacility's id, (which down in the details is the $fid where customFacility=0).
			$rowStatus = '<img src="images/b_check.png"/>';
			$facilityCompleteCount++; 
		}elseif($facilityCompletionStatus == 'partiallyComplete'){
			$rowStatus = '';
			$facilityPartiallyCompleteCount++;
			//$atLeastOneSurveyCategoryIsCompleteForAll = true;
		}elseif($facilityCompletionStatus == 'noSurveyCategoriesComplete'){
			$rowStatus = '';
		}else{
			$em='getmyfacilitiesrows - facil completion status invalid ';
			throwMyExc($em);
		}
		if(defined('DEBUG')){
		 $o .=  '<tr class="userFacilityRow clickable" id="'.$row['id'].'"><td class="cell1" id="'.$row['id'].'">'.$row['id'].''.'</td><td class="nameCell" id="'.$row['name'].'">'.$row['name'].'</td><td>'.$row['address'].'</td><td>'.$row['city'].
  		           '</td><td>'.$row['state'].'</td><td>'.$row['zip'].'</td><td>'.$title.'</td><td>'.$rowStatus.'</td></tr>';
		}else{
		 $o .=  '<tr class="userFacilityRow clickable" id="'.$row['id'].'"><td class="nameCell" id="'.$row['name'].'">'.$row['name'].'</td><td>'.$row['address'].'</td><td>'.$row['city'].
  		           '</td><td>'.$row['state'].'</td><td>'.$row['zip'].'</td><td class="facilityTypeId" id="'.$row['facilityTypeId'].'">'.$title.'</td><td>'.$rowStatus.'</td></tr>';
		 // class="facilityTypeId" id="'.$row['facilityTypeId'].'"
		 
		}
		$facilityCount++;
	}
	if($facilityCompleteCount + $facilityPartiallyCompleteCount == $facilityCount      && $facilityCount  != 0){ 
	   $atLeastOneSurveyCategoryIsCompleteForAll=true;
	}
	
	
	$myFRDao = new myFacilitiesRowsDAO();
	$myFRDao->o = $o;
	
	$myFRDao->atLeastOneSurveyCategoryIsCompleteForAll = $atLeastOneSurveyCategoryIsCompleteForAll;
	
	return $myFRDao;
	
	
	
	
	
	 
}
















function getMyCustomFacilitiesRowsHtml_WithStatus($userId){
	/*  first get list of custom facilities ids.  then, for each cf_id, query all surveyCatgories beneath it.
	 *  we know this is only customfacilities not facilities.
	 */
	$userId = cleanStrForDb($userId);

	$result = mysql_queryCustom("  select customFacility.id, customFacility.name, customFacility.address, 
	customFacility.city, customFacility.state, customFacility.zip, facilityType.title, facilityType.id AS facilityTypeId from customFacility
	left join facilityType on facilityType.id = customFacility.facilityTypeId 
	where userid = ".$userId." ");           //check un/pw against db.
	if($result == FALSE){
		$em='getmycustomfacilitiesrowshtml_withstatus:  query fail result';
		throwMyExc($em);
	}
	$numrows = mysql_num_rows($result);
	if($numrows===false){
		$em='getmycustomfacilitiesrowshtml_withstatus:  numrows was false';
		throwMyExc($em);
	}
	if ($numrows == 0){
		//return " ";
	}
	$o = '';
	$facilityCompleteCount=0;
	$facilityPartiallyCompleteCount=0;
	$facilityCount=0;	
	$atLeastOneSurveyCategoryIsCompleteForAll = false;
	while($row = mysql_fetch_array($result))
	{
		$title = $row['title'];
		if(!$title ||  trim($title==""))
		  $title = "UNK";
		$facilityCompletionStatus = isCustomFacilityComplete($userId,$row['id']);
		  
		if($facilityCompletionStatus == "complete" ){
			$rowStatus = '<img src="images/b_check.png"/>';
			$facilityCompleteCount++;
		}elseif($facilityCompletionStatus == "partiallyComplete"){
			$rowStatus = '';
			$facilityPartiallyCompleteCount++;
		}elseif($facilityCompletionStatus == "noSurveyCategoriesComplete"){
			$rowStatus = '';
		}else{
			$em='getmycustomfacilitiesrows - facil completion status invalid ';
			throwMyExc($em);
			
		}
		if(defined('DEBUG')){
		 $o .=  '<tr class="customFacilityRow" id="'.$row['id'].'"><td class="cell1" id="'.$row['id'].'">'.$row['id'].''.'</td><td class="nameCell" id="'.$row['name'].'">'.$row['name'].'</td><td>'.$row['address'].'</td><td>'.$row['city'].
  		           '</td><td>'.$row['state'].'</td><td>'.$row['zip'].'</td><td>'.$title.'</td><td>'.$rowStatus.'</td></tr>';
		}else{
		 $o .=  '<tr class="customFacilityRow" id="'.$row['id'].'"><td class="nameCell" id="'.$row['name'].'">'.$row['name'].'</td><td>'.$row['address'].'</td><td>'.$row['city'].
  		           '</td><td>'.$row['state'].'</td><td>'.$row['zip'].'</td><td class="facilityTypeId" id="'.$row['facilityTypeId'].'">'.$title.'</td><td>'.$rowStatus.'</td></tr>';
		}
		$facilityCount++;
	}
	if($facilityCompleteCount + $facilityPartiallyCompleteCount == $facilityCount){ 
	   $atLeastOneSurveyCategoryIsCompleteForAll=true;
	}
	
	$myFRDao = new myCustomFacilitiesRowsDAO();
	$myFRDao->o = $o;
	$myFRDao->atLeastOneSurveyCategoryIsCompleteForAll = $atLeastOneSurveyCategoryIsCompleteForAll;
	
	return $myFRDao;
	

}