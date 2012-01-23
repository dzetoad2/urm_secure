 <?php 
 
function getTotalNumAnsweredActivities($userId){

		//table is  userid, username, totalnumactivitiesanswered.
		$r = mysql_query("select count(*) as c from surveyAnswer where userId=".$userId);
    	$row = mysql_fetch_array($r);
		$c = $row['c'];
		return $c;
    	
}

//output: an integer string.
function getTotalFacilitiesRegistered($userId){
		$r = mysql_query("select count(*) as c from userFacility where userid=".$userId);
    	$row = mysql_fetch_array($r);
		$count = $row['c'];
		return $count;

}

function getStats1RowsHtml(){
	//loop through all users in user table.
	$o='';
	$r = mysql_query("select id,username from user");
	while($row = mysql_fetch_array($r)){
		$userId = $row['id'];
		$username = $row['username'];
		$o.='<tr><td>' . $userId.'</td><td>' .$username.'</td><td>' .getTotalFacilitiesRegistered($userId). '</td><td>'. getTotalNumAnsweredActivities($userId). '</tr>';
	}
	return $o;

}


 																	//$row = mysql_fetch_assoc($r);  //what does this do? was in sessionstatefunctions hmm.
    	