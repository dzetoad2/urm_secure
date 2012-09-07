<?php
namespace urm\urm_secure\DAO;

require_once 'urm_secure/validationFunctions.php';

 
class customActivityRowListDAO {
	
	public 
	 $list;      //field data

	 /*  select * from customActivity inner join surveyAnswer
      on customActivity.id = surveyAnswer.activityId
      where 
      surveyAnswer.isCustomActivity = 1
      and
      surveyAnswer.userId = customActivity.userId
	  * 
	  * 
	  * 
	  */
	 
	function __construct($surveyCategoryId){
		if(!isPosInt($surveyCategoryId)){
			die('surv cat id  is not pos int.');
		}
		$this->list = array();
		
		$q1 = "select customActivity.surveyCategoryId as ca_surveyCategoryId, surveyCategory.title as surveyCategoryTitle,   
		surveyAnswer.isCustomActivity as sa_isCustomActivity, 
		customActivity.is_cf, customActivity.title as title, customActivity.description as description,
        surveyAnswer.isPerformedAdult as isPerformedAdult, isPerformedPediatric, isPerformedNatal, 
        hasTimeStandardAdult, hasTimeStandardPediatric, hasTimeStandardNatal, 
        durationAdult, durationPediatric, durationNatal,
        volumeAdult, volumePediatric, volumeNatal,
        methodologyAdult, methodologyPediatric, methodologyNatal 
        
        from 
		surveyCategory join customActivity
		on surveyCategory.id = customActivity.surveyCategoryId
		
        join surveyAnswer
        on customActivity.id = surveyAnswer.activityId
        
        where

        surveyAnswer.isCustomActivity = 1 
                and 
        surveyAnswer.userId = customActivity.userId
        and 
        customActivity.surveyCategoryId = ".$surveyCategoryId;
		
        
        
														//           and customActivity.surveyCategoryId = 1";
		
		$r1 = mysql_queryCustom($q1);
		if($r1===false){
			die('query error: '.$q1 );
		}
		while($row = mysql_fetch_array($r1)){
			$title = $row['title'];
			$descr = $row['description'];
			$survCatTitle = $row['surveyCategoryTitle'];
			$isPerformedAdult = 	$row['isPerformedAdult'];
			$isPerformedPediatric = $row['isPerformedPediatric'];
			$isPerformedNatal =    	$row['isPerformedNatal'];
			$hasTimeStandardAdult = 	$row['hasTimeStandardAdult'];
			$hasTimeStandardPediatric = $row['hasTimeStandardPediatric'];
			$hasTimeStandardNatal = 	$row['hasTimeStandardNatal'];
			$durationAdult = 		$row['durationAdult'];
			$durationPediatric = 	$row['durationPediatric'];
			$durationNatal = 		$row['durationNatal'];
			$volumeAdult = 			$row['volumeAdult'];
			$volumePediatric = 		$row['volumePediatric'];
			$volumeNatal = 			$row['volumeNatal'];
			$methodologyAdult = 	$row['methodologyAdult'];
			$methodologyPediatric = $row['methodologyPediatric'];
			$methodologyNatal = 	$row['methodologyNatal'];
			//echo '----------'.$id.', '.$title;
			$r =  new customActivityRowDAO($title, $descr,$survCatTitle, 
	  			$isPerformedAdult, $isPerformedPediatric, $isPerformedNatal,
	  			$hasTimeStandardAdult, $hasTimeStandardPediatric, $hasTimeStandardNatal,
	  			$durationAdult, $durationPediatric, $durationNatal,
	  			$volumeAdult, $volumePediatric, $volumeNatal,
	  			$methodologyAdult, $methodologyPediatric, $methodologyNatal);
			$this->list[] = $r;
		}
	}
	
	
	function toRowsHtml(){
		$o = '';
		foreach($this->list as $r){
		   $o .= $r->toRowHtml();
		}
		return $o;
	}
	 
}


class customActivityRowDAO {
	
	public 
	  $title, $descr, $survCatTitle, 
	  $isPerformedAdult, $isPerformedPediatric, $isPerformedNatal,
	  $hasTimeStandardAdult, $hasTimeStandardPediatric, $hasTimeStandardNatal,
	  $durationAdult, $durationPediatric, $durationNatal,
	  $volumeAdult, $volumePediatric, $volumeNatal,
	  $methodologyAdult, $methodologyPediatric, $methodologyNatal;
	 
	function __construct($title, $descr,  $survCatTitle,
	  $isPerformedAdult, $isPerformedPediatric, $isPerformedNatal,
	  $hasTimeStandardAdult, $hasTimeStandardPediatric, $hasTimeStandardNatal,
	  $durationAdult, $durationPediatric, $durationNatal,
	  $volumeAdult, $volumePediatric, $volumeNatal,
	  $methodologyAdult, $methodologyPediatric, $methodologyNatal){
	  	
	  $this->title = $title;
	  $this->descr = $descr;
	  $this->survCatTitle = $survCatTitle;
	  $this->isPerformedAdult =     $isPerformedAdult;
	  $this->isPerformedPediatric = $isPerformedPediatric;
	  $this->isPerformedNatal =     $isPerformedNatal;
	  $this->hasTimeStandardAdult =     $hasTimeStandardAdult;
	  $this->hasTimeStandardPediatric = $hasTimeStandardPediatric;
	  $this->hasTimeStandardNatal =     $hasTimeStandardNatal;
	  $this->durationAdult = $durationAdult;     //  $this->durationAdult!="na"?    :  $this->durationAdult = "";
	  $this->durationPediatric = $durationPediatric;
	  $this->durationNatal = 	 $durationNatal;
	  $this->volumeAdult = 		$volumeAdult;
	  $this->volumePediatric =  $volumePediatric;
	  $this->volumeNatal = 		$volumeNatal;
	  $this->methodologyAdult = 	$methodologyAdult;
	  $this->methodologyPediatric = $methodologyPediatric;
	  $this->methodologyNatal = 	$methodologyNatal;
	}
	 
	
	function toRowHtml(){
		$o = '<tr>';
		$o .= '<td >'.$this->survCatTitle.'</td>';
		$o .= '<td>'.$this->title.'</td>';
		$o .= '<td>'.$this->descr.'</td>';
		$o .= '<td>'.$this->isPerformedAdult.'</td>';
		$o .= '<td>'.$this->isPerformedPediatric.'</td>';
		$o .= '<td>'.$this->isPerformedNatal.'</td>';
		$o .= '<td>'.$this->hasTimeStandardAdult.'</td>';
		$o .= '<td>'.$this->hasTimeStandardPediatric.'</td>';
		$o .= '<td>'.$this->hasTimeStandardNatal.'</td>';
		$o .= '<td>'.$this->durationAdult.'</td>';
		$o .= '<td>'.$this->durationPediatric.'</td>';
		$o .= '<td>'.$this->durationNatal.'</td>';
		$o .= '<td>'.$this->volumeAdult.'</td>';
		$o .= '<td>'.$this->volumePediatric.'</td>';
		$o .= '<td>'.$this->volumeNatal.'</td>';
		$o .= '<td>'.$this->methodologyAdult.'</td>';
		$o .= '<td>'.$this->methodologyPediatric.'</td>';
		$o .= '<td>'.$this->methodologyNatal.'</td>';
		$o .= '</tr>';
		return $o;
	}
	 
}

/*
 * function makeRow($title, $descr, $isPerformedAdult){
		$row = array();
		$row['title'] = $title;
		$row['descr'] = $descr;
		$row['isPerformedAdult'] = $isPerformedAdult;
		return $row;
	}
 */