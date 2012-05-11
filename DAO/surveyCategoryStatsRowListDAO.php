<?php
namespace urm\urm_secure\DAO;


/*
 * gets the full set of surveycategory rows. 
 */
class surveyCategoryStatsRowListDAO{
	public
	  $list;
	  
	function __construct(){
		$this->list = array();
		
		$q1 = "select id, title from surveyCategory where 1";
		$r1 = mysql_queryCustom($q1);
		while($row = mysql_fetch_array($r1)){
			$id = $row['id'];
			$title = $row['title'];
			//echo '----------'.$id.', '.$title;
			$r = new surveyCategoryStatsRowDAO($id,$title);              //$r->toRowHtml();
			$this->list[] = $r;
		}
	}
	function toRowsHtml(){
		$o = '';
		foreach($this->list as $r){
		   $o .= $r->toRowHtml();
		}
		//		$o .= 'size: '.count($this->list);
		return $o;
	}
}



class surveyCategoryStatsRowDAO {
	
	public 
	 $id, $title, $activityTotalCount;
	 
	function __construct($in_id, $in_title){
		$this->id = $in_id;
		$this->title = $in_title;
		$this->activityTotalCount = $this->getActivityTotalCount();
	}
	function getActivityTotalCount(){
		$r = mysql_queryCustom(
		 "select count(*) as c 
		   from activity join activityCategory
		   on activity.activityCategoryId = activityCategory.id
		   join surveyCategory
		   on activityCategory.surveyCategoryId = surveyCategory.id
		 where  surveyCategory.id = ".$this->id.";"
		
		);
    	$row = mysql_fetch_array($r);
		$c = $row['c'];
		return $c;
		
	}
	function toRowHtml(){
		$o= '<tr><td>'.$this->id.'</td><td>'.$this->title.'</td></tr>';
		return $o;
	}
	
	
}
