<?php
namespace urm\urm_secure\DAO;

require_once('surveyCategoryStatsRowDAO.php');

use urm\urm_secure\DAO\surveyCategoryStatsRowDAO;

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