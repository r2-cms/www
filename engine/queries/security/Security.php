<?php
	require_once( SROOT .'engine/classes/GT8.php');
	
	class Security  extends GT8{
		
	}
	interface IScanDomain{
		public function getDomains($props = array());
		public function addDomains($props = array());
		public function updateDomains($props = array());
		public function deleteDomains($props = array());
	}
	interface IScanFilesDomain{
		public function addUser($props = array());
		public function updateUser($props = array());
		public function getUser($props = array());
		public function deleteUser($idUser);
		public function setProfile($idUser);
	}	
?>