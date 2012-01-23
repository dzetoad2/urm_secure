<?php
namespace urm\urm_secure\DAO;

class activityCategoryDocDAO {
	
	public 
	 $id,$idNum,$title,$doc;     //field data
	
	public function populateFromArr($row){
		//get fields:   id, idNum, title, activityCategoryDocId
		if(!isset($row['id'])  ||  !isset($row['idNum'])  ||  !isset($row['title'])){
			$em = 'activityCategoryDocDAO->populateFromArr(): A col is not set : among id, or idnum or title';
			throwMyExc($em);
		}
		$this->id = $row['id'];
		$this->idNum = $row['idNum'];
		$this->title = $row['title'];
		if(isset($row['doc'])) $this->doc = $row['doc']; //optional, only for activities.php
	}
	
}