<?php
	
 require_once('urm_secure/validationFunctions.php');
 require_once('urm_secure/activityFunctions.php');
 
 class createActivityController{
 	
 	public static function errorCheck($_POST, $afDao){

 		
 	$_fid = $_POST['fid'];
 													//$_aid = $_POST['aid'];
 	$_is_cf = $_POST['is_cf'];
 	$_is_ca = $_POST['is_ca'];
 	if($_is_ca) $customActivityId = 'dummyValue';

 	(isset($_POST['isPerformedAdult'])) ? $afDao->isPerformedAdult = trim($_POST['isPerformedAdult']) : $afDao->isPerformedAdult = "na";
	(isset($_POST['isPerformedPediatric'])) ? $afDao->isPerformedPediatric = trim($_POST['isPerformedPediatric']) : $afDao->isPerformedPediatric = "na";
	(isset($_POST['isPerformedNatal'])) ? $afDao->isPerformedNatal = trim($_POST['isPerformedNatal']) : $afDao->isPerformedNatal = "na";
	(isset($_POST['hasTimestandardAdult'])) ? $afDao->hasTimestandardAdult = trim($_POST['hasTimestandardAdult']) : $afDao->hasTimestandardAdult = "na";
	(isset($_POST['hasTimestandardPediatric'])) ? $afDao->hasTimestandardPediatric = trim($_POST['hasTimestandardPediatric']) : $afDao->hasTimestandardPediatric="na";
	(isset($_POST['hasTimestandardNatal'])) ? $afDao->hasTimestandardNatal = trim($_POST['hasTimestandardNatal']) : $afDao->hasTimestandardNatal="na";
	(isset($_POST['durationAdult'])) ? $afDao->durationAdult = trim($_POST['durationAdult']) : $afDao->durationAdult = -1;    // && isPosInt($_POST['durationAdult'])   //... not sure if need the isposint check here.
	(isset($_POST['durationPediatric'])) ? $afDao->durationPediatric = trim($_POST['durationPediatric']) : $afDao->durationPediatric = -1; //&& isPosInt($_POST['durationPediatric'])
	(isset($_POST['durationNatal'])) ? $afDao->durationNatal = trim($_POST['durationNatal']) : $afDao->durationNatal = -1; // && isPosInt($_POST['durationNatal'])
	(isset($_POST['volumeAdult']) ) ? $afDao->volumeAdult =  trim($_POST['volumeAdult']) : $afDao->volumeAdult = -1; //&& isPosInt($_POST['volumeAdult'])
	(isset($_POST['volumePediatric']) ) ? $afDao->volumePediatric =  trim($_POST['volumePediatric']) : $afDao->volumePediatric = -1; //&& isPosInt($_POST['volumePediatric'])
	(isset($_POST['volumeNatal']) ) ? $afDao->volumeNatal =  trim($_POST['volumeNatal']) : $afDao->volumeNatal = -1; //&& isPosInt($_POST['volumeNatal'])
	(isset($_POST['methodologyAdult'])) ? $afDao->methodologyAdult = trim($_POST['methodologyAdult']) : $afDao->methodologyAdult = "na";
	(isset($_POST['methodologyPediatric'])) ? $afDao->methodologyPediatric = trim($_POST['methodologyPediatric']) : $afDao->methodologyPediatric = "na";
	(isset($_POST['methodologyNatal'])) ? $afDao->methodologyNatal = trim($_POST['methodologyNatal']) : $afDao->methodologyNatal="na";
 	
	
 		//=================ADULT BLOCK =====================
  
	
 if($afDao->isPerformedAdult=="na"){
 	$afDao->errorLabel .= "Error: Adult: Do you perform this procedure? Please choose yes or no<br/>";
 	$afDao->isPerformedAdultErrorImg = $afDao->errorImg;
 }else if($afDao->isPerformedAdult=="no"){
	//done :  quit this code block.
	$afDao->hasTimestandardAdult="na"; //forced
	$afDao->durationAdult=-1; //forced
	$afDao->volumeAdult=-1; //forced
	$afDao->methodologyAdult="na";//forced
 }else{ //isperf is yes.
 	if ($afDao->hasTimestandardAdult=="na"){
 		$afDao->errorLabel .= "Error: Adult: Do you have a time standard for this procedure? Please choose yes or no<br/>";
 		$afDao->hasTimestandardAdultErrorImg = $afDao->errorImg; 
 	}else if($afDao->hasTimestandardAdult=="no"){
 		//done
 		$afDao->durationAdult=-1; //forced
		$afDao->volumeAdult=-1; //forced
		$afDao->methodologyAdult="na";//forced
 	}else{ //hastimestandard == yes
 		
 		if(!isPosInt($afDao->durationAdult)){
 			//durationadult is invalid, error, done.
 			$afDao->errorLabel.="Error: Adult:  Duration must be a positive integer<br/>";
 			$afDao->durationAdultErrorImg = $afDao->errorImg;
 		}
 		if($afDao->durationAdult>999){
 				$afDao->errorLabel.="Error: Adult: Duration must be less than 1000<br/>";
 				$afDao->durationAdultErrorImg = $afDao->errorImg;
 		} 
 		if(!isPosInt($afDao->volumeAdult)   || $afDao->volumeAdult > 20000    ){
 				$afDao->errorLabel.="Error: Adult: Volume must be a positive integer between 0 and 20000 inclusive<br/>";
 				$afDao->volumeAdultErrorImg = $afDao->errorImg;
 		}else{
 		}
 		if($afDao->methodologyAdult=="na"){
 				$afDao->errorLabel.="Error: Adult: Please choose a methodology<br/>";
 				$afDao->methodologyAdultErrorImg = $afDao->errorImg;
 		}
 	}
 }
 
  
 //========================PEDIATRIC BLOCK=======================
 
 $afDao->hasTimestandardPediatricErrorImg='';
 if($afDao->isPerformedPediatric=="na"){
 	$afDao->errorLabel .= "Error: Pediatrics: Do you perform this procedure? Please choose yes or no<br/>";
 	$afDao->isPerformedPediatricErrorImg = $afDao->errorImg; 
 }else if($afDao->isPerformedPediatric=="no"){
	//done 	
	$afDao->hasTimestandardPediatric="na";//forced
	$afDao->durationPediatric=-1;//forced
	$afDao->volumePediatric=-1;//forced
	$afDao->methodologyPediatric="na";//forced
 }else{ //isperf is yes.
 	if ($afDao->hasTimestandardPediatric=="na"){
 		$afDao->errorLabel .= "Error: Pediatrics: Do you have a time standard for this procedure? Please choose yes or no<br/>";
 		$afDao->hasTimestandardPediatricErrorImg = $afDao->errorImg; 
 	}else if($afDao->hasTimestandardPediatric=="no"){
 		//done
 		$afDao->durationPediatric=-1;//forced
		$afDao->volumePediatric=-1;//forced
		$afDao->methodologyPediatric="na";//forced
 	}else{//hastimestandard == yes
 		if(!isPosInt($afDao->durationPediatric)){
 			//durationped is invalid, error, done.
 			$afDao->errorLabel .= "Error: Pediatrics:  Duration must be a positive integer<br/>";
 			$afDao->durationPediatricErrorImg = $afDao->errorImg;
 		}
 		if($afDao->durationPediatric > 999){
 				$afDao->errorLabel.="Error: Pediatrics: Duration must be less than 1000<br/>";
 				$afDao->durationPediatricErrorImg = $afDao->errorImg;
 		}
 		if(!isPosInt($afDao->volumePediatric) || $afDao->volumePediatric > 20000     ){
 			$afDao->errorLabel.="Error: Pediatrics: Volume must be a positive integer between 0 and 20000 inclusive<br/>";
 			$afDao->volumePediatricErrorImg = $afDao->errorImg;
 		}else{
 		  	
 		}
 	 
 		if($afDao->methodologyPediatric=="na"){
 				$afDao->errorLabel.="Error: Pediatrics: Please choose a methodology<br/>";
 				$afDao->methodologyPediatricErrorImg = $afDao->errorImg;
 		}
 	}
 
 }
 //=======================NATAL BLOCK======================
 
$afDao->isPerformedNatalErrorImg=''; 
$afDao->hasTimestandardNatalErrorImg='';
if($afDao->isPerformedNatal=="na"){
 	$afDao->errorLabel .= "Error: NeoNatal: Do you perform this procedure? Please choose yes or no<br/>";
 	$afDao->isPerformedNatalErrorImg = $afDao->errorImg; 
 }else if($afDao->isPerformedNatal=="no"){
	//done
	$afDao->hasTimestandardNatal="na";//forced
 	$afDao->durationNatal=-1;//forced
	$afDao->volumeNatal=-1;//forced
	$afDao->methodologyNatal="na";//forced
 	 	
 }else{ //isperf is yes.
 	if ($afDao->hasTimestandardNatal=="na"){
 		$afDao->errorLabel .= "Error: NeoNatal: Do you have a time standard for this procedure? Please choose yes or no<br/>";
 		$afDao->hasTimestandardNatalErrorImg=$afDao->errorImg; 
 	}else if($afDao->hasTimestandardNatal=="no"){
 		//done
 		$afDao->durationNatal=-1;//forced
		$afDao->volumeNatal=-1;//forced
		$afDao->methodologyNatal="na";//forced
 	}else{//hastimestandard == yes
 		if(!isPosInt($afDao->durationNatal)){
 			//durationadult is invalid, error, done.
 			$afDao->errorLabel.="Error: NeoNatal:  Duration must be a positive integer<br/>";
 			$afDao->durationNatalErrorImg=$afDao->errorImg;
 		}
 		if($afDao->durationNatal>999){
 				$afDao->errorLabel.="Error: NeoNatal: Duration must be less than 1000<br/>";
 				$afDao->durationNatalErrorImg=$afDao->errorImg;
 		}
 		if(   !isPosInt($afDao->volumeNatal)   || $afDao->volumeNatal > 20000  ){
 			$afDao->errorLabel.="Error: NeoNatal: Volume must be a positive integer between 0 and 20000 inclusive<br/>";
 			$afDao->volumeNatalErrorImg=$afDao->errorImg;
 		}else{

 		}
 		if($afDao->methodologyNatal=="na"){
 				$afDao->errorLabel.="Error: NeoNatal: Please choose a methodology<br/>";
 				$afDao->methodologyNatalErrorImg=$afDao->errorImg;
 		}
 	}
 }
 	} // end public stat func errorcheck
 	//for now only for custom activities.
 	public static function submitSurveyAnswer($userId, $aid, $fid, $is_cf,$is_ca, $afDao){
 		  $_aid = $aid;
		  $_fid = $fid;
		  $_is_cf = $is_cf;
		  $_is_ca = $is_ca;
 		  if(trim($_fid)=='') throwMyExc('submitsurveyanswer(): fid is blank');
 		  if(trim($_aid)=='') throwMyExc('submitsurveyanswer(): aid is blank');
 		  if(trim($_is_cf)=='') throwMyExc('submitsurveyanswer(): is_cf is blank');
 		  
 		  $r = submitSurveyAnswer($userId, $_fid,$_aid,$_is_cf,$_is_ca,$afDao->isPerformedAdult,$afDao->isPerformedPediatric,$afDao->isPerformedNatal,
 					 $afDao->hasTimestandardAdult,$afDao->hasTimestandardPediatric,$afDao->hasTimestandardNatal,$afDao->durationAdult,$afDao->durationPediatric,
 					 $afDao->durationNatal,$afDao->volumeAdult,$afDao->volumePediatric,$afDao->volumeNatal,$afDao->methodologyAdult,
 					 $afDao->methodologyPediatric,$afDao->methodologyNatal);
 	}//end method
 }//end class