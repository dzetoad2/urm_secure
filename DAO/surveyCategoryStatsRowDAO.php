<?php
namespace urm\urm_secure\DAO;

 
class surveyCategoryStatsRowDAO {
	
	public 
	 $id, $title;
	 
	function __construct($in_id, $in_title){
		$this->id = $in_id;
		$this->title = $in_title;
	}
	
	function toRowHtml(){
		$o= '<tr><td>'.$this->id.'</td><td>'.$this->title.'</td></tr>';
		return $o;
	}
	
	
}

 

