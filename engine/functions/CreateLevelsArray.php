<?php
	function CreateLevelsArray() {
		$result	= mysql_query("SELECT id, name, pt FROM gt8_levels ORDER BY id");
		$options	= array();
		while( ($row=mysql_fetch_assoc($result))) {
				$options[]	= $row;
		}
		return $options;
	}
?>