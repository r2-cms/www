<?php
	require_once( SROOT .'engine/classes/GT8.php');
	
	class Security  extends GT8{
		
	}
	interface IScanDomain{
		public function getDomains($props = array());
		public function addDomains($props = array());
		public function updateDomains($id = 0, $props = array());
		public function deleteDomains($id = 0, $props = array());
	}
	interface IScanFilesDomain{
		public function getFilesDomains($props = array());
		public function addFilesDomains($props = array());
		public function updateFilesDomains($id = 0, $props = array());
		public function deleteFilesDomains($id = 0, $props = array());
	}	
?>