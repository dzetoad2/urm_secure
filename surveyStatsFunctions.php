 <?php

require_once('activitiesSupplementalFunctions.php');
 
require_once('DAO/surveyCategoryStatsRowListDAO.php'); 
use urm\urm_secure\DAO\surveyCategoryStatsRowListDAO;
 
require_once('DAO/userStatsRowListDAO.php');
use urm\urm_secure\DAO\userStatsRowListDAO;

function getTotalNumAnsweredActivities($userId){

		//table is  userid, username, totalnumactivitiesanswered.
		$r = mysql_queryCustom("select count(*) as c from surveyAnswer where userId=".$userId);
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
		$o.='<tr><td>' . $userId.'</td><td>' .$username.'</td><td>' .getTotalFacilitiesRegistered($userId). '</td><td>'. getTotalNumAnsweredActivities($userId). '</tr>';
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
	 *      
	 *      
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