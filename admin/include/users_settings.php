<?php
require_once(getabspath("classes/cipherer.php"));




$tdatausers = array();	
	$tdatausers[".truncateText"] = true;
	$tdatausers[".NumberOfChars"] = 80; 
	$tdatausers[".ShortName"] = "users";
	$tdatausers[".OwnerID"] = "";
	$tdatausers[".OriginalTable"] = "users";

//	field labels
$fieldLabelsusers = array();
$fieldToolTipsusers = array();
$pageTitlesusers = array();

if(mlang_getcurrentlang()=="English")
{
	$fieldLabelsusers["English"] = array();
	$fieldToolTipsusers["English"] = array();
	$pageTitlesusers["English"] = array();
	$fieldLabelsusers["English"]["userId"] = "User Id";
	$fieldToolTipsusers["English"]["userId"] = "";
	$fieldLabelsusers["English"]["userName"] = "User Name";
	$fieldToolTipsusers["English"]["userName"] = "";
	$fieldLabelsusers["English"]["userEmail"] = "User Email";
	$fieldToolTipsusers["English"]["userEmail"] = "";
	$fieldLabelsusers["English"]["userPass"] = "User Pass";
	$fieldToolTipsusers["English"]["userPass"] = "";
	if (count($fieldToolTipsusers["English"]))
		$tdatausers[".isUseToolTips"] = true;
}
if(mlang_getcurrentlang()=="")
{
	$fieldLabelsusers[""] = array();
	$fieldToolTipsusers[""] = array();
	$pageTitlesusers[""] = array();
	$fieldLabelsusers[""]["userId"] = "User Id";
	$fieldToolTipsusers[""]["userId"] = "";
	if (count($fieldToolTipsusers[""]))
		$tdatausers[".isUseToolTips"] = true;
}
	
	
	$tdatausers[".NCSearch"] = true;



$tdatausers[".shortTableName"] = "users";
$tdatausers[".nSecOptions"] = 0;
$tdatausers[".recsPerRowList"] = 1;
$tdatausers[".mainTableOwnerID"] = "";
$tdatausers[".moveNext"] = 1;
$tdatausers[".nType"] = 0;

$tdatausers[".strOriginalTableName"] = "users";




$tdatausers[".showAddInPopup"] = false;

$tdatausers[".showEditInPopup"] = false;

$tdatausers[".showViewInPopup"] = false;

//page's base css files names
$popupPagesLayoutNames = array();
$tdatausers[".popupPagesLayoutNames"] = $popupPagesLayoutNames;


$tdatausers[".fieldsForRegister"] = array();

$tdatausers[".listAjax"] = false;

	$tdatausers[".audit"] = false;

	$tdatausers[".locking"] = false;

$tdatausers[".edit"] = true;

$tdatausers[".list"] = true;





$tdatausers[".delete"] = true;

$tdatausers[".showSimpleSearchOptions"] = false;

// search Saving settings
$tdatausers[".searchSaving"] = false;
//

if (isMobile()){
}
else 
	$tdatausers[".showSearchPanel"] = false;

if (isMobile())
	$tdatausers[".isUseAjaxSuggest"] = false;
else 
	$tdatausers[".isUseAjaxSuggest"] = true;

$tdatausers[".rowHighlite"] = true;



$tdatausers[".addPageEvents"] = false;

// use timepicker for search panel
$tdatausers[".isUseTimeForSearch"] = false;





$tdatausers[".allSearchFields"] = array();
$tdatausers[".filterFields"] = array();
$tdatausers[".requiredSearchFields"] = array();



$tdatausers[".googleLikeFields"] = array();
$tdatausers[".googleLikeFields"][] = "userId";
$tdatausers[".googleLikeFields"][] = "userName";
$tdatausers[".googleLikeFields"][] = "userEmail";
$tdatausers[".googleLikeFields"][] = "userPass";



$tdatausers[".tableType"] = "list";

$tdatausers[".printerPageOrientation"] = 0;
$tdatausers[".nPrinterPageScale"] = 100;

$tdatausers[".nPrinterSplitRecords"] = 40;

$tdatausers[".nPrinterPDFSplitRecords"] = 40;





	





// view page pdf

// print page pdf


$tdatausers[".pageSize"] = 20;

$tdatausers[".warnLeavingPages"] = true;



$tstrOrderBy = "";
if(strlen($tstrOrderBy) && strtolower(substr($tstrOrderBy,0,8))!="order by")
	$tstrOrderBy = "order by ".$tstrOrderBy;
$tdatausers[".strOrderBy"] = $tstrOrderBy;

$tdatausers[".orderindexes"] = array();

$tdatausers[".sqlHead"] = "SELECT userId,  	userName,  	userEmail,  	userPass";
$tdatausers[".sqlFrom"] = "FROM users";
$tdatausers[".sqlWhereExpr"] = "";
$tdatausers[".sqlTail"] = "";




//fill array of records per page for list and report without group fields
$arrRPP = array();
$arrRPP[] = 10;
$arrRPP[] = 20;
$arrRPP[] = 30;
$arrRPP[] = 50;
$arrRPP[] = 100;
$arrRPP[] = 500;
$arrRPP[] = -1;
$tdatausers[".arrRecsPerPage"] = $arrRPP;

//fill array of groups per page for report with group fields
$arrGPP = array();
$arrGPP[] = 1;
$arrGPP[] = 3;
$arrGPP[] = 5;
$arrGPP[] = 10;
$arrGPP[] = 50;
$arrGPP[] = 100;
$arrGPP[] = -1;
$tdatausers[".arrGroupsPerPage"] = $arrGPP;

$tdatausers[".highlightSearchResults"] = true;

$tableKeysusers = array();
$tableKeysusers[] = "userId";
$tdatausers[".Keys"] = $tableKeysusers;

$tdatausers[".listFields"] = array();
$tdatausers[".listFields"][] = "userId";
$tdatausers[".listFields"][] = "userName";
$tdatausers[".listFields"][] = "userEmail";

$tdatausers[".hideMobileList"] = array();


$tdatausers[".viewFields"] = array();

$tdatausers[".addFields"] = array();
$tdatausers[".addFields"][] = "userName";
$tdatausers[".addFields"][] = "userEmail";
$tdatausers[".addFields"][] = "userPass";

$tdatausers[".inlineAddFields"] = array();

$tdatausers[".editFields"] = array();
$tdatausers[".editFields"][] = "userEmail";
$tdatausers[".editFields"][] = "userName";

$tdatausers[".inlineEditFields"] = array();

$tdatausers[".exportFields"] = array();

$tdatausers[".importFields"] = array();

$tdatausers[".printFields"] = array();

//	userId
//	Custom field settings
	$fdata = array();
	$fdata["Index"] = 1;
	$fdata["strName"] = "userId";
	$fdata["GoodName"] = "userId";
	$fdata["ownerTable"] = "users";
	$fdata["Label"] = GetFieldLabel("users","userId"); 
	$fdata["FieldType"] = 3;
	
		
		$fdata["AutoInc"] = true;
	
		
				
		$fdata["bListPage"] = true; 
	
		
		
		
		
		
		
		
		
		$fdata["strField"] = "userId"; 
	
		$fdata["isSQLExpression"] = true;
	$fdata["FullName"] = "userId";
	
		
		
				$fdata["FieldPermissions"] = true;
	
				$fdata["UploadFolder"] = "files";
		
//  Begin View Formats
	$fdata["ViewFormats"] = array();
	
	$vdata = array("ViewFormat" => "");
	
		
		
		
		
		
		
		
		
		
		
		
		$vdata["NeedEncode"] = true;
	
	$fdata["ViewFormats"]["view"] = $vdata;
//  End View Formats

//	Begin Edit Formats 	
	$fdata["EditFormats"] = array();
	
	$edata = array("EditFormat" => "Text field");
	
			
	
	


		$edata["IsRequired"] = true; 
	
		
		
		
			$edata["acceptFileTypes"] = ".+$";
	
		$edata["maxNumberOfFiles"] = 1;
	
		
		
		
		
			$edata["HTML5InuptType"] = "number";
	
		$edata["EditParams"] = "";
			
		$edata["controlWidth"] = 200;
	
//	Begin validation
	$edata["validateAs"] = array();
	$edata["validateAs"]["basicValidate"] = array();
	$edata["validateAs"]["customMessages"] = array();
				$edata["validateAs"]["basicValidate"][] = getJsValidatorName("Number");	
						$edata["validateAs"]["basicValidate"][] = "IsRequired";
			
		
	//	End validation
	
		
				
		
	
		
	$fdata["EditFormats"]["edit"] = $edata;
//	End Edit Formats
	
	
	$fdata["isSeparate"] = false;
	
	
	
	

	

	
	$tdatausers["userId"] = $fdata;
//	userName
//	Custom field settings
	$fdata = array();
	$fdata["Index"] = 2;
	$fdata["strName"] = "userName";
	$fdata["GoodName"] = "userName";
	$fdata["ownerTable"] = "users";
	$fdata["Label"] = GetFieldLabel("users","userName"); 
	$fdata["FieldType"] = 200;
	
		
		
		
				
		$fdata["bListPage"] = true; 
	
		$fdata["bAddPage"] = true; 
	
		
		$fdata["bEditPage"] = true; 
	
		
		
		
		
		
		$fdata["strField"] = "userName"; 
	
		$fdata["isSQLExpression"] = true;
	$fdata["FullName"] = "userName";
	
		
		
				$fdata["FieldPermissions"] = true;
	
				$fdata["UploadFolder"] = "files";
		
//  Begin View Formats
	$fdata["ViewFormats"] = array();
	
	$vdata = array("ViewFormat" => "");
	
		
		
		
		
		
		
		
		
		
		
		
		$vdata["NeedEncode"] = true;
	
	$fdata["ViewFormats"]["view"] = $vdata;
//  End View Formats

//	Begin Edit Formats 	
	$fdata["EditFormats"] = array();
	
	$edata = array("EditFormat" => "Text field");
	
			
	
	


		
		
		
		
			$edata["acceptFileTypes"] = ".+$";
	
		$edata["maxNumberOfFiles"] = 1;
	
		
		
		
		
			$edata["HTML5InuptType"] = "text";
	
		$edata["EditParams"] = "";
			$edata["EditParams"].= " maxlength=30";
	
		$edata["controlWidth"] = 200;
	
//	Begin validation
	$edata["validateAs"] = array();
	$edata["validateAs"]["basicValidate"] = array();
	$edata["validateAs"]["customMessages"] = array();
		
		
	//	End validation
	
		
				
		
	
		
	$fdata["EditFormats"]["edit"] = $edata;
//	End Edit Formats
	
	
	$fdata["isSeparate"] = false;
	
	
	
	

	

	
	$tdatausers["userName"] = $fdata;
//	userEmail
//	Custom field settings
	$fdata = array();
	$fdata["Index"] = 3;
	$fdata["strName"] = "userEmail";
	$fdata["GoodName"] = "userEmail";
	$fdata["ownerTable"] = "users";
	$fdata["Label"] = GetFieldLabel("users","userEmail"); 
	$fdata["FieldType"] = 200;
	
		
		
		
				
		$fdata["bListPage"] = true; 
	
		$fdata["bAddPage"] = true; 
	
		
		$fdata["bEditPage"] = true; 
	
		
		
		
		
		
		$fdata["strField"] = "userEmail"; 
	
		$fdata["isSQLExpression"] = true;
	$fdata["FullName"] = "userEmail";
	
		
		
				$fdata["FieldPermissions"] = true;
	
				$fdata["UploadFolder"] = "files";
		
//  Begin View Formats
	$fdata["ViewFormats"] = array();
	
	$vdata = array("ViewFormat" => "");
	
		
		
		
		
		
		
		
		
		
		
		
		$vdata["NeedEncode"] = true;
	
	$fdata["ViewFormats"]["view"] = $vdata;
//  End View Formats

//	Begin Edit Formats 	
	$fdata["EditFormats"] = array();
	
	$edata = array("EditFormat" => "Text field");
	
			
	
	


		
		
		
		
			$edata["acceptFileTypes"] = ".+$";
	
		$edata["maxNumberOfFiles"] = 1;
	
		
		
		
		
			$edata["HTML5InuptType"] = "text";
	
		$edata["EditParams"] = "";
			$edata["EditParams"].= " maxlength=60";
	
		$edata["controlWidth"] = 200;
	
//	Begin validation
	$edata["validateAs"] = array();
	$edata["validateAs"]["basicValidate"] = array();
	$edata["validateAs"]["customMessages"] = array();
		
		
	//	End validation
	
		
				
		
	
		
	$fdata["EditFormats"]["edit"] = $edata;
//	End Edit Formats
	
	
	$fdata["isSeparate"] = false;
	
	
	
	

	

	
	$tdatausers["userEmail"] = $fdata;
//	userPass
//	Custom field settings
	$fdata = array();
	$fdata["Index"] = 4;
	$fdata["strName"] = "userPass";
	$fdata["GoodName"] = "userPass";
	$fdata["ownerTable"] = "users";
	$fdata["Label"] = GetFieldLabel("users","userPass"); 
	$fdata["FieldType"] = 200;
	
		
		
		
				
		
		$fdata["bAddPage"] = true; 
	
		
		
		
		
		
		
		
		$fdata["strField"] = "userPass"; 
	
		$fdata["isSQLExpression"] = true;
	$fdata["FullName"] = "userPass";
	
		
		
				
				$fdata["UploadFolder"] = "files";
		
//  Begin View Formats
	$fdata["ViewFormats"] = array();
	
	$vdata = array("ViewFormat" => "");
	
		
		
		
		
		
		
		
		
		
		
		
		$vdata["NeedEncode"] = true;
	
	$fdata["ViewFormats"]["view"] = $vdata;
//  End View Formats

//	Begin Edit Formats 	
	$fdata["EditFormats"] = array();
	
	$edata = array("EditFormat" => "Text field");
	
			
	
	


		
		
		
		
			$edata["acceptFileTypes"] = ".+$";
	
		$edata["maxNumberOfFiles"] = 1;
	
		
		
		
		
			$edata["HTML5InuptType"] = "text";
	
		$edata["EditParams"] = "";
			$edata["EditParams"].= " maxlength=255";
	
		$edata["controlWidth"] = 200;
	
//	Begin validation
	$edata["validateAs"] = array();
	$edata["validateAs"]["basicValidate"] = array();
	$edata["validateAs"]["customMessages"] = array();
		
		
	//	End validation
	
		
				
		
	
		
	$fdata["EditFormats"]["edit"] = $edata;
//	End Edit Formats
	
	
	$fdata["isSeparate"] = false;
	
	
	
	

	

	
	$tdatausers["userPass"] = $fdata;

	
$tables_data["users"]=&$tdatausers;
$field_labels["users"] = &$fieldLabelsusers;
$fieldToolTips["users"] = &$fieldToolTipsusers;
$page_titles["users"] = &$pageTitlesusers;

// -----------------start  prepare master-details data arrays ------------------------------//
// tables which are detail tables for current table (master)
$detailsTablesData["users"] = array();
	
// tables which are master tables for current table (detail)
$masterTablesData["users"] = array();


// -----------------end  prepare master-details data arrays ------------------------------//

require_once(getabspath("classes/sql.php"));










function createSqlQuery_users()
{
$proto0=array();
$proto0["m_strHead"] = "SELECT";
$proto0["m_strFieldList"] = "userId,  	userName,  	userEmail,  	userPass";
$proto0["m_strFrom"] = "FROM users";
$proto0["m_strWhere"] = "";
$proto0["m_strOrderBy"] = "";
$proto0["m_strTail"] = "";
			$proto0["cipherer"] = null;
$proto1=array();
$proto1["m_sql"] = "";
$proto1["m_uniontype"] = "SQLL_UNKNOWN";
	$obj = new SQLNonParsed(array(
	"m_sql" => ""
));

$proto1["m_column"]=$obj;
$proto1["m_contained"] = array();
$proto1["m_strCase"] = "";
$proto1["m_havingmode"] = false;
$proto1["m_inBrackets"] = false;
$proto1["m_useAlias"] = false;
$obj = new SQLLogicalExpr($proto1);

$proto0["m_where"] = $obj;
$proto3=array();
$proto3["m_sql"] = "";
$proto3["m_uniontype"] = "SQLL_UNKNOWN";
	$obj = new SQLNonParsed(array(
	"m_sql" => ""
));

$proto3["m_column"]=$obj;
$proto3["m_contained"] = array();
$proto3["m_strCase"] = "";
$proto3["m_havingmode"] = false;
$proto3["m_inBrackets"] = false;
$proto3["m_useAlias"] = false;
$obj = new SQLLogicalExpr($proto3);

$proto0["m_having"] = $obj;
$proto0["m_fieldlist"] = array();
						$proto5=array();
			$obj = new SQLField(array(
	"m_strName" => "userId",
	"m_strTable" => "users",
	"m_srcTableName" => "users"
));

$proto5["m_sql"] = "userId";
$proto5["m_srcTableName"] = "users";
$proto5["m_expr"]=$obj;
$proto5["m_alias"] = "";
$obj = new SQLFieldListItem($proto5);

$proto0["m_fieldlist"][]=$obj;
						$proto7=array();
			$obj = new SQLField(array(
	"m_strName" => "userName",
	"m_strTable" => "users",
	"m_srcTableName" => "users"
));

$proto7["m_sql"] = "userName";
$proto7["m_srcTableName"] = "users";
$proto7["m_expr"]=$obj;
$proto7["m_alias"] = "";
$obj = new SQLFieldListItem($proto7);

$proto0["m_fieldlist"][]=$obj;
						$proto9=array();
			$obj = new SQLField(array(
	"m_strName" => "userEmail",
	"m_strTable" => "users",
	"m_srcTableName" => "users"
));

$proto9["m_sql"] = "userEmail";
$proto9["m_srcTableName"] = "users";
$proto9["m_expr"]=$obj;
$proto9["m_alias"] = "";
$obj = new SQLFieldListItem($proto9);

$proto0["m_fieldlist"][]=$obj;
						$proto11=array();
			$obj = new SQLField(array(
	"m_strName" => "userPass",
	"m_strTable" => "users",
	"m_srcTableName" => "users"
));

$proto11["m_sql"] = "userPass";
$proto11["m_srcTableName"] = "users";
$proto11["m_expr"]=$obj;
$proto11["m_alias"] = "";
$obj = new SQLFieldListItem($proto11);

$proto0["m_fieldlist"][]=$obj;
$proto0["m_fromlist"] = array();
												$proto13=array();
$proto13["m_link"] = "SQLL_MAIN";
			$proto14=array();
$proto14["m_strName"] = "users";
$proto14["m_srcTableName"] = "users";
$proto14["m_columns"] = array();
$proto14["m_columns"][] = "userId";
$proto14["m_columns"][] = "userName";
$proto14["m_columns"][] = "userEmail";
$proto14["m_columns"][] = "userPass";
$obj = new SQLTable($proto14);

$proto13["m_table"] = $obj;
$proto13["m_sql"] = "users";
$proto13["m_alias"] = "";
$proto13["m_srcTableName"] = "users";
$proto15=array();
$proto15["m_sql"] = "";
$proto15["m_uniontype"] = "SQLL_UNKNOWN";
	$obj = new SQLNonParsed(array(
	"m_sql" => ""
));

$proto15["m_column"]=$obj;
$proto15["m_contained"] = array();
$proto15["m_strCase"] = "";
$proto15["m_havingmode"] = false;
$proto15["m_inBrackets"] = false;
$proto15["m_useAlias"] = false;
$obj = new SQLLogicalExpr($proto15);

$proto13["m_joinon"] = $obj;
$obj = new SQLFromListItem($proto13);

$proto0["m_fromlist"][]=$obj;
$proto0["m_groupby"] = array();
$proto0["m_orderby"] = array();
$proto0["m_srcTableName"]="users";		
$obj = new SQLQuery($proto0);

	return $obj;
}
$queryData_users = createSqlQuery_users();


	
				
	
$tdatausers[".sqlquery"] = $queryData_users;

$tableEvents["users"] = new eventsBase;
$tdatausers[".hasEvents"] = false;

?>