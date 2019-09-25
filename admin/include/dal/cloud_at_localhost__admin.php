<?php
$dalTableadmin = array();
$dalTableadmin["id"] = array("type"=>3,"varname"=>"id");
$dalTableadmin["username"] = array("type"=>200,"varname"=>"username");
$dalTableadmin["password"] = array("type"=>200,"varname"=>"password");
	$dalTableadmin["id"]["key"]=true;

$dal_info["cloud_at_localhost__admin"] = &$dalTableadmin;
?>