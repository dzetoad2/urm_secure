<?php
 require_once('activitiesSupplementalFunctions.php');
 require_once('DAO/activityCategoryDocDAO.php');
 
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
		  $o.= '<tr class="activityCategoryDocRow clickable" id="'.$acDocDAO->id.'" ><td class="" >'.$acDocDAO->idNum.'</td><td class="bold">'.$acDocDAO->title.'</td><td>'.$rowStatus.'</td></tr>'; 
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
	$result = mysql_query("  select  id, idNum, title from activityCategory where surveyCategoryId = ".$surveyCategoryId." and activityCategoryDocId = ".$activityCategoryDocId. ";");
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
				  $o .=  '<tr class="activityCategoryRow clickable" id="'.$row['id'].'"><td   >'.$row['idNum'].'</td><td class="cell1" id="'.$row['id'].'">'.$row['id'].''.'</td><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$rowStatus.'</td></tr>';
				}
				else{
				  $o .=  '<tr class="activityCategoryRow clickable" id="'.$row['id'].'"><td   >'.$row['idNum'].'</td><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$rowStatus.'</td></tr>';
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
	$result = mysql_query("  select  distinct activityCategoryDoc.id, activityCategoryDoc.idNum, activityCategoryDoc.title
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
function getActivityCategoriesRowsHtml__oldVersion($userId, $facilityId, $isCustomFacility,  $surveyCategoryId){
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
	*     just get all the data of activityCategories for a given surveyCategoryId.
	*     Then print out rows for all those activityCategories.
	*/



	$result = mysql_query("  select  id, idNum, title from activityCategory where surveyCategoryId = ".$surveyCategoryId.";");           //check un/pw against db.
	if($result === FALSE){
		return " ";//throw error exception!
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
		if(isActivityCategoryComplete($userId,$facilityId, $row['id'], $isCustomFacility,0)){    // 0 : iscustomactivity.    row[id]: activitycategoryid.
			$rowStatus = '<img src="images/b_check.png"/>';
		}else{
			$rowStatus = '';//blank
		}
		if(defined('DEBUG')){
			$o .=  '<tr class="activityCategoryRow clickable" id="'.$row['id'].'"><td   >'.$row['idNum'].'</td><td class="cell1" id="'.$row['id'].'">'.$row['id'].''.'</td><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$rowStatus.'</td></tr>';
		}
		else{
			$o .=  '<tr class="activityCategoryRow clickable" id="'.$row['id'].'"><td   >'.$row['idNum'].'</td><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$rowStatus.'</td></tr>';
		}
	}
	return $o;

}

//todo: get rid of $isCustomActivity in the sig.
function getCustomActivityRowsHtml($userId, $facilityId, $isCustomFacility, $isCustomActivity,    $surveyCategoryId){
	/*
	 */
	$result = mysql_query("  select  id,title from customActivity where userid=".$userId." and surveyCategoryId = ".$surveyCategoryId.";");           //check un/pw against db.
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
		if(isActivityAnswered($userId,$facilityId,$row['id'], $isCustomFacility,1)){    //row[id] refers to the id of customActivity.
		  $rowStatus = '<img src="images/b_check.png"/>';
		}else{
		  $rowStatus = '';//blank
		}
		if(defined('DEBUG')){
		 $o .=  '<tr class="customActivityRow clickable" id="'.$row['id'].'""><td><img class="edit" src="images/b_edit.png"/></td><td><img class="drop" src="images/b_drop.png"/></td><td class="cell1" id="'.$row['id'].'">'.$row['id'].''.'</td><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$rowStatus .'</td></tr>';
		}else{
		 $o .=  '<tr class="customActivityRow clickable" id="'.$row['id'].'""><td><img class="edit" src="images/b_edit.png"/></td><td><img class="drop" src="images/b_drop.png"/></td><td class="nameCell" id="'.$row['title'].'">'.$row['title'].'</td><td>'.$rowStatus .'</td></tr>';
		}
		
		
	}
	return $o;
	 
}
function deleteCustomActivity($userId, $customActivityId){
	$r1 = mysql_query("  delete from customActivity where id = ".$customActivityId."");  
//	$r = mysql_affected_rows();
	$r2 = mysql_query("  delete from surveyAnswer where userId = ".$userId." and isCustomActivity=1 and activityId=".$customActivityId."");
	$o = array(); 
	$o['hasError'] = true; 
	if($r1 === TRUE && $r2 === TRUE){
		 $o['msg'] = "User Created activity successfully deleted (including its related survey answers)";
		 $o['hasError'] = false;
		 return $o;
	}else if($r1 === TRUE && $r2 === false){
		$o['msg'] = "User Created activity successfully deleted (but failure deleting its related survey answers)";
		return $o;
	}else if($r1===false && $r2 === true){
		$o['msg']= "Failed to delete user created activity but successfully deleted its related survey answers";
		return $o;
	}else{
		$o['msg']= "Failure to delete user created activity, failure to delete its related survey answers";
		return $o;
	}
}
