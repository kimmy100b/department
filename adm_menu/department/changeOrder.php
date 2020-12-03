<?php
include $_SERVER['DOCUMENT_ROOT']."/application/default.php";
include_once __BASE_PATH."/function/util_func.php";

// REFERER CHECK
CheckRequest( "A" );
// 권한 체크
Check_Page_Use_Admin();

if ( $_POST['order'] != "" ) 
{
	include __MODULE_PATH."/department/Department.php";
	$department = new Department();	
	$department->changeOrder( $_POST['order'], $_POST['year'] );
}
?>