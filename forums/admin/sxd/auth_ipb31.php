<?php
// Sypex Dumper 2 authorization file for IPBoard 
$path = file_exists('../conf_global.php')?'../conf_global.php':'../../conf_global.php'; // path from ipb/sxd/ to ipb/conf_global.php 
$login = '$5$rounds=5000$saltsaltforsalt$nvCgPZ0Pbn3yx/pduwmoiQcm6SeR7CbUk0YOL88/iLB';
$password = '$5$rounds=5000$saltsaltforsalt$A50UjL7b0Si.xDbwpPdcvxZnq2RkpKMjTFOSLbI7GF5'; 

include($path);
if(!empty($user)) {
	if($this->connect($INFO['sql_host'], '', $INFO['sql_user'], $INFO['sql_pass'])){
		// Checking user
		$auth = crypt($user, '$5$rounds=5000$saltsaltforsalt$') == $login && crypt($pass, '$5$rounds=5000$saltsaltforsalt$') == $password;
		$this->CFG['my_db']   = $INFO['sql_database'];
	}
}

?>