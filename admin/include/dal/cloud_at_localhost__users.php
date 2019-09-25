<?php
$dalTableusers = array();
$dalTableusers["userId"] = array("type"=>3,"varname"=>"userId");
$dalTableusers["userName"] = array("type"=>200,"varname"=>"userName");
$dalTableusers["userEmail"] = array("type"=>200,"varname"=>"userEmail");
$dalTableusers["userPass"] = array("type"=>200,"varname"=>"userPass");
	$dalTableusers["userId"]["key"]=true;

$dal_info["cloud_at_localhost__users"] = &$dalTableusers;
?>